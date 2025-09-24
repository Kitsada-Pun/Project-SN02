<?php
session_start();
header('Content-Type: application/json');

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// รับข้อมูลจากฟอร์ม
$contract_id = $_POST['contract_id'] ?? null;
$reviewer_id = $_SESSION['user_id'];
$reviewed_user_id = $_POST['designer_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$comment = $_POST['comment'] ?? '';
$job_title = $_POST['job_title'] ?? 'ไม่มีชื่อ'; // รับชื่องาน

// ตรวจสอบข้อมูล
if (empty($contract_id) || empty($reviewed_user_id) || empty($rating)) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit();
}

$conn->begin_transaction();

try {
    // ตรวจสอบว่าเคยรีวิวไปแล้วหรือยัง
    $sql_check = "SELECT review_id FROM reviews WHERE contract_id = ? AND reviewer_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $contract_id, $reviewer_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows > 0) {
        throw new Exception('คุณได้ทำการรีวิวงานนี้ไปแล้ว');
    }
    $stmt_check->close();

    // 1. บันทึกรีวิวลงในฐานข้อมูล
    $sql_insert = "INSERT INTO reviews (contract_id, reviewer_id, reviewed_user_id, rating, comment, review_type) VALUES (?, ?, ?, ?, ?, 'client_review_designer')";
    $stmt_insert = $conn->prepare($sql_insert);
    if (!$stmt_insert) throw new Exception("Prepare failed (reviews): " . $conn->error);

    $stmt_insert->bind_param("iiids", $contract_id, $reviewer_id, $reviewed_user_id, $rating, $comment);
    if (!$stmt_insert->execute()) throw new Exception("Execute failed (reviews): " . $stmt_insert->error);
    $stmt_insert->close();

    // 2. ส่งข้อความแจ้งเตือนไปยังนักออกแบบ
    $message_to_designer = "<b>โปรเจกต์เสร็จสมบูรณ์!</b><br>ผู้ว่าจ้างได้ให้คะแนนและรีวิวสำหรับงาน '" . htmlspecialchars($job_title) . "' เรียบร้อยแล้ว ขอขอบคุณสำหรับผลงานที่ยอดเยี่ยม!";

    $sql_message = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
    $stmt_message = $conn->prepare($sql_message);
    if (!$stmt_message) throw new Exception("Prepare failed (messages): " . $conn->error);

    $stmt_message->bind_param("iis", $reviewer_id, $reviewed_user_id, $message_to_designer);
    if (!$stmt_message->execute()) throw new Exception("Execute failed (messages): " . $stmt_message->error);
    $stmt_message->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'ขอบคุณสำหรับรีวิวของคุณ!']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>