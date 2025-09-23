<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

require_once '../connect.php';

$client_id = $_SESSION['user_id'];
$request_id = $_POST['request_id'] ?? 0;
$action = $_POST['action'] ?? '';

if (empty($request_id) || empty($action)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data provided']);
    exit();
}

// ตรวจสอบว่าผู้ใช้เป็นเจ้าของงานจริงหรือไม่
$sql_verify = "SELECT designer_id, revision_count FROM client_job_requests WHERE request_id = ? AND client_id = ?";
$stmt_verify = $conn->prepare($sql_verify);
$stmt_verify->bind_param("ii", $request_id, $client_id);
$stmt_verify->execute();
$result_verify = $stmt_verify->get_result();

if ($result_verify->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Job request not found or you do not own it.']);
    exit();
}
$job_data = $result_verify->fetch_assoc();
$designer_id = $job_data['designer_id'];
$stmt_verify->close();

$conn->begin_transaction();

try {
    if ($action === 'accept_draft') {
        // ผู้ว่าจ้างยอมรับงาน -> เปลี่ยนสถานะเป็น awaiting_final_payment
        $new_status = 'awaiting_final_payment';
        $sql_update = "UPDATE client_job_requests SET status = ? WHERE request_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $request_id);
        $stmt_update->execute();

        // แจ้งเตือนนักออกแบบ (Optional)
        $message = "ผู้ว่าจ้างได้ยอมรับงานฉบับร่างแล้ว และกำลังจะชำระเงินส่วนที่เหลือ";
        $sql_msg = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
        $stmt_msg = $conn->prepare($sql_msg);
        $stmt_msg->bind_param("iis", $client_id, $designer_id, $message);
        $stmt_msg->execute();

        $conn->commit();
        echo json_encode(['status' => 'success']);
    } elseif ($action === 'request_revision') {
        // ตรวจสอบว่ายังมีสิทธิ์แก้ไขเหลือหรือไม่
        if ($job_data['revision_count'] >= 2) {
            echo json_encode(['status' => 'error', 'message' => 'คุณใช้สิทธิ์ในการแก้ไขงานครบแล้ว']);
            exit();
        }

        // ผู้ว่าจ้างขอแก้ไข -> เปลี่ยนสถานะกลับไปเป็น assigned และเพิ่ม revision_count
        $revision_message = $_POST['message'] ?? 'ไม่มีข้อความระบุ';
        $new_status = 'assigned'; // สถานะ 'กำลังดำเนินการ'

        $sql_update = "UPDATE client_job_requests SET status = ?, revision_count = revision_count + 1 WHERE request_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $request_id);
        $stmt_update->execute();

        // ส่งข้อความแจ้งให้นักออกแบบทราบ
        $message_to_designer = "ผู้ว่าจ้างขอแก้ไขงาน: \"" . htmlspecialchars($revision_message) . "\"กรุณาตรวจสอบและดำเนินการแก้ไข";
        $sql_msg = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
        $stmt_msg = $conn->prepare($sql_msg);
        $stmt_msg->bind_param("iis", $client_id, $designer_id, $message_to_designer);
        $stmt_msg->execute();

        $conn->commit();
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception("Invalid action.");
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
