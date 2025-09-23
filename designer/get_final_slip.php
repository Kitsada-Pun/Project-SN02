<?php
session_start();
header('Content-Type: application/json');
require_once '../connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$request_id = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;
$designer_id = $_SESSION['user_id'];

if ($request_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request ID.']);
    exit();
}

// 1. หา contract_id จาก request_id
$contract_sql = "SELECT contract_id FROM contracts WHERE request_id = ? AND designer_id = ?";
$stmt_contract = $conn->prepare($contract_sql);
$stmt_contract->bind_param("ii", $request_id, $designer_id);
$stmt_contract->execute();
$contract_result = $stmt_contract->get_result();

if ($contract_result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบสัญญาจ้างสำหรับงานนี้']);
    exit();
}
$contract_id = $contract_result->fetch_assoc()['contract_id'];
$stmt_contract->close();


// 2. หาสลิปการชำระเงินล่าสุด (ถือว่าเป็นยอดคงเหลือ) จาก contract_id
$slip_sql = "SELECT slip_path FROM transactions WHERE contract_id = ? ORDER BY transaction_date DESC LIMIT 1";
$stmt_slip = $conn->prepare($slip_sql);
$stmt_slip->bind_param("i", $contract_id);
$stmt_slip->execute();
$slip_result = $stmt_slip->get_result();

if ($slip_result->num_rows > 0) {
    $row = $slip_result->fetch_assoc();
    $filePath = $row['slip_path'];

    if (!empty($filePath)) {
        // สร้าง URL ที่ถูกต้องเพื่อให้ browser แสดงรูปได้
        $displayPath = '../' . ltrim($filePath, './');
        echo json_encode(['status' => 'success', 'filePath' => $displayPath]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์สลิปสำหรับรายการนี้']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลการชำระเงิน']);
}

$stmt_slip->close();
$conn->close();
?>