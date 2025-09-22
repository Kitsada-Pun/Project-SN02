<?php
session_start();
header('Content-Type: application/json');
require_once '../connect.php';

// ตรวจสอบว่าเป็น Admin หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']);
    exit();
}

// ตรวจสอบว่าเป็น POST request และมีข้อมูลที่จำเป็นครบถ้วนหรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submission_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
    exit();
}

$submission_id = (int)$_POST['submission_id'];
$status = $_POST['status']; // 'approved' or 'rejected'

// ตรวจสอบว่า status ที่ส่งมาถูกต้อง
if (!in_array($status, ['approved', 'rejected'])) {
    echo json_encode(['success' => false, 'message' => 'สถานะไม่ถูกต้อง']);
    exit();
}

$conn->begin_transaction();

try {
    // Step 1: ดึง user_id จาก submission_id เพื่อใช้อัปเดตตาราง users
    $sql_get_user = "SELECT user_id FROM verification_submissions WHERE id = ?";
    $stmt_get_user = $conn->prepare($sql_get_user);
    $stmt_get_user->bind_param("i", $submission_id);
    $stmt_get_user->execute();
    $result_user = $stmt_get_user->get_result();

    if ($result_user->num_rows === 0) {
        throw new Exception("ไม่พบคำขอยืนยันตัวตนนี้");
    }
    $user_data = $result_user->fetch_assoc();
    $user_id = $user_data['user_id'];
    $stmt_get_user->close();

    // Step 2: อัปเดตตาราง verification_submissions
    $sql_update_submission = "UPDATE verification_submissions SET status = ? WHERE id = ?";
    $stmt_update_submission = $conn->prepare($sql_update_submission);
    $stmt_update_submission->bind_param("si", $status, $submission_id);
    if (!$stmt_update_submission->execute()) {
        throw new Exception("ไม่สามารถอัปเดตสถานะคำขอได้: " . $stmt_update_submission->error);
    }
    $stmt_update_submission->close();

    // Step 3: ถ้าอนุมัติ (approved) ให้อัปเดตตาราง users ด้วย
    if ($status === 'approved') {
        $sql_update_user = "UPDATE users SET is_verified = 1 WHERE user_id = ?";
        $stmt_update_user = $conn->prepare($sql_update_user);
        $stmt_update_user->bind_param("i", $user_id);
        if (!$stmt_update_user->execute()) {
            throw new Exception("ไม่สามารถอัปเดตสถานะผู้ใช้ได้: " . $stmt_update_user->error);
        }
        $stmt_update_user->close();
    }

    // ถ้าทุกอย่างสำเร็จ
    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // หากเกิดข้อผิดพลาด ให้ยกเลิกการเปลี่ยนแปลงทั้งหมด
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>