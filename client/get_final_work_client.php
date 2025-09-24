<?php
session_start();
header('Content-Type: application/json');
require_once '../connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

if (!isset($_GET['request_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Request ID is missing.']);
    exit();
}

$request_id = $_GET['request_id'];
$client_id = $_SESSION['user_id'];

$sql = "SELECT attachment_path FROM client_job_requests WHERE request_id = ? AND client_id = ? AND status = 'completed'";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ii", $request_id, $client_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $filePath = str_replace('../', '', $row['attachment_path']);
        echo json_encode(['status' => 'success', 'filePath' => '../' . $filePath]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์ หรือคุณไม่มีสิทธิ์เข้าถึงไฟล์นี้']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'SQL Error']);
}

$conn->close();
?>