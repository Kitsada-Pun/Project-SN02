<?php
session_start();
header('Content-Type: application/json');
require_once '../connect.php';

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

if (!isset($_GET['request_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Request ID is missing.']);
    exit();
}

$request_id = $_GET['request_id'];
$designer_id = $_SESSION['user_id'];

// ดึง attachment_path ของงานที่ completed แล้วเท่านั้น
$sql = "SELECT attachment_path FROM client_job_requests WHERE request_id = ? AND designer_id = ? AND status = 'completed'";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ii", $request_id, $designer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // สร้าง URL ที่ถูกต้องสำหรับดาวน์โหลด
        $filePath = str_replace('../', '', $row['attachment_path']);
        echo json_encode(['status' => 'success', 'filePath' => '../' . $filePath]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์ หรือคุณไม่มีสิทธิ์เข้าถึงไฟล์นี้']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL']);
}

$conn->close();
?>