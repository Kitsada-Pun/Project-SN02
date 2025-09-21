<?php
session_start();
require_once '../connect.php';

// ตรวจสอบสิทธิ์การเข้าถึง (ต้องเป็น admin)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// รับค่าจาก URL
$submission_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id_to_update = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($submission_id > 0 && ($action === 'approve' || $action === 'reject')) {

    $conn->begin_transaction(); // เริ่ม Transaction

    try {
        // 1. อัปเดตสถานะในตาราง verification_submissions
        $new_status = ($action === 'approve') ? 'approved' : 'rejected';
        $stmt1 = $conn->prepare("UPDATE verification_submissions SET status = ? WHERE id = ?");
        $stmt1->bind_param("si", $new_status, $submission_id);
        $stmt1->execute();
        $stmt1->close();

        // 2. ถ้าอนุมัติ (approve), ให้อัปเดตตาราง users ด้วย
        if ($action === 'approve' && $user_id_to_update > 0) {
            $stmt2 = $conn->prepare("UPDATE users SET is_verified = 1 WHERE user_id = ?");
            $stmt2->bind_param("i", $user_id_to_update);
            $stmt2->execute();
            $stmt2->close();
        }

        $conn->commit(); // ยืนยันการเปลี่ยนแปลงทั้งหมด
        
        // (ตัวเลือก) สามารถเพิ่มการส่งอีเมลแจ้งเตือนผู้ใช้ได้ที่นี่
        
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback(); // ยกเลิกการเปลี่ยนแปลงทั้งหมดหากมีข้อผิดพลาด
        // สามารถตั้งค่าการแสดงข้อความ Error ได้ที่นี่
    }
}

// กลับไปยังหน้าจัดการ
header("Location: manage_verification_submissions.php");
exit();
?>