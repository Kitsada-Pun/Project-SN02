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
$loggedInUserName = $_SESSION['username'] ?? 'ผู้ใช้';

// ตรวจสอบว่ามี request_id หรือไม่
if ($request_id === 0) {
    die("ไม่พบคำขอจ้างงาน");
}

// ดึงข้อมูลที่จำเป็นสำหรับหน้าชำระเงิน
$sql = "
    SELECT 
        cjr.request_id,
        cjr.title,
        cjr.designer_id,
        u_designer.first_name AS designer_first_name,
        u_designer.last_name AS designer_last_name,
        ja.offered_price
    FROM client_job_requests cjr
    JOIN job_applications ja ON cjr.request_id = ja.request_id
    JOIN users u_designer ON cjr.designer_id = u_designer.user_id
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

// สมมติว่าค่ามัดจำคือ 50% ของราคาที่ตกลง
$deposit_amount = $job_info['offered_price'] * 0.50; 
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
    <style>
        * { font-family: 'Kanit', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

    <nav class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="main.php"><img src="../dist/img/logo.png" alt="PixelLink Logo" class="h-12"></a>
            <div class="space-x-4">
                <a href="my_requests.php" class="text-gray-600 hover:text-blue-500">คำขอของฉัน</a>
                <a href="../logout.php" class="text-red-500 hover:text-red-700">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">ชำระเงินมัดจำ</h1>
                <p class="text-gray-500 mt-2">สำหรับงาน: "<?= htmlspecialchars($job_info['title']) ?>"</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-6 border">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">สรุปรายการ</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">นักออกแบบ:</span>
                        <span class="font-medium text-gray-800"><?= htmlspecialchars($job_info['designer_first_name'] . ' ' . $job_info['designer_last_name']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">ราคาที่ตกลงทั้งหมด:</span>
                        <span class="font-medium text-gray-800">฿<?= number_format($job_info['offered_price'], 2) ?></span>
                    </div>
                    <hr class="my-3">
                    <div class="flex justify-between text-xl font-bold">
                        <span class="text-blue-600">ยอดชำระมัดจำ (50%):</span>
                        <span class="text-blue-600">฿<?= number_format($deposit_amount, 2) ?></span>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">ช่องทางการชำระเงิน</h2>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                    <p class="font-semibold text-blue-800">ธนาคาร: กสิกรไทย</p>
                    <p class="text-blue-700">ชื่อบัญชี: PixelLink Co., Ltd.</p>
                    <p class="text-blue-700">เลขที่บัญชี: <span id="account-number">123-4-56789-0</span> 
                        <button onclick="copyToClipboard()" class="ml-2 text-blue-500 hover:text-blue-700"><i class="far fa-copy"></i></button>
                    </p>
                </div>
            </div>

            <form action="upload_slip.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="request_id" value="<?= $request_id ?>">
                <input type="hidden" name="amount" value="<?= $deposit_amount ?>">
                
                <div>
                    <label for="slip_image" class="block text-lg font-semibold mb-2 text-gray-700">อัปโหลดหลักฐานการชำระเงิน</label>
                    <input type="file" name="slip_image" id="slip_image" required 
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100 cursor-pointer"/>
                    <p class="text-xs text-gray-500 mt-2">รองรับไฟล์ประเภท: JPG, PNG, PDF ขนาดไม่เกิน 5MB</p>
                </div>

                <div class="mt-8 text-center">
                    <button type="submit" class="w-full bg-green-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-600 transition duration-300 text-lg">
                        <i class="fas fa-check-circle mr-2"></i> ยืนยันการชำระเงิน
                    </button>
                    <a href="my_requests.php" class="block mt-4 text-gray-500 hover:text-gray-700">กลับไปหน้ารายการ</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        function copyToClipboard() {
            const accountNumber = document.getElementById('account-number').innerText;
            navigator.clipboard.writeText(accountNumber).then(() => {
                alert('คัดลอกเลขที่บัญชีแล้ว!');
            }, (err) => {
                console.error('ไม่สามารถคัดลอกได้: ', err);
            });
        }
    </script>

</body>
</html>