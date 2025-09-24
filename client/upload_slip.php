<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
require_once '../connect.php';

// ตรวจสอบว่าเป็น Client และ Login อยู่หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

// ตรวจสอบว่าเป็นการส่งข้อมูลแบบ POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $amount = $_POST['amount'];
    $client_id = $_SESSION['user_id'];

    // ดึงข้อมูล contract_id และ designer_id เพื่อใช้ในการบันทึก
    $sql_contract = "SELECT contract_id, designer_id FROM contracts WHERE request_id = ? AND client_id = ?";
    $stmt_contract = $conn->prepare($sql_contract);
    $stmt_contract->bind_param("ii", $request_id, $client_id);
    $stmt_contract->execute();
    $result_contract = $stmt_contract->get_result();
    $contract_info = $result_contract->fetch_assoc();

    if (!$contract_info) {
        die("เกิดข้อผิดพลาด: ไม่พบข้อมูลสัญญาสำหรับงานนี้");
    }
    $contract_id = $contract_info['contract_id'];
    $designer_id = $contract_info['designer_id'];
    $stmt_contract->close();


    // === การจัดการไฟล์ที่อัปโหลด ===
    if (isset($_FILES['slip_image']) && $_FILES['slip_image']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['slip_image'];
        $upload_dir = '../uploads/payment_slips/';

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];

        // ตรวจสอบประเภทไฟล์
        if (!in_array($file_extension, $allowed_types)) {
            die("ข้อผิดพลาด: อนุญาตให้อัปโหลดเฉพาะไฟล์ JPG, PNG และ PDF เท่านั้น");
        }

        // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            die("ข้อผิดพลาด: ขนาดไฟล์ต้องไม่เกิน 5MB");
        }

        // สร้างชื่อไฟล์ใหม่เพื่อป้องกันการซ้ำกัน
        $new_filename = 'slip_' . $request_id . '_' . time() . '.' . $file_extension;
        $target_path = $upload_dir . $new_filename;

        // ย้ายไฟล์ไปยังโฟลเดอร์ที่กำหนด
        if (move_uploaded_file($file['tmp_name'], $target_path)) {

            // === การอัปเดตฐานข้อมูล ===
            $conn->begin_transaction();
            try {
                // 1. บันทึกข้อมูลการชำระเงินลงในตาราง transactions
                $payment_method = 'Bank Transfer';
                $transaction_status = 'pending'; // รอการตรวจสอบจาก designer
                // *** เพิ่ม slip_path ในคำสั่ง INSERT และตัวแปรที่จะ bind ***
                $sql_trans = "INSERT INTO transactions (contract_id, payer_id, payee_id, amount, payment_method, status, slip_path) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_trans = $conn->prepare($sql_trans);
                // *** เพิ่ม "s" สำหรับ slip_path และเพิ่มตัวแปร $target_path ***
                $stmt_trans->bind_param("iiidsss", $contract_id, $client_id, $designer_id, $amount, $payment_method, $transaction_status, $target_path);
                $stmt_trans->execute();
                $stmt_trans->close();

                // 2. อัปเดตสถานะของ client_job_requests
                $new_status = 'awaiting_deposit_verification';
                $sql_update = "UPDATE client_job_requests SET status = ? WHERE request_id = ? AND client_id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("sii", $new_status, $request_id, $client_id);
                $stmt_update->execute();
                $stmt_update->close();

                // --- [แก้ไข] 3. ส่งข้อความแจ้งเตือนไปยังนักออกแบบ ---
                $client_name = $_SESSION['full_name'] ?? $_SESSION['username'];

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
                $message_content = "สวัสดีครับ คุณ " . htmlspecialchars($client_name) . " ได้ส่งหลักฐานการชำระเงินมัดจำสำหรับงาน '" . htmlspecialchars($job_title) . "' เรียบร้อยแล้วครับ\n\nกรุณาตรวจสอบและยืนยันเพื่อเริ่มงานในขั้นตอนต่อไป";

                $sql_send_message = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
                $stmt_message = $conn->prepare($sql_send_message);
                if ($stmt_message) {
                    $stmt_message->bind_param("iis", $client_id, $designer_id, $message_content);
                    $stmt_message->execute();
                    $stmt_message->close();
                }
                // --- สิ้นสุดส่วนที่แก้ไข ---

                $conn->commit();

                // แสดงผลลัพธ์ด้วย SweetAlert
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "อัปโหลดสำเร็จ!",
                            text: "ได้ส่งหลักฐานการชำระเงินให้นักออกแบบแล้ว โปรดรอการตรวจสอบ",
                            icon: "success",
                            confirmButtonText: "ตกลง"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "my_requests.php";
                            }
                        });
                    });
                </script>';
            } catch (Exception $e) {
                $conn->rollback();
                die("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage());
            }
        } else {
            die("เกิดข้อผิดพลาดในการอัปโหลดไฟล์");
        }
    } else {
        die("ไม่มีไฟล์ถูกอัปโหลด หรือเกิดข้อผิดพลาด");
    }
} else {
    // ถ้าไม่ใช่ POST ให้กลับไปหน้าหลัก
    header("Location: main.php");
    exit();
}
