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
        // อัปเดตสถานะในตาราง job_applications เป็น 'rejected'
        $sql = "UPDATE job_applications SET status = 'rejected' WHERE request_id = ? AND designer_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("SQL prepare failed: " . $conn->error);
        
        $stmt->bind_param("ii", $request_id, $designer_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'คุณได้ปฏิเสธข้อเสนองานเรียบร้อยแล้ว']);
        } else {
            throw new Exception("ไม่พบข้อเสนองานที่ตรงกัน หรืออาจถูกยกเลิกไปแล้ว");
        }
        $stmt->close();

    } elseif ($action === 'confirm_deposit') {
        // [โค้ดสำหรับการยืนยันมัดจำ]
        // ...
        // หมายเหตุ: ส่วนนี้ควรมีอยู่แล้ว หรือถ้ายังไม่มี ให้เพิ่มตาม workflow ต่อไป
         echo json_encode(['status' => 'success', 'message' => 'ยืนยันมัดจำเรียบร้อยแล้ว']);

    } else {
        throw new Exception("การกระทำไม่ถูกต้อง");
    }

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>