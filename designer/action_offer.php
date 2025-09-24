<?php
session_start();
require_once '../connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$designer_id = $_SESSION['user_id'];
$request_id = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';
$designer_name = $_SESSION['full_name'] ?? $_SESSION['username']; // ดึงชื่อนักออกแบบจาก Session

// --- ดึงข้อมูลที่จำเป็นสำหรับสร้างข้อความ ---
$job_info = null;
$sql_info = "SELECT title, client_id FROM client_job_requests WHERE request_id = ?";
$stmt_info = $conn->prepare($sql_info);
if ($stmt_info) {
    $stmt_info->bind_param("i", $request_id);
    $stmt_info->execute();
    $result_info = $stmt_info->get_result();
    if ($result_info->num_rows > 0) {
        $job_info = $result_info->fetch_assoc();
    }
    $stmt_info->close();
}

if (!$job_info) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลงาน']);
    exit();
}
$job_title = $job_info['title'];
$client_id = $job_info['client_id'];
// --- สิ้นสุดการดึงข้อมูล ---

if ($request_id === 0 || empty($action)) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ถูกต้อง']);
    exit();
}

$conn->begin_transaction();

try {
    // =================================================================
    // START: ส่วนของโค้ดที่เพิ่มเข้ามาใหม่
    // =================================================================
    if ($action === 'confirm_deposit') {
        // ตรวจสอบสถานะปัจจุบันของงานว่าเป็น 'awaiting_deposit_verification' จริงหรือไม่
        $sql_check = "SELECT status FROM client_job_requests WHERE request_id = ? AND designer_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $request_id, $designer_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows === 0) {
            throw new Exception("ไม่พบงาน หรือคุณไม่มีสิทธิ์จัดการงานนี้");
        }

        $current_status = $result_check->fetch_assoc()['status'];
        if ($current_status !== 'awaiting_deposit_verification') {
            throw new Exception("สถานะของงานไม่ถูกต้อง ไม่สามารถยืนยันการชำระเงินได้");
        }
        $stmt_check->close();

        // อัปเดตสถานะในตาราง client_job_requests เป็น 'assigned' (กำลังดำเนินการ)
        $sql_update_request = "UPDATE client_job_requests SET status = 'assigned' WHERE request_id = ?";
        $stmt_request = $conn->prepare($sql_update_request);
        $stmt_request->bind_param("i", $request_id);
        if (!$stmt_request->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการอัปเดตสถานะงาน");
        }
        $stmt_request->close();

        // อัปเดตสถานะในตาราง contracts เป็น 'active'
        $sql_update_contract = "UPDATE contracts SET contract_status = 'active' WHERE request_id = ?";
        $stmt_contract = $conn->prepare($sql_update_contract);
        $stmt_contract->bind_param("i", $request_id);
        if (!$stmt_contract->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการอัปเดตสถานะสัญญา");
        }
        $stmt_contract->close();

        // อัปเดตสถานะในตาราง transactions เป็น 'completed' (สำหรับมัดจำ)
        $sql_update_transaction = "UPDATE transactions SET status = 'completed' WHERE contract_id = (SELECT contract_id FROM contracts WHERE request_id = ?) AND slip_path IS NOT NULL AND status = 'pending' ORDER BY transaction_date DESC LIMIT 1";
        $stmt_transaction = $conn->prepare($sql_update_transaction);
        $stmt_transaction->bind_param("i", $request_id);
        if (!$stmt_transaction->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการอัปเดตสถานะการชำระเงิน");
        }
        $stmt_transaction->close();
        // --- [เพิ่มส่วนนี้] ส่งข้อความแจ้งเตือน "ยืนยันการชำระเงิน" ---
        $message_content = "นักออกแแบบได้ยืนยันการชำระเงินมัดจำสำหรับงาน '" . htmlspecialchars($job_title) . "' ของคุณเรียบร้อยแล้ว และจะเริ่มดำเนินการทันที";
        $sql_message = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
        $stmt_message = $conn->prepare($sql_message);
        if ($stmt_message) {
            $stmt_message->bind_param("iis", $designer_id, $client_id, $message_content);
            $stmt_message->execute();
            $stmt_message->close();
        }
        // --- สิ้นสุดส่วนที่เพิ่ม ---
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'ยืนยันการชำระเงินมัดจำเรียบร้อย!']);

        // =================================================================
        // END: สิ้นสุดส่วนของโค้ดที่เพิ่มเข้ามา
        // =================================================================
    } elseif ($action === 'reject_offer') {
        // 1. อัปเดตสถานะในตาราง job_applications ให้เป็น 'rejected'
        $sql_app = "UPDATE job_applications SET status = 'rejected' WHERE request_id = ? AND designer_id = ?";
        $stmt_app = $conn->prepare($sql_app);
        if (!$stmt_app) {
            throw new Exception("SQL prepare failed (applications): " . $conn->error);
        }
        $stmt_app->bind_param("ii", $request_id, $designer_id);
        $stmt_app->execute();
        $affected_app = $stmt_app->affected_rows;
        $stmt_app->close();

        // 2. [สำคัญ] อัปเดตสถานะในตาราง client_job_requests ให้เป็น 'rejected' ด้วย
        $sql_req = "UPDATE client_job_requests SET status = 'rejected' WHERE request_id = ?";
        $stmt_req = $conn->prepare($sql_req);
        if (!$stmt_req) {
            throw new Exception("SQL prepare failed (requests): " . $conn->error);
        }
        $stmt_req->bind_param("i", $request_id);
        $stmt_req->execute();
        $affected_req = $stmt_req->affected_rows;
        $stmt_req->close();
        // --- [เพิ่มส่วนนี้] ส่งข้อความแจ้งเตือน "ปฏิเสธข้อเสนอ" ---
        $message_content = "สวัสดีครับ ผมต้องขออภัยที่ไม่สามารถรับข้อเสนองาน '" . htmlspecialchars($job_title) . "' ได้ในขณะนี้ครับ เนื่องจาก... (สามารถระบุเหตุผลเพิ่มเติมได้)";
        $sql_message = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
        $stmt_message = $conn->prepare($sql_message);
        if ($stmt_message) {
            $stmt_message->bind_param("iis", $designer_id, $client_id, $message_content);
            $stmt_message->execute();
            $stmt_message->close();
        }
        // --- สิ้นสุดส่วนที่เพิ่ม ---
        if ($affected_app > 0 || $affected_req > 0) {
            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'คุณได้ปฏิเสธข้อเสนองานเรียบร้อยแล้ว']);
        } else {
            throw new Exception("ไม่สามารถปฏิเสธข้อเสนองานได้ อาจเป็นเพราะงานนี้ไม่มีอยู่แล้ว หรือถูกจัดการไปแล้ว");
        }
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
