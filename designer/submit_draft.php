<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
require_once '../connect.php'; // ไฟล์นี้สร้างตัวแปร $conn

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['request_id']) || !isset($_FILES['draft_file'])) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลที่ส่งมาไม่ถูกต้อง']);
    exit();
}

$designer_id = $_SESSION['user_id'];
$request_id = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
$draft_file = $_FILES['draft_file'];
$message_to_client = $_POST['message_to_client'] ?? '';

if ($draft_file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์']);
    exit();
}

$max_file_size = 25 * 1024 * 1024; // 25 MB
if ($draft_file['size'] > $max_file_size) {
    echo json_encode(['status' => 'error', 'message' => 'ขนาดไฟล์ต้องไม่เกิน 25MB']);
    exit();
}

$upload_dir = '../uploads/drafts/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$file_extension = strtolower(pathinfo($draft_file['name'], PATHINFO_EXTENSION));
$unique_filename = 'draft_' . $request_id . '_' . time() . '.' . $file_extension;
$destination = $upload_dir . $unique_filename;

// ใช้ตัวแปร $conn ที่ถูกต้อง
$conn->begin_transaction();

try {
    // แก้ไข SQL ให้ตรวจสอบ status = 'assigned' ใน WHERE clause
    $sql_verify = "SELECT c.contract_id, cjr.client_id 
                   FROM contracts c 
                   JOIN client_job_requests cjr ON c.request_id = cjr.request_id 
                   WHERE c.request_id = ? AND c.designer_id = ? AND cjr.status = 'assigned'";
    
    $stmt_verify = $conn->prepare($sql_verify);
    $stmt_verify->bind_param("ii", $request_id, $designer_id);
    $stmt_verify->execute();
    $result_verify = $stmt_verify->get_result();

    if ($result_verify->num_rows === 0) {
        throw new Exception("ไม่พบงานที่กำลังดำเนินการ หรือคุณไม่มีสิทธิ์ส่งงานนี้");
    }
    
    $contract_data = $result_verify->fetch_assoc();
    $contract_id = $contract_data['contract_id'];
    $client_id = $contract_data['client_id'];
    $stmt_verify->close();

    if (!move_uploaded_file($draft_file['tmp_name'], $destination)) {
        throw new Exception("ไม่สามารถบันทึกไฟล์ที่อัปโหลดได้");
    }

    $file_path_for_db = str_replace('../', '', $destination);
    $sql_insert_file = "INSERT INTO uploaded_files (contract_id, uploader_id, file_name, file_path, file_type, file_size, uploaded_date) VALUES (?, ?, ?, ?, 'draft', ?, NOW())";
    $stmt_insert_file = $conn->prepare($sql_insert_file);
    $stmt_insert_file->bind_param("iissi", $contract_id, $designer_id, $draft_file['name'], $file_path_for_db, $draft_file['size']);
    if (!$stmt_insert_file->execute()) {
        throw new Exception("ไม่สามารถบันทึกข้อมูลไฟล์ลงฐานข้อมูลได้");
    }
    $stmt_insert_file->close();

    $new_status = 'draft_submitted';
    $sql_update_req = "UPDATE client_job_requests SET status = ? WHERE request_id = ?";
    $stmt_update_req = $conn->prepare($sql_update_req);
    $stmt_update_req->bind_param("si", $new_status, $request_id);
    if (!$stmt_update_req->execute()) {
        throw new Exception("ไม่สามารถอัปเดตสถานะงานได้");
    }
    $stmt_update_req->close();
    
    $final_message = "นักออกแบบได้ส่งมอบงานฉบับร่างสำหรับโปรเจกต์ของคุณแล้ว กรุณาตรวจสอบและดำเนินการต่อ";
    if (!empty($message_to_client)) {
        $final_message .= "\n\nข้อความจากนักออกแบบ:\n\"" . htmlspecialchars($message_to_client) . "\"";
    }
    $sql_message = "INSERT INTO messages (from_user_id, to_user_id, message, request_id) VALUES (?, ?, ?, ?)";
    $stmt_message = $conn->prepare($sql_message);
    $stmt_message->bind_param("iisi", $designer_id, $client_id, $final_message, $request_id);
    $stmt_message->execute();
    $stmt_message->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'ส่งมอบงานฉบับร่างเรียบร้อยแล้ว']);

} catch (Exception $e) {
    $conn->rollback();
    if (isset($destination) && file_exists($destination)) {
        unlink($destination);
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>