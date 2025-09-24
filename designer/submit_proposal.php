<?php
session_start();
header('Content-Type: application/json'); // <-- เพิ่มบรรทัดนี้

// ตรวจสอบว่าใช่นักออกแบบที่ login อยู่หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit();
}

require_once '../connect.php';

$response = ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดไม่ทราบสาเหตุ'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $request_id = $_POST['request_id'] ?? null;
    $client_id = $_POST['client_id'] ?? null;
    $designer_id = $_SESSION['user_id'];
    $proposal_text = $_POST['proposal_text'] ?? '';
    $offered_price = $_POST['offered_price'] ?? null;

    if (!$request_id || !$client_id || !$offered_price) {
        $response['message'] = 'ข้อมูลไม่ครบถ้วน';
        echo json_encode($response);
        exit();
    }

    // --- 1. เพิ่มข้อมูลใบเสนอราคาลงในตาราง job_applications ---
    $sql_insert_app = "INSERT INTO job_applications (request_id, designer_id, client_id, proposal_text, offered_price, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert_app);
    $status_app = 'pending';
    $stmt_insert->bind_param("iiisds", $request_id, $designer_id, $client_id, $proposal_text, $offered_price, $status_app);

    // --- 2. อัปเดตสถานะของคำขอจ้างงานในตาราง client_job_requests ---
    $sql_update_req = "UPDATE client_job_requests SET status = ? WHERE request_id = ?";
    $stmt_update = $conn->prepare($sql_update_req);
    $status_req = 'proposed';
    $stmt_update->bind_param("si", $status_req, $request_id);

    $conn->begin_transaction();

    try {
        $stmt_insert->execute();
        $stmt_update->execute();
        // --- [เพิ่มส่วนนี้] 3. ส่งข้อความแจ้งเตือนไปยังผู้ว่าจ้าง ---
        $designer_name = $_SESSION['full_name'] ?? $_SESSION['username'];

        // ดึงชื่องานเพื่อใช้ในข้อความ
        $job_title = '';
        $sql_get_title = "SELECT title FROM client_job_requests WHERE request_id = ?";
        $stmt_get_title = $conn->prepare($sql_get_title);
        if ($stmt_get_title) {
            $stmt_get_title->bind_param("i", $request_id);
            if ($stmt_get_title->execute()) {
                $result_title = $stmt_get_title->get_result();
                if ($row = $result_title->fetch_assoc()) {
                    $job_title = $row['title'];
                }
            }
            $stmt_get_title->close();
        }

        // สร้างข้อความแจ้งเตือน
        $message_content = "นักออกแบบได้ส่งใบเสนอราคาสำหรับงาน '". htmlspecialchars($job_title) . "' ให้คุณพิจารณาแล้ว\nคุณสามารถตรวจสอบข้อเสนอได้ที่หน้า 'คำขอจ้างงานของฉัน' ในแท็บ 'รอพิจารณา'";

        $sql_send_message = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
        $stmt_send_message = $conn->prepare($sql_send_message);
        if ($stmt_send_message) {
            $stmt_send_message->bind_param("iis", $designer_id, $client_id, $message_content);
            $stmt_send_message->execute();
            $stmt_send_message->close();
        }
        // --- สิ้นสุดส่วนที่เพิ่มเข้ามา ---
        $conn->commit();
        $response = ['status' => 'success', 'message' => 'ยื่นใบเสนอราคาสำเร็จแล้ว!'];
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $response['message'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $exception->getMessage();
    } finally {
        $stmt_insert->close();
        $stmt_update->close();
        $conn->close();
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response); // <-- ส่งผลลัพธ์กลับเป็น JSON
exit();
