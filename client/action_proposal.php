<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
require_once '../connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$client_id = $_SESSION['user_id'];
$application_id = $_POST['application_id'] ?? 0;
$request_id = $_POST['request_id'] ?? 0;
$designer_id = $_POST['designer_id'] ?? 0;
$action = $_POST['action'] ?? '';

if (empty($application_id) || empty($request_id) || empty($action) || empty($designer_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit();
}

$conn->begin_transaction();

try {
    // Verify that the job request belongs to the client
    $sql_verify = "SELECT title, status FROM client_job_requests WHERE request_id = ? AND client_id = ?";
    $stmt_verify = $conn->prepare($sql_verify);
    $stmt_verify->bind_param("ii", $request_id, $client_id);
    $stmt_verify->execute();
    $result_verify = $stmt_verify->get_result();

    if ($result_verify->num_rows === 0) {
        throw new Exception('Job request not found or you do not have permission.');
    }
    $job_request = $result_verify->fetch_assoc();
    $job_title = $job_request['title']; // <-- เพิ่มบรรทัดนี้
    $stmt_verify->close();

    if (!in_array($job_request['status'], ['open', 'proposed'])) {
        throw new Exception('This job has already been processed.');
    }

    if ($action === 'accept') {
        // --- START: MODIFIED AND ADDED CODE ---

        // 1. Get offered_price from the accepted application
        $price_sql = "SELECT offered_price FROM job_applications WHERE application_id = ?";
        $stmt_price = $conn->prepare($price_sql);
        $stmt_price->bind_param("i", $application_id);
        $stmt_price->execute();
        $price_result = $stmt_price->get_result();
        if ($price_result->num_rows === 0) {
            throw new Exception('Application not found.');
        }
        $application_details = $price_result->fetch_assoc();
        $agreed_price = $application_details['offered_price'];
        $stmt_price->close();

        // 2. Update client_job_requests status to 'pending_deposit' 
        $sql_update_job = "UPDATE client_job_requests SET status = 'pending_deposit', designer_id = ? WHERE request_id = ?";
        $stmt_update_job = $conn->prepare($sql_update_job);
        $stmt_update_job->bind_param("ii", $designer_id, $request_id);
        if (!$stmt_update_job->execute()) {
            throw new Exception("Error updating job request: " . $stmt_update_job->error);
        }
        $stmt_update_job->close();

        // 3. Update the accepted job_application status to 'accepted'
        $sql_accept_app = "UPDATE job_applications SET status = 'accepted' WHERE application_id = ?";
        $stmt_accept_app = $conn->prepare($sql_accept_app);
        $stmt_accept_app->bind_param("i", $application_id);
        if (!$stmt_accept_app->execute()) {
            throw new Exception("Error accepting application: " . $stmt_accept_app->error);
        }
        $stmt_accept_app->close();

        // 4. Reject all other applications for this job request
        $sql_reject_others = "UPDATE job_applications SET status = 'rejected' WHERE request_id = ? AND application_id != ?";
        $stmt_reject_others = $conn->prepare($sql_reject_others);
        $stmt_reject_others->bind_param("ii", $request_id, $application_id);
        if (!$stmt_reject_others->execute()) {
            throw new Exception("Error rejecting other applications: " . $stmt_reject_others->error);
        }
        $stmt_reject_others->close();

        // 5. Create a new contract
        $start_date = date("Y-m-d");
        $contract_status = 'pending';
        $payment_status = 'pending';
        $sql_contract = "INSERT INTO contracts (request_id, designer_id, client_id, agreed_price, start_date, contract_status, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_contract = $conn->prepare($sql_contract);
        if (!$stmt_contract) {
            throw new Exception("Prepare failed for contract insertion: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt_contract->bind_param("iiidsss", $request_id, $designer_id, $client_id, $agreed_price, $start_date, $contract_status, $payment_status);
        $stmt_contract->execute();
        $stmt_contract->close();

        // --- END: MODIFIED AND ADDED CODE ---
        // --- START: โค้ดส่วนที่เพิ่มเข้ามาใหม่ ---
        // 6. ส่งข้อความแจ้งเตือนไปยังนักออกแบบ
        $message_content = "ยินดีด้วย! ข้อเสนอของคุณสำหรับงาน '" . htmlspecialchars($job_title) . "' ได้รับการยอมรับแล้ว กรุณารอผู้ว่าจ้างชำระเงินมัดจำเพื่อเริ่มงาน";
        $sql_send_message = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
        $stmt_message = $conn->prepare($sql_send_message);
        if ($stmt_message) {
            $system_sender_id = $client_id; // ให้ผู้ส่งเป็นผู้ว่าจ้าง
            $stmt_message->bind_param("iis", $system_sender_id, $designer_id, $message_content);
            $stmt_message->execute();
            $stmt_message->close();
        }
        // --- END: สิ้นสุดโค้ดส่วนที่เพิ่มเข้ามา ---
        $conn->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'ตอบรับข้อเสนอเรียบร้อยแล้ว! ระบบกำลังนำคุณไปหน้าชำระเงิน',
            'redirectUrl' => 'payment.php?request_id=' . $request_id
        ]);
    } elseif ($action === 'reject') {
        $sql_update_job = "UPDATE client_job_requests SET status = 'cancelled' WHERE request_id = ?";
        $stmt_update_job = $conn->prepare($sql_update_job);
        $stmt_update_job->bind_param("i", $request_id);
        if (!$stmt_update_job->execute()) {
            throw new Exception("Error cancelling job request: " . $stmt_update_job->error);
        }
        $stmt_update_job->close();

        $sql_reject_all = "UPDATE job_applications SET status = 'rejected' WHERE request_id = ?";
        $stmt_reject_all = $conn->prepare($sql_reject_all);
        $stmt_reject_all->bind_param("i", $request_id);
        $stmt_reject_all->execute();
        $stmt_reject_all->close();

        $conn->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'ข้อเสนอถูกปฏิเสธ และงานนี้ถูกยกเลิกแล้ว',
            'redirectUrl' => 'my_requests.php'
        ]);
    } else {
        throw new Exception('Invalid action.');
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
