<?php
session_start();
header('Content-Type: application/json');
require_once '../connect.php';

// ตรวจสอบสิทธิ์ว่าเป็น Designer
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

// ดึงข้อมูลรีวิวจากฐานข้อมูล
$sql = "SELECT r.rating, r.comment, r.review_date, CONCAT(u.first_name, ' ', u.last_name) AS client_name
        FROM reviews r
        JOIN contracts c ON r.contract_id = c.contract_id
        JOIN users u ON r.reviewer_id = u.user_id
        WHERE c.request_id = ? AND r.reviewed_user_id = ? AND r.review_type = 'client_review_designer'";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ii", $request_id, $designer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $review_data = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'data' => $review_data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ยังไม่มีรีวิวสำหรับงานนี้']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูลรีวิว']);
}

$conn->close();
?>