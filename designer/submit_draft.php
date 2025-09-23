<?php
session_start();
header('Content-Type: application/json');

// ตรวจสอบการล็อกอินและประเภทผู้ใช้
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์เข้าถึง']);
    exit();
}

require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'] ?? null;
    $designer_id = $_SESSION['user_id'];
    $draft_message = $_POST['draft_message'] ?? '';

    if (empty($request_id)) {
        echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit();
    }

    // ตรวจสอบว่าเป็นเจ้าของงานจริงหรือไม่
    $sql_check = "SELECT client_id FROM client_job_requests WHERE request_id = ? AND designer_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $request_id, $designer_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบโปรเจกต์ หรือคุณไม่ใช่เจ้าของงาน']);
        exit();
    }
     $client_info = $result_check->fetch_assoc();
     $client_id = $client_info['client_id'];


    // จัดการการอัปโหลดไฟล์
    $file_path = null;
    if (isset($_FILES['draft_file']) && $_FILES['draft_file']['error'] === 0) {
        $upload_dir = '../uploads/draft_files/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = uniqid('draft_' . $request_id . '_') . '_' . basename($_FILES['draft_file']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['draft_file']['tmp_name'], $target_file)) {
            $file_path = $target_file;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถอัปโหลดไฟล์ได้']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'กรุณาแนบไฟล์งานฉบับร่าง']);
        exit();
    }

    // อัปเดตฐานข้อมูล
    $conn->begin_transaction();
    try {
        // 1. อัปเดตสถานะของ client_job_requests
        $new_status = 'draft_submitted';
        $sql_update = "UPDATE client_job_requests SET status = ?, attachment_path = ? WHERE request_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $new_status, $file_path, $request_id);
        $stmt_update->execute();

        // 2. (ตัวเลือก) ส่งข้อความแจ้งเตือนไปยังผู้ว่าจ้าง
        if (!empty($draft_message)) {
             $message_to_send = "ได้ส่งมอบงานฉบับร่างสำหรับโปรเจกต์ของคุณแล้ว กรุณาตรวจสอบ <br>ข้อความจากนักออกแบบ: " . htmlspecialchars($draft_message);
            $sql_message = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
            $stmt_message = $conn->prepare($sql_message);
            $stmt_message->bind_param("iis", $designer_id, $client_id, $message_to_send);
            $stmt_message->execute();
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'ส่งมอบงานฉบับร่างเรียบร้อยแล้ว!']);

    } catch (Exception $e) {
        $conn->rollback();
        // หากมีข้อผิดพลาด ให้ลบไฟล์ที่อัปโหลดไปแล้ว
        if ($file_path && file_exists($file_path)) {
            unlink($file_path);
        }
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage()]);
    }

    $stmt_check->close();
    if(isset($stmt_update)) $stmt_update->close();
    if(isset($stmt_message)) $stmt_message->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>