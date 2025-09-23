<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

require_once '../connect.php';

$client_id = $_SESSION['user_id'];
$request_id = $_POST['request_id'] ?? 0;
$amount = $_POST['amount'] ?? 0;

if (empty($request_id) || empty($amount) || !isset($_FILES['payment_slip'])) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit();
}

// ดึงข้อมูล contract_id และ designer_id
$sql_contract = "
    SELECT con.contract_id, cjr.designer_id 
    FROM contracts con
    JOIN client_job_requests cjr ON con.request_id = cjr.request_id
    WHERE con.request_id = ? AND cjr.client_id = ?
";
$stmt_contract = $conn->prepare($sql_contract);
$stmt_contract->bind_param("ii", $request_id, $client_id);
$stmt_contract->execute();
$result_contract = $stmt_contract->get_result();

if ($result_contract->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลสัญญาสำหรับงานนี้']);
    exit();
}
$contract_data = $result_contract->fetch_assoc();
$contract_id = $contract_data['contract_id'];
$designer_id = $contract_data['designer_id'];
$stmt_contract->close();


// จัดการการอัปโหลดไฟล์สลิป
$upload_dir = '../uploads/payment_slips/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
$file_extension = pathinfo($_FILES['payment_slip']['name'], PATHINFO_EXTENSION);
$slip_filename = 'final_slip_' . $request_id . '_' . time() . '.' . $file_extension;
$target_file = $upload_dir . $slip_filename;

if (!move_uploaded_file($_FILES['payment_slip']['tmp_name'], $target_file)) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถอัปโหลดไฟล์ได้']);
    exit();
}

// เริ่ม Transaction
$conn->begin_transaction();
try {
    // 1. เพิ่มข้อมูลในตาราง transactions
    $sql_trans = "INSERT INTO transactions (contract_id, payer_id, payee_id, amount, payment_method, status, slip_path) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_trans = $conn->prepare($sql_trans);
    $payment_method = 'Bank Transfer';
    $status = 'pending'; 
    $stmt_trans->bind_param("iiidsss", $contract_id, $client_id, $designer_id, $amount, $payment_method, $status, $target_file);
    $stmt_trans->execute();

    // 2. อัปเดตสถานะใน client_job_requests
    $new_status = 'final_payment_verification';
    $sql_update = "UPDATE client_job_requests SET status = ? WHERE request_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $new_status, $request_id);
    $stmt_update->execute();
    
    // 3. (Optional) ส่งข้อความแจ้งเตือนนักออกแบบ
    $message = "ผู้ว่าจ้างได้ชำระเงินส่วนที่เหลือแล้ว กรุณาตรวจสอบและส่งมอบไฟล์งานสุดท้าย";
    $sql_msg = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
    $stmt_msg = $conn->prepare($sql_msg);
    $stmt_msg->bind_param("iis", $client_id, $designer_id, $message);
    $stmt_msg->execute();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'อัปโหลดหลักฐานเรียบร้อยแล้ว! กรุณารอนักออกแบบตรวจสอบ']);

} catch (Exception $e) {
    $conn->rollback();
    // ลบไฟล์ถ้าเกิดข้อผิดพลาด
    if (file_exists($target_file)) {
        unlink($target_file);
    }
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}

$stmt_trans->close();
$stmt_update->close();
$stmt_msg->close();
$conn->close();

?>