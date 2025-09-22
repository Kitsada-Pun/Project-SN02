<?php
session_start();
require_once '../connect.php';

header('Content-Type: application/json');

// ตรวจสอบว่าเป็น designer ที่ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$designer_id = $_SESSION['user_id'];
$request_id = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;

if ($request_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request ID.']);
    exit();
}

// ค้นหา slip_path ล่าสุดจากตาราง transactions ที่เกี่ยวข้องกับ request_id นี้
// และตรวจสอบให้แน่ใจว่า designer คนนี้เป็นเจ้าของงานนั้นจริงๆ
$sql = "
    SELECT t.slip_path 
    FROM transactions t
    JOIN contracts con ON t.contract_id = con.contract_id
    WHERE con.request_id = ? AND con.designer_id = ?
    ORDER BY t.transaction_date DESC 
    LIMIT 1
";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ii", $request_id, $designer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['slip_path'])) {
            // สร้าง Path ที่ถูกต้องสำหรับแสดงผล (จากโฟลเดอร์ designer/ ต้องถอยกลับไป 1 ระดับ)
            $filePath = '../' . ltrim($row['slip_path'], './');
            echo json_encode(['status' => 'success', 'filePath' => $filePath]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์สลิปสำหรับงานนี้']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลการชำระเงิน']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database query failed.']);
}

$conn->close();
?>