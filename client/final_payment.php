<?php
session_start();
date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

require_once '../connect.php';

$client_id = $_SESSION['user_id'];
$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;

if ($request_id === 0) {
    die("ไม่พบคำขอจ้างงาน");
}

$sql = "
    SELECT 
        cjr.request_id,
        cjr.title,
        cjr.status,
        u.user_id AS designer_id,
        CONCAT(u.first_name, ' ', u.last_name) AS designer_name,
        p.payment_qr_code_url,
        p.bank_name,
        p.account_number,
        ja.offered_price
    FROM client_job_requests cjr
    JOIN users u ON cjr.designer_id = u.user_id
    LEFT JOIN profiles p ON u.user_id = p.user_id
    LEFT JOIN job_applications ja ON cjr.request_id = ja.request_id AND ja.status = 'accepted'
    WHERE cjr.request_id = ? AND cjr.client_id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $request_id, $client_id);
$stmt->execute();
$result = $stmt->get_result();
$payment_info = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$payment_info || $payment_info['status'] !== 'awaiting_final_payment') {
    die("ไม่สามารถเข้าถึงหน้านี้ได้ หรือสถานะงานไม่ถูกต้อง");
}

$total_price = (float)$payment_info['offered_price'];
$deposit_amount = $total_price * 0.20;
$remaining_amount = $total_price - $deposit_amount;

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงินส่วนที่เหลือ - <?= htmlspecialchars($payment_info['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Kanit', sans-serif; }
        .qr-code-container {
            max-width: 250px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body class="bg-gray-50">

    <?php include '../includes/nav.php'; ?>

    <main class="container mx-auto px-4 py-10">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 p-8 text-white">
                <h1 class="text-3xl font-bold">ชำระเงินงวดสุดท้าย</h1>
                <p class="mt-2 opacity-90">สำหรับงาน: "<?= htmlspecialchars($payment_info['title']) ?>"</p>
            </div>

            <div class="p-8 grid md:grid-cols-2 gap-8">
                <div class="border-r border-gray-200 pr-8">
                    <h2 class="text-2xl font-semibold mb-6 text-gray-800">ช่องทางการชำระเงิน</h2>
                    <p class="text-sm text-gray-500 mb-4">โปรดชำระเงินไปยังบัญชีของนักออกแบบโดยตรง และอัปโหลดหลักฐานการชำระเงิน</p>

                    <?php if (!empty($payment_info['payment_qr_code_url'])): ?>
                        <div class="mb-6 text-center">
                            <h3 class="font-semibold text-gray-700 mb-2">สแกน QR Code เพื่อชำระเงิน</h3>
                            <div class="qr-code-container p-3 border rounded-lg bg-white">
                                <img src="../<?= htmlspecialchars($payment_info['payment_qr_code_url']) ?>" alt="QR Code" class="w-full h-auto">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($payment_info['bank_name']) && !empty($payment_info['account_number'])): ?>
                        <div class="bg-gray-100 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-700 mb-3">หรือโอนผ่านบัญชีธนาคาร</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">ธนาคาร:</span>
                                    <span class="font-medium text-gray-900"><?= htmlspecialchars($payment_info['bank_name']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">ชื่อบัญชี:</span>
                                    <span class="font-medium text-gray-900"><?= htmlspecialchars($payment_info['designer_name']) ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">เลขที่บัญชี:</span>
                                    <div class="flex items-center">
                                        <span id="account-number" class="font-medium text-gray-900 mr-2"><?= htmlspecialchars($payment_info['account_number']) ?></span>
                                        <button onclick="copyToClipboard()" class="text-blue-500 hover:text-blue-700 text-xs"><i class="far fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($payment_info['payment_qr_code_url']) && empty($payment_info['bank_name'])): ?>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                            <p class="text-yellow-800">นักออกแบบยังไม่ได้เพิ่มข้อมูลการชำระเงิน กรุณาติดต่อนักออกแบบโดยตรง</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <div class="bg-gray-50 rounded-lg p-6 mb-6 border">
                        <h2 class="text-xl font-semibold mb-4 text-gray-700">สรุปรายการ</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between text-gray-700">
                                <span>ราคาเต็ม</span>
                                <span class="font-semibold">฿<?= number_format($total_price, 2) ?></span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>ชำระมัดจำแล้ว (20%)</span>
                                <span class="font-semibold text-red-500">- ฿<?= number_format($deposit_amount, 2) ?></span>
                            </div>
                            <hr class="my-2">
                            <div class="flex justify-between text-xl font-bold">
                                <span class="text-green-600">ยอดชำระคงเหลือ:</span>
                                <span class="text-green-600">฿<?= number_format($remaining_amount, 2) ?></span>
                            </div>
                        </div>
                    </div>

                    <form id="slip-upload-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="request_id" value="<?= $request_id ?>">
                        <input type="hidden" name="amount" value="<?= $remaining_amount ?>">
                        
                        <div>
                            <label for="payment_slip" class="block text-lg font-semibold mb-2 text-gray-700">อัปโหลดสลิป</label>
                            <input type="file" name="payment_slip" id="payment_slip" required 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"/>
                            <p class="text-xs text-gray-500 mt-2">รองรับ: JPG, PNG, PDF (ไม่เกิน 5MB)</p>
                             <div class="mt-4">
                                <img id="slip-preview" src="#" alt="ตัวอย่างสลิป" class="hidden max-w-full h-auto rounded-md border"/>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full bg-green-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-600 transition duration-300 text-lg flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i> ยืนยันและอัปโหลดหลักฐาน
                            </button>
                            <a href="my_requests.php" class="block mt-4 text-center text-gray-500 hover:text-gray-700 text-sm">กลับไปหน้ารายการ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ฟังก์ชัน Copy เลขบัญชี
        function copyToClipboard() {
            const accountNumber = document.getElementById('account-number').innerText;
            navigator.clipboard.writeText(accountNumber).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'คัดลอกเลขที่บัญชีแล้ว',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        }

        $(document).ready(function() {
            // Preview slip image
            $('#payment_slip').on('change', function(event) {
                const preview = $('#slip-preview');
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.attr('src', e.target.result).removeClass('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Submit form
            $('#slip-upload-form').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                Swal.fire({
                    title: 'กำลังอัปโหลด...',
                    text: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: 'upload_final_slip.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'อัปโหลดสำเร็จ!',
                                text: response.message,
                            }).then(() => {
                                window.location.href = 'my_requests.php';
                            });
                        } else {
                            Swal.fire('ผิดพลาด!', response.message, 'error');
                        }
                    },
                    error: function() {
                         Swal.fire('ผิดพลาด!', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                    }
                });
            });
        });
    </script>
</body>
</html>