<?php
session_start();
date_default_timezone_set('Asia/Bangkok');

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$home_link = '../index.php'; // Default URL
if (isset($_SESSION['user_type'])) {
    switch ($_SESSION['user_type']) {
        case 'designer':
            $home_link = 'main.php';
            break;
        case 'client':
            $home_link = '../client/main.php';
            break;
        case 'admin':
            $home_link = '../admin/main.php';
            break;
    }
}

// --- ตรวจสอบสถานะการอัปเดตจาก URL ---
$show_success_popup = false;
$show_no_change_popup = false;
if (isset($_GET['update_status'])) {
    if ($_GET['update_status'] === 'success') {
        $show_success_popup = true;
    } elseif ($_GET['update_status'] === 'nochange') {
        $show_no_change_popup = true;
    }
}
// --- การตั้งค่าการเชื่อมต่อฐานข้อมูล ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pixellink";

$condb = new mysqli($servername, $username, $password, $dbname);
if ($condb->connect_error) {
    die("Connection failed: " . $condb->connect_error);
}
$condb->set_charset("utf8mb4");

// ดึง user_id จาก URL และ Session
$user_id_to_view = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$current_user_id = $_SESSION['user_id'] ?? 0;
$loggedInUserName = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Designer';

// --- ดึงชื่อผู้ใช้ที่ล็อกอิน ---
if (isset($_SESSION['user_id'])) {
    $loggedInUserName = $_SESSION['username'] ?? $_SESSION['full_name'] ?? '';
    if (empty($loggedInUserName)) {
        $user_id = $_SESSION['user_id'];
        $sql_user = "SELECT first_name, last_name FROM users WHERE user_id = ?";
        $stmt_user = $condb->prepare($sql_user);
        if ($stmt_user) {
            $stmt_user->bind_param("i", $user_id);
            $stmt_user->execute();
            $result_user = $stmt_user->get_result();
            if ($result_user->num_rows === 1) {
                $user_info = $result_user->fetch_assoc();
                $loggedInUserName = $user_info['first_name'] . ' ' . $user_info['last_name'];
            }
            $stmt_user->close();
        }
    }
}

$profile_data = null;
$job_postings_for_profile = [];
$portfolio_items = [];
$verification_status = 'not_submitted';

if ($user_id_to_view > 0) {
    // ========== (แก้ไข) เปลี่ยน linkedin_url เป็น tiktok_url ==========
    $sql_profile = "SELECT p.*, u.first_name, u.last_name, u.email, u.phone_number, u.username, u.is_verified,
                           p.facebook_url, p.instagram_url, p.tiktok_url,
                           p.payment_qr_code_url, p.bank_name, p.account_number
                      FROM profiles p
                      JOIN users u ON p.user_id = u.user_id
                      WHERE p.user_id = ?";
    $stmt_profile = $condb->prepare($sql_profile);
    if ($stmt_profile) {
        $stmt_profile->bind_param("i", $user_id_to_view);
        $stmt_profile->execute();
        $profile_data = $stmt_profile->get_result()->fetch_assoc();
        $stmt_profile->close();
    }
    // ตรวจสอบสถานะการยื่นเอกสาร
    if ($profile_data && !$profile_data['is_verified']) { // ตรวจสอบต่อเมื่อยังไม่ได้รับการยืนยันตัวตน
        $sql_verify_status = "SELECT status FROM verification_submissions WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1";
        $stmt_verify = $condb->prepare($sql_verify_status);
        if ($stmt_verify) {
            $stmt_verify->bind_param("i", $user_id_to_view);
            $stmt_verify->execute();
            $verify_result = $stmt_verify->get_result()->fetch_assoc();
            if ($verify_result && $verify_result['status'] === 'pending') {
                $verification_status = 'pending';
            }
            $stmt_verify->close();
        }
    }
    // ดึงโพสต์งานพร้อม Path รูปภาพ
    $sql_job_postings_for_profile = "SELECT
                                        jp.post_id, jp.title, jp.description, jp.price_range, jp.posted_date,
                                        u.first_name, u.last_name, jc.category_name,
                                        uf.file_path AS job_image_path
                                      FROM job_postings AS jp
                                      JOIN users AS u ON jp.designer_id = u.user_id
                                      LEFT JOIN job_categories AS jc ON jp.category_id = jc.category_id
                                      LEFT JOIN uploaded_files AS uf ON jp.main_image_id = uf.file_id
                                      WHERE jp.designer_id = ? AND jp.status = 'active'
                                      ORDER BY jp.posted_date DESC";

    $stmt_job_postings = $condb->prepare($sql_job_postings_for_profile);
    if ($stmt_job_postings) {
        $stmt_job_postings->bind_param("i", $user_id_to_view);
        $stmt_job_postings->execute();
        $job_postings_for_profile = $stmt_job_postings->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_job_postings->close();
    }
    // ดึงข้อมูลคะแนนรีวิวเฉลี่ย
    $avg_rating = 0;
    $review_count = 0;
    $sql_reviews = "SELECT AVG(rating) as avg_rating, COUNT(review_id) as review_count
                  FROM reviews
                  WHERE reviewed_user_id = ? AND review_type = 'client_review_designer'";
    $stmt_reviews = $condb->prepare($sql_reviews);
    if ($stmt_reviews) {
        $stmt_reviews->bind_param("i", $user_id_to_view);
        $stmt_reviews->execute();
        $review_data = $stmt_reviews->get_result()->fetch_assoc();
        if ($review_data) {
            $avg_rating = $review_data['avg_rating'] ?? 0;
            $review_count = $review_data['review_count'] ?? 0;
        }
        $stmt_reviews->close();
    }
}

