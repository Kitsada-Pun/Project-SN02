<?php
session_start();
date_default_timezone_set('Asia/Bangkok');

// ตรวจสอบว่าผู้ใช้เป็น client และ login อยู่หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

require_once '../connect.php';

$client_id = $_SESSION['user_id'];
$request_id = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;

// ตรวจสอบว่ามี request_id หรือไม่
if ($request_id === 0) {
    die("ไม่พบคำขอจ้างงาน");
}

// ดึงข้อมูลงานและข้อมูลการชำระเงินของนักออกแบบ
$sql = "
    SELECT 
        cjr.request_id,
        cjr.title,
        cjr.designer_id,
        u_designer.first_name AS designer_first_name,
        u_designer.last_name AS designer_last_name,
        ja.offered_price,
        p.payment_qr_code_url,
        p.bank_name,
        p.account_number
    FROM client_job_requests cjr
    JOIN job_applications ja ON cjr.request_id = ja.request_id
    JOIN users u_designer ON cjr.designer_id = u_designer.user_id
    LEFT JOIN profiles p ON cjr.designer_id = p.user_id
    WHERE cjr.request_id = ? AND cjr.client_id = ? AND ja.status = 'accepted'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $request_id, $client_id);
$stmt->execute();
$result = $stmt->get_result();
$job_info = $result->fetch_assoc();
$stmt->close();

if (!$job_info) {
    die("ไม่พบข้อมูลงาน หรือคุณไม่มีสิทธิ์เข้าถึงหน้านี้");
}

// สมมติว่าค่ามัดจำคือ 20% ของราคาที่ตกลง
$deposit_amount = $job_info['offered_price'] * 0.20;
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงินมัดจำ - <?= htmlspecialchars($job_info['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <nav class="bg-white shadow-sm p-4 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <a href="main.php"><img src="../dist/img/logo.png" alt="PixelLink Logo" class="h-10"></a>
            <div class="space-x-6">
                <a href="my_requests.php" class="text-gray-600 hover:text-blue-600 transition-colors">คำขอของฉัน</a>
                <a href="../logout.php" class="text-red-500 hover:text-red-700 transition-colors font-semibold">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-10">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-8 text-white">
                <h1 class="text-3xl font-bold">ชำระเงินมัดจำ (20%)</h1>
                <p class="mt-2 opacity-90">สำหรับงาน: "<?= htmlspecialchars($job_info['title']) ?>"</p>
            </div>

            <div class="p-8 grid md:grid-cols-2 gap-8">
                <div class="border-r border-gray-200 pr-8">
                    <h2 class="text-2xl font-semibold mb-6 text-gray-800">ช่องทางการชำระเงิน</h2>
                    <p class="text-sm text-gray-500 mb-4">โปรดชำระเงินไปยังบัญชีของนักออกแบบโดยตรง และอัปโหลดหลักฐานการชำระเงิน</p>

                    <?php if (!empty($job_info['payment_qr_code_url'])): ?>
                        <div class="mb-6 text-center">
                            <h3 class="font-semibold text-gray-700 mb-2">สแกน QR Code เพื่อชำระเงิน</h3>
                            <div class="qr-code-container p-3 border rounded-lg bg-white">
                                <img src="../<?= htmlspecialchars($job_info['payment_qr_code_url']) ?>" alt="QR Code" class="w-full h-auto">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($job_info['bank_name']) && !empty($job_info['account_number'])): ?>
                        <div class="bg-gray-100 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-700 mb-3">หรือโอนผ่านบัญชีธนาคาร</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">ธนาคาร:</span>
                                    <span class="font-medium text-gray-900"><?= htmlspecialchars($job_info['bank_name']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">ชื่อบัญชี:</span>
                                    <span class="font-medium text-gray-900"><?= htmlspecialchars($job_info['designer_first_name'] . ' ' . $job_info['designer_last_name']) ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">เลขที่บัญชี:</span>
                                    <div class="flex items-center">
                                        <span id="account-number" class="font-medium text-gray-900 mr-2"><?= htmlspecialchars($job_info['account_number']) ?></span>
                                        <button onclick="copyToClipboard()" class="text-blue-500 hover:text-blue-700 text-xs"><i class="far fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                     <?php if (empty($job_info['payment_qr_code_url']) && empty($job_info['bank_name'])): ?>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                           <p class="text-yellow-800">นักออกแบบยังไม่ได้เพิ่มข้อมูลการชำระเงิน กรุณาติดต่อนักออกแบบโดยตรง</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <div class="bg-gray-50 rounded-lg p-6 mb-6 border">
                        <h2 class="text-xl font-semibold mb-4 text-gray-700">สรุปรายการ</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">ราคาเต็ม:</span>
                                <span class="font-medium text-gray-800">฿<?= number_format($job_info['offered_price'], 2) ?></span>
                            </div>
                            <hr class="my-2">
                            <div class="flex justify-between text-xl font-bold">
                                <span class="text-indigo-600">ยอดชำระมัดจำ (20%):</span>
                                <span class="text-indigo-600">฿<?= number_format($deposit_amount, 2) ?></span>
                            </div>
                        </div>
                    </div>

                    <form action="upload_slip.php" method="post" enctype="multipart/form-data" id="payment-form">
                        <input type="hidden" name="request_id" value="<?= $request_id ?>">
                        <input type="hidden" name="amount" value="<?= $deposit_amount ?>">
                        
                        <div>
                            <label for="slip_image" class="block text-lg font-semibold mb-2 text-gray-700">อัปโหลดสลิป</label>
                            <input type="file" name="slip_image" id="slip_image" required 
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
    <script>
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
            }, (err) => {
                console.error('ไม่สามารถคัดลอกได้: ', err);
                alert('ไม่สามารถคัดลอกได้');
            });
        }

        $(document).ready(function() {
            $('#slip_image').on('change', function(event) {
                const preview = $('#slip-preview');
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.attr('src', e.target.result).removeClass('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                     preview.attr('src', '#').addClass('hidden');
                }
            });
        });
    </script>

</body>
</html>