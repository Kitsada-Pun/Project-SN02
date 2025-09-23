<?php
session_start();
require_once '../connect.php';
header('Content-Type: application/json');

// ตรวจสอบสิทธิ์ผู้ใช้
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$designer_id = $_SESSION['user_id'];
$request_id = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;

// ตรวจสอบข้อมูลเบื้องต้น
if ($request_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request ID.']);
    exit();
}

if (!isset($_FILES['final_work_file']) || $_FILES['final_work_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาแนบไฟล์งานฉบับสมบูรณ์']);
    exit();
}

// จัดการการอัปโหลดไฟล์
$upload_dir = '../uploads/final_files/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$file = $_FILES['final_work_file'];
$file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$new_filename = 'final_' . $request_id . '_' . uniqid() . '.' . $file_ext;
$target_path = $upload_dir . $new_filename;
$db_path = 'uploads/final_files/' . $new_filename;

if (!move_uploaded_file($file['tmp_name'], $target_path)) {
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์']);
    exit();
}

// เริ่ม Transaction
$conn->begin_transaction();

try {
    // ดึง contract_id และ client_id จาก request_id
    $contract_id = null;
    $client_id = null;
    $stmt_get_ids = $conn->prepare("SELECT contract_id, client_id FROM contracts WHERE request_id = ?");
    $stmt_get_ids->bind_param("i", $request_id);
    $stmt_get_ids->execute();
    $result_ids = $stmt_get_ids->get_result();
    if ($row_ids = $result_ids->fetch_assoc()) {
        $contract_id = $row_ids['contract_id'];
        $client_id = $row_ids['client_id'];
    }
    $stmt_get_ids->close();

    if (!$contract_id || !$client_id) {
        throw new Exception("ไม่พบสัญญาสำหรับงานนี้");
    }

    // 1. อัปเดต client_job_requests เป็น 'completed'
    $stmt_req = $conn->prepare("UPDATE client_job_requests SET status = 'completed' WHERE request_id = ? AND designer_id = ?");
    $stmt_req->bind_param("ii", $request_id, $designer_id);
    $stmt_req->execute();
    if ($stmt_req->affected_rows === 0) throw new Exception("ไม่สามารถอัปเดตสถานะงานหลักได้");
    $stmt_req->close();

    // 2. อัปเดต contracts เป็น 'completed'
    $stmt_con = $conn->prepare("UPDATE contracts SET contract_status = 'completed', end_date = NOW() WHERE request_id = ?");
    $stmt_con->bind_param("i", $request_id);
    $stmt_con->execute();
    $stmt_con->close();

    // 3. บันทึกข้อมูลไฟล์ลงในตาราง uploaded_files
    $stmt_file = $conn->prepare("INSERT INTO uploaded_files (contract_id, uploader_id, file_name, file_path, file_type, uploaded_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt_file->bind_param("isss", $contract_id, $designer_id, $file['name'], $db_path, $file['type']);
    $stmt_file->execute();
    $stmt_file->close();

    // 4. ส่งข้อความแจ้งเตือนผู้ว่าจ้าง
    $message_text = "นักออกแบบได้ส่งมอบไฟล์งานฉบับสมบูรณ์สำหรับงาน '#" . $request_id . "' แล้ว กรุณาตรวจสอบและให้คะแนน";
    $msg_stmt = $conn->prepare("INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)");
    $msg_stmt->bind_param("iis", $designer_id, $client_id, $message_text);
    $msg_stmt->execute();
    $msg_stmt->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'ยืนยันการชำระเงินและส่งมอบไฟล์งานสำเร็จ!']);

} catch (Exception $e) {
    $conn->rollback();
    if (file_exists($target_path)) {
        unlink($target_path);
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>