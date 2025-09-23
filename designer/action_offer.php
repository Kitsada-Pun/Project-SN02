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

if ($request_id === 0 || empty($action)) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ถูกต้อง']);
    exit();
}

$conn->begin_transaction();

try {
    if ($action === 'reject_offer') {
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
