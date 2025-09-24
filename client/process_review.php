<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_SESSION['user_id'];
    $contract_id = $_POST['contract_id'];
    $designer_id = $_POST['designer_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // ป้องกันการรีวิวซ้ำ
    $sql_check = "SELECT review_id FROM reviews WHERE contract_id = ? AND reviewer_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $contract_id, $client_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "คุณได้รีวิวงานนี้ไปแล้ว";
        // อาจจะ redirect กลับพร้อมกับ error message
        header("Location: my_requests.php?error=already_reviewed");
        exit();
    }
    $stmt_check->close();

    // เพิ่มรีวิวใหม่
    $sql_insert = "INSERT INTO reviews (contract_id, reviewer_id, reviewed_user_id, rating, comment, review_type) VALUES (?, ?, ?, ?, ?, 'client_review_designer')";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iiids", $contract_id, $client_id, $designer_id, $rating, $comment);

    if ($stmt_insert->execute()) {
        // สำเร็จ: กลับไปหน้า my_requests
        header("Location: my_requests.php?review=success");
    } else {
        // ผิดพลาด
        echo "เกิดข้อผิดพลาดในการบันทึกรีวิว: " . $conn->error;
    }
    $stmt_insert->close();
}

$conn->close();
?>