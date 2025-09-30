<?php
session_start();
date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

require_once '../connect.php';

$client_id = $_SESSION['user_id'];
// --- START: MODIFIED CODE ---
// แก้ไขการดึงข้อมูลชื่อผู้ใช้ให้กระชับและถูกต้อง
$loggedInUserName = $_SESSION['username'] ?? '';
if (empty($loggedInUserName)) {
    $sql_user = "SELECT first_name, last_name FROM users WHERE user_id = ?";
    $stmt_user = $conn->prepare($sql_user);
    if ($stmt_user) {
        $stmt_user->bind_param("i", $client_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if ($user_info = $result_user->fetch_assoc()) {
            $loggedInUserName = $user_info['first_name'] . ' ' . $user_info['last_name'];
        }
        $stmt_user->close();
    }
}
// --- END: MODIFIED CODE ---


// ดึงข้อมูลคำขอจ้างงานทั้งหมดของ Client คนนี้ พร้อมดึงราคาที่เสนอ (offered_price) ถ้ามี
$requests = [];
$sql = "
    SELECT 
        cjr.request_id,
        cjr.title,
        cjr.description,
        COALESCE(ja.offered_price, cjr.budget) AS budget,
        cjr.posted_date,
        cjr.status,
        u.user_id AS designer_id,
        CONCAT(u.first_name, ' ', u.last_name) AS designer_name,
        (SELECT t.slip_path 
         FROM transactions t
         JOIN contracts con_t ON t.contract_id = con_t.contract_id
         WHERE con_t.request_id = cjr.request_id 
         ORDER BY t.transaction_date DESC 
         LIMIT 1) AS slip_path,
        -- --- [เพิ่มส่วนนี้เข้ามา] ---
        (SELECT COUNT(r.review_id) 
         FROM reviews r 
         JOIN contracts c_r ON r.contract_id = c_r.contract_id 
         WHERE c_r.request_id = cjr.request_id AND r.reviewer_id = cjr.client_id
        ) > 0 AS has_reviewed
        -- --- สิ้นสุดส่วนที่เพิ่ม ---
    FROM client_job_requests cjr
    LEFT JOIN users u ON cjr.designer_id = u.user_id
    LEFT JOIN job_applications ja ON cjr.request_id = ja.request_id AND ja.status = 'accepted'
    WHERE cjr.client_id = ?
    ORDER BY cjr.posted_date DESC
";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $requests = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล");
}

$counts = [
    'open' => 0,
    'proposed' => 0,
    'pending_deposit' => 0,
    'awaiting_deposit_verification' => 0,
    'assigned' => 0,
    'draft_submitted' => 0,
    'awaiting_final_payment' => 0,
    'final_payment_verification' => 0, // <-- เพิ่มบรรทัดนี้
    'completed' => 0,
    'cancelled' => 0,
    'rejected' => 0, // เพิ่มบรรทัดนี้
];
foreach ($requests as $request) {
    if (isset($counts[$request['status']])) {
        $counts[$request['status']]++;
    }
}
// นับจำนวนรวมสำหรับแท็บ "รอชำระเงินมัดจำ"
$pending_deposit_total = ($counts['pending_deposit'] ?? 0) + ($counts['awaiting_deposit_verification'] ?? 0);
// นับจำนวนรวมสำหรับแท็บ "รอชำระเงิน"
$awaiting_final_payment_total = ($counts['awaiting_final_payment'] ?? 0) + ($counts['final_payment_verification'] ?? 0);
// [เพิ่ม] นับจำนวนรวมสำหรับแท็บ "ยกเลิก"
$cancelled_rejected_total = ($counts['cancelled'] ?? 0) + ($counts['rejected'] ?? 0);

$conn->close();