$condb->close();

// กำหนดค่าเริ่มต้นสำหรับแสดงผล
$display_name = trim(($profile_data['first_name'] ?? '') . ' ' . ($profile_data['last_name'] ?? '')) ?: ($profile_data['username'] ?? 'ไม่ระบุชื่อ');
$is_verified = $profile_data['is_verified'] ?? 0;
$display_email = $profile_data['email'] ?? 'ไม่ระบุอีเมล';
$display_tel = $profile_data['phone_number'] ?? 'ไม่ระบุเบอร์โทรศัพท์';
$display_company_name = $profile_data['company_name'] ?? '';
$display_bio = $profile_data['bio'] ?? 'ยังไม่มีประวัติ';
$display_skills = !empty($profile_data['skills']) ? explode(',', $profile_data['skills']) : [];

// ========== (แก้ไข) ตรรกะการแสดงรูปโปรไฟล์ ==========
$raw_pic_path = $profile_data['profile_picture_url'] ?? '';
$display_profile_pic = '../dist/img/user8.jpg'; // Default image
if (!empty($raw_pic_path) && file_exists('..' . $raw_pic_path)) {
    $display_profile_pic = '..' . $raw_pic_path;
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของ <?= htmlspecialchars($display_name) ?> | PixelLink</title>
    <link rel="icon" type="image/png" href="../dist/img/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }

        .text-gradient {
            background: linear-gradient(45deg, #0a5f97, #0d96d2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .carousel-content {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: calc(100% - 1rem);
            gap: 1.5rem;
            overflow-x: scroll;
            scroll-behavior: smooth;
            scrollbar-width: none;
        }

        .carousel-content::-webkit-scrollbar {
            display: none;
        }

        @media (min-width: 768px) {
            .carousel-content {
                grid-auto-columns: calc(50% - 0.75rem);
            }
        }

        @media (min-width: 1024px) {
            .carousel-content {
                /* ปรับให้แสดง 2 การ์ดในคอลัมน์ขวา */
                grid-auto-columns: calc(50% - 0.75rem);
            }
        }

        .card-item {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            flex-shrink: 0;
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

        .carousel-button {
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 0.75rem 0.5rem;
            cursor: pointer;
            z-index: 10;
            border-radius: 9999px;
            position: absolute;
            top: 40%; /* Adjust position */
            transform: translateY(-50%);
            transition: all 0.3s ease;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .carousel-button.left {
            left: -1rem;
        }

        .carousel-button.right {
            right: -1rem;
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

        .btn-danger {
            background-color: #ef4444;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
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
    </style>
</head>

<body class="bg-slate-100 min-h-screen flex flex-col">

    <nav class="bg-white/80 backdrop-blur-sm p-4 shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <a href="main.php">
                <img src="../dist/img/logo.png" alt="PixelLink Logo" class="h-12 transition-transform hover:scale-105">
            </a>
            <div class="space-x-2 sm:space-x-4 flex items-center flex-nowrap">
                <span class="font-medium text-slate-700 text-xs sm:text-base whitespace-nowrap">
                    สวัสดี, <?= htmlspecialchars($loggedInUserName) ?>!
                </span>

                <a href="view_profile.php?user_id=<?= $_SESSION['user_id']; ?>" class="btn-primary text-white text-xs sm:text-base px-3 sm:px-5 py-2 rounded-lg font-medium shadow-md whitespace-nowrap">ดูโปรไฟล์</a>
                <a href="../logout.php" class="btn-danger text-white text-xs sm:text-base px-3 sm:px-5 py-2 rounded-lg font-medium shadow-md whitespace-nowrap">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8">
        <?php if (!$profile_data): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-lg text-center">ไม่พบข้อมูลโปรไฟล์สำหรับ user ID: <?= $user_id_to_view ?></div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
                        <img src="<?= htmlspecialchars($display_profile_pic) ?>" alt="รูปโปรไฟล์" class="w-32 h-32 mx-auto rounded-full object-cover shadow-lg border-4 border-white ">
                        <h1 class="text-2xl font-bold text-gray-900 mt-4 flex items-center justify-center">
                            <?= htmlspecialchars($display_name) ?>
                            <?php if ($is_verified): ?>
                                <span title="บัญชีนี้ได้รับการยืนยันตัวตนแล้ว">
                                    <svg class="ml-2 w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            <?php endif; ?>
                        </h1>
                        <?php if (!empty($display_company_name)): ?>
                            <p class="text-md text-gray-500"><i class="fas fa-building mr-1"></i><?= htmlspecialchars($display_company_name) ?></p>
                        <?php endif; ?>

                        <div class="mt-4 flex justify-center">
                            <?php if ($review_count > 0): ?>
                                <div class="flex items-center space-x-2">
                                    <div class="flex text-yellow-400">
                                        <?php
                                        $full_stars = floor($avg_rating);
                                        $half_star = ($avg_rating - $full_stars) >= 0.5;
                                        $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                                        for ($i = 0; $i < $full_stars; $i++) echo '<i class="fas fa-star"></i>';
                                        if ($half_star) echo '<i class="fas fa-star-half-alt"></i>';
                                        for ($i = 0; $i < $empty_stars; $i++) echo '<i class="far fa-star"></i>';
                                        ?>
                                    </div>
                                    <span class="font-bold text-slate-700"><?= number_format($avg_rating, 1) ?></span>
                                    <span class="text-sm text-slate-500">(<?= $review_count ?> รีวิว)</span>
                                </div>
                            <?php else: ?>
                                <div class="text-sm text-slate-500"><i class="far fa-star mr-1"></i> ยังไม่มีรีวิว</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="font-semibold text-lg text-gradient mb-4">ข้อมูลติดต่อ</h3>
                        <div class="space-y-2 text-gray-600 text-sm">
                            <p><i class="fas fa-envelope fa-fw mr-2 text-slate-400"></i><?= htmlspecialchars($display_email) ?></p>
                            <p><i class="fas fa-phone fa-fw mr-2 text-slate-400"></i><?= htmlspecialchars($display_tel) ?></p>
                        </div>

                        <?php $has_social_links = !empty($profile_data['facebook_url']) || !empty($profile_data['instagram_url']) || !empty($profile_data['tiktok_url']); ?>
                        <?php if ($has_social_links): ?>
                            <div class="border-t my-4"></div>
                            <h3 class="font-semibold text-lg text-gradient mb-4">โซเชียลมีเดีย</h3>
                            <div class="space-y-3 mt-4 text-sm">
                                <?php if (!empty($profile_data['facebook_url'])): ?>
                                    <a href="<?= htmlspecialchars($profile_data['facebook_url']); ?>" target="_blank" class="flex items-center text-gray-600 hover:text-blue-600 hover:underline transition-colors">
                                        <i class="fab fa-facebook-square fa-fw mr-2 text-xl text-blue-600"></i>
                                        <span class="truncate"><?= htmlspecialchars($profile_data['facebook_url']); ?></span>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($profile_data['instagram_url'])): ?>
                                    <a href="<?= htmlspecialchars($profile_data['instagram_url']); ?>" target="_blank" class="flex items-center text-gray-600 hover:text-pink-500 hover:underline transition-colors">
                                        <i class="fab fa-instagram-square fa-fw mr-2 text-xl text-pink-500"></i>
                                        <span class="truncate"><?= htmlspecialchars($profile_data['instagram_url']); ?></span>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($profile_data['tiktok_url'])): ?>
                                    <a href="<?= htmlspecialchars($profile_data['tiktok_url']); ?>" target="_blank" class="flex items-center text-gray-600 hover:text-black hover:underline transition-colors">
                                        <i class="fab fa-tiktok fa-fw mr-2 text-xl text-black"></i>
                                        <span class="truncate"><?= htmlspecialchars($profile_data['tiktok_url']); ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($display_skills)): ?>
                        <div class="bg-white rounded-2xl shadow-xl p-6">
                            <h3 class="font-semibold text-lg text-gradient mb-4">ทักษะ</h3>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($display_skills as $skill): ?>
                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full"><?= htmlspecialchars(trim($skill)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($user_id_to_view == $current_user_id): ?>
                        <div class="bg-white rounded-2xl shadow-xl p-6">
                            <a href="edit_profile.php" class="block text-center w-full bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-lg font-medium shadow-md transition-colors">
                                <i class="fas fa-pencil-alt mr-2"></i>แก้ไขโปรไฟล์
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="lg:col-span-2 space-y-8">

                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h2 class="text-2xl font-semibold text-gradient mb-4">เกี่ยวกับฉัน</h2>
                        <p class="text-gray-700 leading-relaxed prose"><?= nl2br(htmlspecialchars($display_bio)) ?></p>
                    </div>

                    <?php if ($user_id_to_view == $current_user_id): // แสดงเฉพาะเจ้าของโปรไฟล์ 
                    ?>
                        <div class="bg-white rounded-2xl shadow-xl p-6">
                            <h2 class="text-2xl font-semibold text-gradient mb-4">การยืนยันตัวตน</h2>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <?php if ($is_verified): ?>
                                    <div class="flex items-center text-green-600">
                                        <i class="fas fa-check-circle fa-2x mr-4"></i>
                                        <div>
                                            <p class="font-bold">คุณได้รับการยืนยันตัวตนเรียบร้อยแล้ว</p>
                                            <p class="text-sm">บัญชีของคุณมีความน่าเชื่อถือสูง</p>
                                        </div>
                                    </div>
                                <?php elseif ($verification_status === 'pending'): ?>
                                    <div class="flex items-center text-orange-600">
                                        <i class="fas fa-hourglass-half fa-2x mr-4"></i>
                                        <div>
                                            <p class="font-bold">เอกสารของคุณอยู่ระหว่างการตรวจสอบ</p>
                                            <p class="text-sm">โดยปกติจะใช้เวลา 1-3 วันทำการ</p>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-triangle fa-2x mr-4 text-amber-500"></i>
                                        <div>
                                            <p class="font-bold text-gray-800">บัญชีของคุณยังไม่ได้รับการยืนยันตัวตน</p>
                                            <p class="text-sm text-gray-600 mt-1 mb-4">
                                                เพิ่มความน่าเชื่อถือโดยการส่งเอกสารตรวจสอบประวัติอาชญากรรมเพื่อรับเครื่องหมายยืนยันตัวตน
                                            </p>
                                            <a href="submit_verification.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium text-sm shadow-md transition-colors">
                                                <i class="fas fa-shield-alt mr-2"></i>ยืนยันตัวตน / ส่งเอกสาร
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($user_id_to_view == $current_user_id): ?>
                        <div class="bg-white rounded-2xl shadow-xl p-6">
                            <h2 class="text-2xl font-semibold text-gradient mb-4">ข้อมูลการชำระเงิน</h2>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <?php if (!empty($profile_data['payment_qr_code_url']) && !empty($profile_data['bank_name'])): ?>
                                    <div>
                                        <p><strong>เลขที่บัญชี:</strong> <?= htmlspecialchars($profile_data['account_number']); ?></p>
                                        <p>
                                            <strong>ธนาคาร:</strong> <?= htmlspecialchars($profile_data['bank_name']); ?>
                                            <a href="<?= '..' . htmlspecialchars($profile_data['payment_qr_code_url']); ?>" target="_blank" class="ml-2 text-blue-500 hover:underline">
                                                ( <i class="fas fa-qrcode"></i> ดู QR Code )
                                            </a>
                                        </p>
                                    </div>
                                <?php else: ?>
                                    <p class="text-gray-500">ยังไม่มีข้อมูลการชำระเงิน</p>
                                <?php endif; ?>
                                <a href="edit_payment.php" class="mt-3 inline-block bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium text-sm shadow-md transition-colors">
                                    <i class="fas fa-edit mr-2"></i>จัดการข้อมูลการชำระเงิน
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h2 class="text-2xl font-semibold text-gradient mb-4">โพสต์ประกาศงานของคุณ</h2>
                        <?php if (empty($job_postings_for_profile)): ?>
                            <div class="bg-blue-100 text-blue-700 px-4 py-3 rounded-lg text-center">ยังไม่มีงานที่ประกาศ</div>
                        <?php else: ?>
                            <div class="carousel-container relative">
                                <button id="prevBtn" class="carousel-button left"><i class="fas fa-chevron-left"></i></button>
                                <div class="carousel-content p-2" id="carouselContent">
                                    <?php foreach ($job_postings_for_profile as $job): ?>
                                        <div class="card-item flex flex-col">
                                            <?php
                                            $image_source = !empty($job['job_image_path']) && file_exists(htmlspecialchars($job['job_image_path']))
                                                ? htmlspecialchars($job['job_image_path'])
                                                : '../dist/img/pdpa02.jpg';
                                            ?>
                                            <img src="<?= $image_source ?>" alt="ภาพประกอบงาน: <?= htmlspecialchars($job['title']) ?>" class="card-image">
                                            <div class="p-4 md:p-6 flex-grow flex flex-col justify-between">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 line-clamp-2"><?= htmlspecialchars($job['title']) ?></h3>
                                                    <p class="text-sm text-gray-500 mb-2">หมวดหมู่: <?= htmlspecialchars($job['category_name'] ?? 'ไม่ระบุ') ?></p>
                                                    <p class="text-sm text-gray-700 line-clamp-3 font-light"><?= htmlspecialchars($job['description']) ?></p>
                                                </div>
                                                <div class="mt-4">
                                                    <p class="text-lg font-semibold text-green-700">ราคา: <?= htmlspecialchars($job['price_range'] ?? 'สอบถาม') ?></p>
                                                    <p class="text-xs text-gray-500">ประกาศเมื่อ: <?= date('d M Y', strtotime($job['posted_date'])) ?></p>
                                                    <div class="mt-2 flex space-x-2">
                                                        <a href="../job_detail.php?id=<?= $job['post_id'] ?>&type=posting" class="flex-1 btn-primary text-white px-4 py-2 rounded-lg font-medium text-sm shadow-lg text-center">ดูรายละเอียด</a>
                                                        <?php if ($user_id_to_view == $current_user_id): ?>
                                                            <a href="edit_job_post.php?id=<?= $job['post_id'] ?>" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-2 rounded-lg font-medium text-sm shadow-lg transition-colors"><i class="fas fa-pencil-alt"></i></a>
                                                            <a href="delete_job_post.php?id=<?= $job['post_id'] ?>" onclick="confirmDelete(event, <?= $job['post_id'] ?>)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg font-medium text-sm shadow-lg transition-colors"><i class="fas fa-trash"></i></a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button id="nextBtn" class="carousel-button right"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($show_success_popup): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'อัปเดตโปรไฟล์ของคุณเรียบร้อยแล้ว',
                    timer: 2500,
                    showConfirmButton: false
                });
            <?php elseif ($show_no_change_popup): ?>
                Swal.fire({
                    icon: 'info',
                    title: 'ไม่มีการเปลี่ยนแปลง',
                    text: 'ข้อมูลของคุณเป็นปัจจุบันอยู่แล้ว',
                    timer: 2500,
                    showConfirmButton: false
                });
            <?php endif; ?>

            // Carousel script
            const carouselContent = document.getElementById('carouselContent');
            if (carouselContent) {
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');

                function updateButtons() {
                    const {
                        scrollWidth,
                        clientWidth,
                        scrollLeft
                    } = carouselContent;
                    if (scrollWidth <= clientWidth) {
                        prevBtn.style.display = 'none';
                        nextBtn.style.display = 'none';
                        return;
                    }
                    prevBtn.style.display = scrollLeft > 0 ? 'flex' : 'none';
                    nextBtn.style.display = scrollLeft < (scrollWidth - clientWidth - 1) ? 'flex' : 'none';
                }

                prevBtn.addEventListener('click', () => {
                    const cardWidth = carouselContent.querySelector('.card-item').offsetWidth;
                    carouselContent.scrollBy({
                        left: -(cardWidth + 24),
                        behavior: 'smooth'
                    });
                });

                nextBtn.addEventListener('click', () => {
                    const cardWidth = carouselContent.querySelector('.card-item').offsetWidth;
                    carouselContent.scrollBy({
                        left: cardWidth + 24,
                        behavior: 'smooth'
                    });
                });

                carouselContent.addEventListener('scroll', () => setTimeout(updateButtons, 250));
                window.addEventListener('resize', updateButtons);
                updateButtons();
            }
        });

        function confirmDelete(event, postId) {
            event.preventDefault();
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณจะไม่สามารถกู้คืนโพสต์นี้ได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_job_post.php?id=' + postId;
                }
            })
        }
    </script>
</body>

</html>