<?php
session_start();
header('Content-Type: application/json');
require_once '../connect.php';

// ตรวจสอบการล็อกอินและประเภทผู้ใช้
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$request_id = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;
$designer_id = $_SESSION['user_id'];

if ($request_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request ID.']);
    exit();
}

// ดึงข้อมูล path ของไฟล์ฉบับร่างจากตาราง client_job_requests
$sql = "SELECT attachment_path FROM client_job_requests WHERE request_id = ? AND designer_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ii", $request_id, $designer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = $row['attachment_path'];

        if (!empty($filePath)) {
            // สร้าง URL ที่ถูกต้องสำหรับแสดงไฟล์
            $displayPath = '../' . ltrim($filePath, '.');
            echo json_encode(['status' => 'success', 'filePath' => $displayPath]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์ฉบับร่างสำหรับงานนี้']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลงาน หรือคุณไม่มีสิทธิ์เข้าถึง']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database query failed.']);
}

$conn->close();
?>