function getStatusInfoClient($status)
{
    switch ($status) {
        case 'open':
            return ['text' => 'ส่งคำขอจ้างงาน', 'color' => 'bg-gray-200 text-gray-800', 'tab' => 'open'];
        case 'proposed':
            return ['text' => 'รอการพิจารณา', 'color' => 'bg-yellow-100 text-yellow-800', 'tab' => 'proposed'];
        case 'pending_deposit':
            return ['text' => 'รอชำระเงินมัดจำ', 'color' => 'bg-orange-100 text-orange-800', 'tab' => 'pending_deposit'];
        case 'awaiting_deposit_verification':
            return ['text' => 'รอตรวจสอบ (มัดจำ)', 'color' => 'bg-purple-100 text-purple-800', 'tab' => 'pending_deposit'];
        case 'assigned':
            return ['text' => 'กำลังดำเนินการ', 'color' => 'bg-blue-100 text-blue-800', 'tab' => 'assigned'];
        case 'draft_submitted': // <-- เพิ่ม case ใหม่
            return ['text' => 'ตรวจสอบงาน', 'color' => 'bg-purple-100 text-purple-800', 'tab' => 'review_work'];

        case 'awaiting_final_payment':
            return ['text' => 'รอชำระเงินส่วนที่เหลือ', 'color' => 'bg-yellow-100 text-yellow-800', 'tab' => 'awaiting_final_payment'];
        case 'final_payment_verification':
            return ['text' => 'รอตรวจสอบยอดชำระคงเหลือ', 'color' => 'bg-purple-100 text-purple-800', 'tab' => 'awaiting_final_payment'];
        case 'completed':
            return ['text' => 'เสร็จสมบูรณ์', 'color' => 'bg-green-100 text-green-800', 'tab' => 'completed'];
        case 'cancelled':
            return ['text' => 'คุณปฏิเสธ', 'color' => 'bg-gray-200 text-gray-800', 'tab' => 'cancelled'];
        case 'rejected': // [เพิ่ม case นี้]
            return ['text' => 'ถูกยกเลิก', 'color' => 'bg-red-100 text-red-800', 'tab' => 'cancelled'];
        default:
            return ['text' => 'ไม่ระบุ', 'color' => 'bg-gray-100 text-gray-800', 'tab' => 'all'];
    }
}
?>
<!DOCTYPE html>
<html lang="th" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คำขอจ้างงานของฉัน - PixelLink</title>
    <link rel="icon" type="image/png" href="../dist/img/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Kanit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #e8edf3 100%);
            color: #2c3e50;
            overflow-x: hidden;
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background: linear-gradient(45deg, #0a5f97 0%, #0d96d2 100%);
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(13, 150, 210, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #0d96d2 0%, #0a5f97 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 150, 210, 0.5);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(108, 117, 125, 0.2);
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(108, 117, 125, 0.4);
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
        }

        .text-gradient {
            background: linear-gradient(45deg, #0a5f97, #0d96d2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .pixellink-logo,
        .pixellink-logo-footer {
            font-weight: 700;
            font-size: 2.25rem;
            background: linear-gradient(45deg, #0a5f97, #0d96d2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .pixellink-logo b,
        .pixellink-logo-footer b {
            color: #0d96d2;
        }

        .card-item {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .card-image {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .hero-section {
            background-image: url('dist/img/cover.png');
            background-size: cover;
            background-position: center;
            position: relative;
            z-index: 1;
            padding: 8rem 0;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
    </style>
</head>

<body class="bg-slate-50 flex flex-col min-h-screen">

    <nav class="bg-white/80 backdrop-blur-sm p-4 shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <a href="main.php">
                <img src="../dist/img/logo.png" alt="PixelLink Logo" class="h-12 transition-transform hover:scale-105">
            </a>

            <div class="space-x-2 sm:space-x-4 flex items-center flex-nowrap">

                <span class="font-medium text-slate-700 text-xs sm:text-base whitespace-nowrap">สวัสดี, <?= htmlspecialchars($loggedInUserName) ?>!</span>

                <a href="../logout.php" class="btn-danger text-white text-xs sm:text-base px-3 sm:px-5 py-2 rounded-lg font-medium shadow-md whitespace-nowrap">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 flex-grow">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-slate-800">คำขอจ้างงานของฉัน</h1>
            <p class="text-slate-500 mt-1">จัดการและติดตามสถานะคำขอจ้างงานทั้งหมดของคุณ</p>
        </div>

        <div x-data="{ tab: 'all' }">
            <div class="mb-6 p-1.5 bg-slate-200/60 rounded-xl flex flex-wrap items-center gap-2">
                <button @click="tab = 'all'" :class="tab === 'all' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300/60'" class="px-4 py-2 text-sm font-semibold rounded-lg transition-all">
                    <i class="fa-solid fa-list-ul mr-1.5"></i> ทั้งหมด
                </button>
                <button @click="tab = 'proposed'" :class="tab === 'proposed' ? 'bg-white text-yellow-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300/60'" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-all">
                    <i class="fa-solid fa-file-alt mr-1.5"></i> รอพิจารณา
                    <?php if ($counts['proposed'] > 0) : ?>
                        <span class="ml-2 inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-xs font-bold text-white"><?= $counts['proposed'] ?></span>
                    <?php endif; ?>
                </button>
                <button @click="tab = 'pending_deposit'" :class="tab === 'pending_deposit' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300/60'" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-all">
                    <i class="fa-solid fa-money-bill-wave mr-1.5"></i> ชำระเงินมัดจำ
                    <?php if ($pending_deposit_total > 0) : ?>
                        <span class="ml-2 inline-flex items-center justify-center h-5 w-5 rounded-full bg-orange-500 text-xs font-bold text-white"><?= $pending_deposit_total ?></span>
                    <?php endif; ?>
                </button>
                <button @click="tab = 'assigned'" :class="tab === 'assigned' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300/60'" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-all">
                    <i class="fa-solid fa-person-digging mr-1.5"></i> กำลังดำเนินการ
                    <?php if ($counts['assigned'] > 0) : ?>
                        <span class="ml-2 inline-flex items-center justify-center h-5 w-5 rounded-full bg-blue-500 text-xs font-bold text-white"><?= $counts['assigned'] ?></span>
                    <?php endif; ?>
                </button>
                <button @click="tab = 'review_work'" :class="tab === 'review_work' ? 'bg-white text-purple-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300/60'" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-all">
                    <i class="fa-solid fa-file-import mr-1.5"></i> ตรวจสอบงาน
                    <?php if ($counts['draft_submitted'] > 0) : ?>
                        <span class="ml-2 inline-flex items-center justify-center h-5 w-5 rounded-full bg-purple-500 text-xs font-bold text-white"><?= $counts['draft_submitted'] ?></span>
                    <?php endif; ?>
                </button>
                <button @click="tab = 'awaiting_final_payment'" :class="tab === 'awaiting_final_payment' ? 'bg-white text-yellow-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300/60'" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-all">
                    <i class="fa-solid fa-hand-holding-dollar mr-1.5"></i> รอชำระเงิน
                    <?php if ($awaiting_final_payment_total > 0) : ?>
                        <span class="ml-2 inline-flex items-center justify-center h-5 w-5 rounded-full bg-yellow-500 text-xs font-bold text-white"><?= $awaiting_final_payment_total ?></span>
                    <?php endif; ?>
                </button>
                <button @click="tab = 'completed'" :class="tab === 'completed' ? 'bg-white text-green-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300/60'" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-all">
                    <i class="fa-solid fa-circle-check mr-1.5"></i> เสร็จสมบูรณ์
                    <?php if ($counts['completed'] > 0) : ?>
                        <span class="ml-2 inline-flex items-center justify-center h-5 w-5 rounded-full bg-green-500 text-xs font-bold text-white"><?= $counts['completed'] ?></span>
                    <?php endif; ?>
                </button>
                <button @click="tab = 'cancelled'" :class="tab === 'cancelled' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300/60'" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-all">
                    <i class="fa-solid fa-circle-xmark mr-1.5"></i> ยกเลิก
                    <?php if ($cancelled_rejected_total > 0) : ?>
                        <span class="ml-2 inline-flex items-center justify-center h-5 w-5 rounded-full bg-gray-500 text-xs font-bold text-white"><?= $cancelled_rejected_total ?></span>
                    <?php endif; ?>
                </button>
            </div>
            <div class="space-y-5">
                <?php if (empty($requests)) : ?>
                    <div class="text-center bg-white rounded-lg shadow-sm p-12">
                        <div class="mx-auto w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-file-circle-xmark fa-2x text-slate-400"></i>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-slate-700">คุณยังไม่ได้สร้างคำขอจ้างงาน</h3>
                        <p class="mt-1 text-slate-500">เริ่มจ้างนักออกแบบได้โดยการสร้างคำขอจ้างงานใหม่</p>
                        <a href="../job_listings.php" class="mt-4 inline-block px-6 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600">ไปที่หน้าจ้างงาน</a>
                    </div>
                <?php else : ?>
                    <?php foreach ($requests as $request) : ?>
                        <?php
                        $statusInfo = getStatusInfoClient($request['status']);
                        $data_status = $statusInfo['tab'];
                        ?>
                        <div x-show="tab === 'all' || tab === '<?= $data_status ?>'" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                            <div class="flex flex-col sm:flex-row gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                                        <h2 class="text-xl font-bold text-slate-800 leading-tight">
                                            <a href="#" class="view-details-btn hover:text-blue-600" data-request-id="<?= $request['request_id'] ?>">
                                                <?= htmlspecialchars($request['title']) ?>
                                            </a>
                                        </h2>
                                        <span class="text-xs font-semibold px-3 py-1 rounded-full <?= $statusInfo['color'] ?>">
                                            <?= htmlspecialchars($statusInfo['text']) ?>
                                        </span>
                                    </div>
                                    <p class="text-slate-500 text-sm mb-4 line-clamp-2">
                                        <?= htmlspecialchars($request['description']) ?>
                                    </p>
                                    <div class="text-sm space-y-2 text-slate-600">
                                        <?php // --- [เพิ่ม] เงื่อนไขแสดงชื่อนักออกแบบ ถ้ามี --- 
                                        ?>
                                        <?php if (!empty($request['designer_name'])) : ?>
                                            <p><i class="fa-solid fa-user-pen w-5 text-slate-400 mr-1"></i> นักออกแบบ:
                                                <a href="view_profile.php?user_id=<?= $request['designer_id'] ?>" class="font-semibold text-blue-600 hover:underline">
                                                    <?= htmlspecialchars($request['designer_name']) ?>
                                                </a>
                                            </p>
                                        <?php endif; ?>
                                        <p><i class="fa-solid fa-calendar-day w-5 text-slate-400 mr-1"></i> ยื่นข้อเสนอเมื่อ: <?= date('d M Y, H:i', strtotime($request['posted_date'])) ?></p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 sm:text-right sm:border-l sm:pl-6 border-slate-200/80 w-full sm:w-auto">
                                    <div class="text-2xl font-bold text-green-600 mb-4">
                                        ฿<?= !empty($request['budget']) ? htmlspecialchars(number_format((float)$request['budget'], 2)) : 'N/A' ?>
                                    </div>
                                    <div class="flex flex-col sm:items-end gap-2">
                                        <?php if ($request['status'] === 'proposed') : ?>
                                            <a href="review_proposal.php?request_id=<?= $request['request_id'] ?>" class="w-full sm:w-auto text-center px-4 py-2 bg-yellow-500 text-white rounded-lg text-sm font-semibold hover:bg-yellow-600">
                                                <i class="fa-solid fa-file-alt mr-1"></i> พิจารณาข้อเสนอ
                                            </a>
                                        <?php elseif ($request['status'] === 'pending_deposit') : ?>
                                            <a href="payment.php?request_id=<?= $request['request_id'] ?>" class="w-full sm:w-auto text-center px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-semibold hover:bg-green-600">
                                                <i class="fa-solid fa-credit-card mr-1"></i> ชำระเงินมัดจำ
                                            </a>
                                        <?php elseif ($request['status'] === 'awaiting_deposit_verification') : ?>
                                            <button
                                                class="view-slip-btn w-full sm:w-auto text-center px-4 py-2 bg-purple-500 text-white rounded-lg text-sm font-semibold hover:bg-purple-600 transition-all"
                                                data-slip-url="<?= htmlspecialchars($request['slip_path']) ?>">
                                                <i class="fa-solid fa-receipt mr-1"></i> ดูหลักฐานการชำระเงิน
                                            </button>
                                        <?php elseif ($request['status'] === 'assigned') : ?>
                                            <a href="../messages.php?to_user=<?= $request['designer_id'] ?>" class="w-full sm:w-auto text-center px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-semibold hover:bg-blue-600">
                                                <i class="fa-solid fa-comments mr-1"></i> พูดคุยกับนักออกแบบ
                                            </a>
                                        <?php elseif ($request['status'] === 'draft_submitted') : ?>
                                            <a href="review_work.php?request_id=<?= $request['request_id'] ?>" class="w-full sm:w-auto text-center px-4 py-2 bg-purple-500 text-white rounded-lg text-sm font-semibold hover:bg-purple-600">
                                                <i class="fa-solid fa-file-circle-check mr-1"></i> ตรวจสอบงาน
                                            </a>
                                        <?php elseif ($request['status'] === 'awaiting_final_payment') : ?>
                                            <a href="final_payment.php?request_id=<?= $request['request_id'] ?>" class="w-full sm:w-auto text-center px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-semibold hover:bg-green-600">
                                                <i class="fa-solid fa-credit-card mr-1"></i> ชำระเงินส่วนที่เหลือ
                                            </a>
                                        <?php elseif ($request['status'] === 'final_payment_verification') : ?>
                                            <button
                                                class="view-slip-btn w-full sm:w-auto text-center px-4 py-2 bg-purple-500 text-white rounded-lg text-sm font-semibold hover:bg-purple-600 transition-all"
                                                data-slip-url="<?= htmlspecialchars($request['slip_path']) ?>">
                                                <i class="fa-solid fa-receipt mr-1"></i> ดูหลักฐานการชำระเงิน
                                            </button>
                                        <?php elseif ($request['status'] === 'completed') : ?>
                                            <a href="#" class="download-final-file-btn w-full sm:w-auto text-center px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-semibold hover:bg-green-600 mb-2" data-request-id="<?= $request['request_id'] ?>">
                                                <i class="fa-solid fa-download mr-1"></i> ดาวน์โหลดไฟล์งาน
                                            </a>

                                            <?php if (!$request['has_reviewed']) : ?>
                                                <a href="submit_review.php?request_id=<?= $request['request_id'] ?>" class="w-full sm:w-auto text-center px-4 py-2 bg-yellow-500 text-white rounded-lg text-sm font-semibold hover:bg-yellow-600">
                                                    <i class="fa-solid fa-star mr-1"></i> ให้คะแนนและรีวิว
                                                </a>
                                            <?php else : ?>
                                                <button disabled class="w-full sm:w-auto text-center px-4 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm font-semibold cursor-not-allowed">
                                                    <i class="fa-solid fa-check mr-1"></i> คุณรีวิวแล้ว
                                                </button>
                                            <?php endif; ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div x-show="tab === 'proposed' && <?= $counts['proposed'] ?> === 0" class="text-center bg-white rounded-lg shadow-sm p-12">
                        <i class="fa-solid fa-file-alt fa-3x text-slate-300"></i>
                        <h3 class="mt-4 text-xl font-semibold text-slate-700">ไม่มีใบเสนอราคาที่ต้องพิจารณา</h3>
                        <p class="mt-1 text-slate-500">เมื่อนักออกแบบส่งข้อเสนอมา งานจะแสดงที่นี่</p>
                    </div>
                    <div x-show="tab === 'pending_deposit' && <?= $pending_deposit_total ?> === 0" class="text-center bg-white rounded-lg shadow-sm p-12">
                        <i class="fa-solid fa-money-bill-wave fa-3x text-slate-300"></i>
                        <h3 class="mt-4 text-xl font-semibold text-slate-700">ไม่มีงานที่รอชำระเงินมัดจำ</h3>
                        <p class="mt-1 text-slate-500">หลังจากตอบตกลงใบเสนอราคาแล้ว งานจะแสดงที่นี่</p>
                    </div>
                    <div x-show="tab === 'assigned' && <?= $counts['assigned'] ?> === 0" class="text-center bg-white rounded-lg shadow-sm p-12">
                        <i class="fa-solid fa-person-digging fa-3x text-slate-300"></i>
                        <h3 class="mt-4 text-xl font-semibold text-slate-700">ไม่มีงานที่กำลังดำเนินการ</h3>
                        <p class="mt-1 text-slate-500">เมื่องานเริ่มขึ้นแล้ว คุณสามารถติดตามความคืบหน้าได้ที่นี่</p>
                    </div>
                    <div x-show="tab === 'review_work' && <?= $counts['draft_submitted'] ?> === 0" class="text-center bg-white rounded-lg shadow-sm p-12">
                        <i class="fa-solid fa-file-import fa-3x text-slate-300"></i>
                        <h3 class="mt-4 text-xl font-semibold text-slate-700">ไม่มีงานที่ต้องตรวจสอบ</h3>
                        <p class="mt-1 text-slate-500">เมื่อนักออกแบบส่งมอบงานฉบับร่าง คุณสามารถตรวจสอบและอนุมัติได้จากที่นี่</p>
                    </div>
                    <div x-show="tab === 'awaiting_final_payment' && <?= $awaiting_final_payment_total ?> === 0" class="text-center bg-white rounded-lg shadow-sm p-12">
                        <i class="fa-solid fa-hand-holding-dollar fa-3x text-slate-300"></i>
                        <h3 class="mt-4 text-xl font-semibold text-slate-700">ไม่มีงานที่รอชำระเงิน</h3>
                        <p class="mt-1 text-slate-500">หลังจากคุณยอมรับงานฉบับร่างแล้ว งานจะมาแสดงที่นี่เพื่อรอชำระเงินงวดสุดท้าย</p>
                    </div>
                    <div x-show="tab === 'completed' && <?= $counts['completed'] ?> === 0" class="text-center bg-white rounded-lg shadow-sm p-12">
                        <i class="fa-solid fa-circle-check fa-3x text-slate-300"></i>
                        <h3 class="mt-4 text-xl font-semibold text-slate-700">ยังไม่มีงานที่เสร็จสมบูรณ์</h3>
                        <p class="mt-1 text-slate-500">รายการงานที่จ้างสำเร็จแล้วทั้งหมดจะถูกเก็บไว้ที่นี่</p>
                    </div>
                    <div x-show="tab === 'cancelled' && <?= $counts['cancelled'] ?> === 0" class="text-center bg-white rounded-lg shadow-sm p-12">
                        <i class="fa-solid fa-circle-xmark fa-3x text-slate-300"></i>
                        <h3 class="mt-4 text-xl font-semibold text-slate-700">ไม่มีงานที่ถูกยกเลิก</h3>
                        <p class="mt-1 text-slate-500">งานที่คุณปฏิเสธ หรือถูกนักออกแบบยกเลิกจะแสดงอยู่ที่นี่</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.view-details-btn').on('click', function(e) {
                e.preventDefault();
                const requestId = $(this).data('request-id');

                $.ajax({
                    url: '../get_request_details.php',
                    method: 'GET',
                    data: {
                        request_id: requestId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            const details = response.data;
                            const deadline = new Date(details.deadline).toLocaleDateString('th-TH', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                            let attachmentHtml = '';
                            if (details.attachment_path && details.attachment_path.trim() !== '') {
                                const filePath = details.attachment_path.startsWith('../') ? details.attachment_path.substring(3) : details.attachment_path;
                                attachmentHtml = `<hr style="margin: 1rem 0;"><p><strong>ไฟล์แนบ:</strong></p><a href="../${filePath}" target="_blank"><img src="../${filePath}" alt="ไฟล์แนบ" style="max-width: 100%; max-height: 250px; margin-top: 5px; border-radius: 5px; border: 1px solid #ddd;"></a>`;
                            }
                            Swal.fire({
                                title: `<strong>รายละเอียดคำขอจ้างงาน</strong>`,
                                html: `<div style="text-align: left; padding: 0 1rem;"><p><strong>ชื่องาน:</strong> ${details.title}</p><p><strong>ประเภทงาน:</strong> ${details.category_name || 'ไม่ได้ระบุ'}</p><p><strong>รายละเอียด:</strong></p><div style="white-space: pre-wrap; background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; border-radius: 5px; max-height: 150px; overflow-y: auto;">${details.description}</div>${attachmentHtml}<hr style="margin: 1rem 0;"><p><strong>งบประมาณ:</strong> ${details.budget ? '฿' + parseFloat(details.budget).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 'ไม่ได้ระบุ'}</p><p><strong>ส่งมอบงานภายใน:</strong> ${deadline}</p></div>`,
                                confirmButtonText: 'ปิด',
                                width: '600px'
                            });
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเพื่อดึงข้อมูลได้', 'error');
                    }
                });
            });
            // --- START: ADD THIS CODE ---
            // Event listener for viewing payment slip
            // ...
            $(document).on('click', '.view-slip-btn', function() {
                let slipUrl = $(this).data('slip-url');

                // ตรวจสอบว่ามีข้อมูล Path หรือไม่
                if (slipUrl) {
                    // *** เพิ่ม Logic จัดการ Path ***
                    // ถ้า Path ที่ได้มาขึ้นต้นด้วย '../' ให้ตัดออก
                    if (slipUrl.startsWith('../')) {
                        slipUrl = slipUrl.substring(3);
                    }
                    // สร้าง URL ที่ถูกต้องโดยอ้างอิงจาก root ของเว็บ
                    const finalUrl = '../' + slipUrl;

                    Swal.fire({
                        title: 'หลักฐานการชำระเงิน',
                        imageUrl: finalUrl, // ใช้ URL ที่ปรับปรุงแล้ว
                        imageAlt: 'Payment Slip',
                        imageWidth: 400, // กำหนดขนาดเพื่อให้ดูสวยงาม
                        confirmButtonText: 'ปิด'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: 'ไม่พบไฟล์หลักฐานการชำระเงิน!'
                    });
                }
            })
            // จัดการปุ่มดาวน์โหลดไฟล์งานฉบับสมบูรณ์
            $(document).on('click', '.download-final-file-btn', function(e) {
                e.preventDefault();
                const requestId = $(this).data('request-id');

                $.ajax({
                    url: 'get_final_work_client.php',
                    method: 'GET',
                    data: {
                        request_id: requestId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success' && response.filePath) {
                            window.open(response.filePath, '_blank');
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด', response.message || 'ไม่พบไฟล์', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('ผิดพลาด!', 'ไม่สามารถเชื่อมต่อเพื่อดึงข้อมูลไฟล์ได้', 'error');
                    }
                });
            });
        });
        // --- END: ADD THIS CODE ---
    </script>
</body>

</html>