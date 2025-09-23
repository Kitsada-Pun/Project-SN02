<?php
session_start();
header('Content-Type: application/json');
require_once '../connect.php'; // ตรวจสอบว่า path ไปยัง connect.php ถูกต้อง

// --- ตรวจสอบสิทธิ์การเข้าถึง ---
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้']);
    exit();
}

// --- ตรวจสอบว่าเป็น Method POST และมีข้อมูลที่จำเป็นครบถ้วน ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

if (!isset($_POST['request_id']) || !isset($_FILES['final_work_file'])) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit();
}

$request_id = $_POST['request_id'];
$designer_id = $_SESSION['user_id'];
$file = $_FILES['final_work_file'];

// --- จัดการการอัปโหลดไฟล์ ---
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์']);
    exit();
}

// --- ตั้งค่าโฟลเดอร์สำหรับเก็บไฟล์งานสุดท้าย ---
$upload_dir = '../uploads/final_files/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // สร้างโฟลเดอร์หากยังไม่มี
}

$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
// สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกัน เพื่อป้องกันการเขียนทับ
$new_file_name = 'final_' . $request_id . '_' . uniqid() . '.' . $file_extension;
$file_path = $upload_dir . $new_file_name;
$relative_path = '../' . $file_path; // Path ที่จะเก็บในฐานข้อมูล

// --- ย้ายไฟล์ไปยังโฟลเดอร์ที่กำหนด ---
if (!move_uploaded_file($file['tmp_name'], $file_path)) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถบันทึกไฟล์ได้']);
    exit();
}

// --- เริ่ม Transaction สำหรับการอัปเดตฐานข้อมูล ---
$conn->begin_transaction();

try {
    // 1. อัปเดตตาราง client_job_requests
    $sql_update_request = "UPDATE client_job_requests SET status = 'completed', attachment_path = ? WHERE request_id = ? AND designer_id = ?";
    $stmt_request = $conn->prepare($sql_update_request);
    if (!$stmt_request) {
        throw new Exception("SQL Error (requests): " . $conn->error);
    }
    $stmt_request->bind_param("sii", $relative_path, $request_id, $designer_id);
    $stmt_request->execute();

    // 2. อัปเดตตาราง contracts
    $current_date = date("Y-m-d");
    $sql_update_contract = "UPDATE contracts SET contract_status = 'completed', end_date = ? WHERE request_id = ?";
    $stmt_contract = $conn->prepare($sql_update_contract);
    if (!$stmt_contract) {
        throw new Exception("SQL Error (contracts): " . $conn->error);
    }
    $stmt_contract->bind_param("si", $current_date, $request_id);
    $stmt_contract->execute();

    // --- ถ้าทุกอย่างสำเร็จ ---
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'ส่งมอบงานฉบับสมบูรณ์เรียบร้อยแล้ว!']);

} catch (Exception $e) {
    // --- หากเกิดข้อผิดพลาด ให้ยกเลิกการเปลี่ยนแปลงทั้งหมด ---
    $conn->rollback();
    // ลบไฟล์ที่อัปโหลดไปแล้ว หากการบันทึกข้อมูลล้มเหลว
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    error_log($e->getMessage()); // บันทึก error ไว้ดู
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage()]);
}

$stmt_request->close();
$stmt_contract->close();
$conn->close();
?>