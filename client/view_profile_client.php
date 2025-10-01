<?php
session_start();
require_once '../connect.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$profile_data = null;
$job_stats = [
    'completed' => 0,
    'cancelled' => 0,
    'total_spent' => 0,
    'avg_rating' => 0
];
$verification_status = 'not_submitted';
// ดึงข้อมูลโปรไฟล์ของผู้ว่าจ้าง
$sql_profile = "SELECT u.first_name, u.last_name, u.email, u.phone_number, u.username, u.registration_date, u.is_verified, p.company_name, p.profile_picture_url
                FROM users u
                LEFT JOIN profiles p ON u.user_id = p.user_id
                WHERE u.user_id = ?";
$stmt_profile = $conn->prepare($sql_profile);
if ($stmt_profile) {
    $stmt_profile->bind_param("i", $user_id);
    $stmt_profile->execute();
    $profile_data = $stmt_profile->get_result()->fetch_assoc();
    $stmt_profile->close();
}
// ตรวจสอบสถานะการยื่นเอกสาร
if (!$profile_data['is_verified']) { // ตรวจสอบต่อเมื่อยังไม่ได้รับการยืนยันตัวตน
    $sql_verify_status = "SELECT status FROM verification_submissions WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1";
    $stmt_verify = $conn->prepare($sql_verify_status);
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
if ($profile_data) {
    // ========== (แก้ไข) ปรับปรุง SQL เพื่อดึงสถิติใหม่ทั้งหมด ==========

    // 1. ดึงจำนวนงานที่เสร็จและยกเลิก
    $sql_job_counts = "SELECT 
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_jobs,
                        SUM(CASE WHEN status IN ('rejected') THEN 1 ELSE 0 END) AS cancelled_jobs
                       FROM client_job_requests 
                       WHERE client_id = ?";
    $stmt_job_counts = $conn->prepare($sql_job_counts);
    if ($stmt_job_counts) {
        $stmt_job_counts->bind_param("i", $user_id);
        $stmt_job_counts->execute();
        $job_counts_result = $stmt_job_counts->get_result()->fetch_assoc();
        $job_stats['completed'] = $job_counts_result['completed_jobs'] ?? 0;
        $job_stats['cancelled'] = $job_counts_result['cancelled_jobs'] ?? 0;
        $stmt_job_counts->close();
    }

    // 2. ดึงยอดใช้จ่ายทั้งหมดจาก contract ที่เสร็จสมบูรณ์แล้ว
    $sql_spending = "SELECT SUM(agreed_price) as total_spent FROM contracts WHERE client_id = ? AND contract_status = 'completed'";
    $stmt_spending = $conn->prepare($sql_spending);
    if ($stmt_spending) {
        $stmt_spending->bind_param("i", $user_id);
        $stmt_spending->execute();
        $spending_result = $stmt_spending->get_result()->fetch_assoc();
        $job_stats['total_spent'] = $spending_result['total_spent'] ?? 0;
        $stmt_spending->close();
    }

    // 3. ดึงคะแนนรีวิวเฉลี่ยที่เคยให้
    $sql_rating = "SELECT AVG(rating) as avg_rating FROM reviews WHERE reviewer_id = ?";
    $stmt_rating = $conn->prepare($sql_rating);
    if ($stmt_rating) {
        $stmt_rating->bind_param("i", $user_id);
        $stmt_rating->execute();
        $rating_result = $stmt_rating->get_result()->fetch_assoc();
        $job_stats['avg_rating'] = $rating_result['avg_rating'] ?? 0;
        $stmt_rating->close();
    }
    // ตรวจสอบสถานะการยื่นเอกสารล่าสุด
    // จะตรวจสอบก็ต่อเมื่อผู้ใช้ยังไม่ได้รับการยืนยันตัวตน (is_verified = 0)
    if (!$profile_data['is_verified']) {
        $sql_verify_status = "SELECT status FROM verification_submissions WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1";
        $stmt_verify = $conn->prepare($sql_verify_status);
        if ($stmt_verify) {
            $stmt_verify->bind_param("i", $user_id);
            $stmt_verify->execute();
            $verify_result = $stmt_verify->get_result()->fetch_assoc();
            // ถ้ามีข้อมูลและสถานะเป็น pending ให้เปลี่ยนค่าตัวแปร
            if ($verify_result && $verify_result['status'] === 'pending') {
                $verification_status = 'pending';
            }
            $stmt_verify->close();
        }
    }
}

$conn->close();

$loggedInUserName = $_SESSION['username'] ?? '';
if (empty($loggedInUserName)) {
    $sql_user = "SELECT first_name, last_name FROM users WHERE user_id = ?";
    $stmt_user = $conn->prepare($sql_user);
    if ($stmt_user) {
        $stmt_user->bind_param("i", $current_user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if ($user_info = $result_user->fetch_assoc()) {
            $loggedInUserName = $user_info['first_name'] . ' ' . $user_info['last_name'];
        }
        $stmt_user->close();
    }
}

// กำหนดค่าเริ่มต้นสำหรับแสดงผล
$display_name = trim(($profile_data['first_name'] ?? '') . ' ' . ($profile_data['last_name'] ?? '')) ?: ($profile_data['username'] ?? 'ไม่ระบุชื่อ');
$display_email = $profile_data['email'] ?? 'ไม่ระบุอีเมล';
$display_tel = $profile_data['phone_number'] ?? 'ไม่ระบุเบอร์โทรศัพท์';
$display_company_name = $profile_data['company_name'] ?? 'ไม่ระบุบริษัท';
$registration_date = isset($profile_data['registration_date']) ? date("j F Y", strtotime($profile_data['registration_date'])) : '-';
$is_verified = $profile_data['is_verified'] ?? 0;

$raw_pic_path = $profile_data['profile_picture_url'] ?? '';
$display_profile_pic = '../dist/img/logo.png';
if (!empty($raw_pic_path)) {
    $correct_path = ltrim($raw_pic_path, '/');
    if (file_exists('../' . $correct_path)) {
        $display_profile_pic = '../' . htmlspecialchars($correct_path);
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของคุณ | PixelLink</title>
    <link rel="icon" type="image/png" href="../dist/img/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        * {
            font-family: 'Kanit', sans-serif;
            font-style: normal;
            font-weight: 400;
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
    </style>
</head>

<body class="bg-slate-100 min-h-screen">

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

    <main class="container mx-auto px-4 py-8">
        <?php if (!$profile_data): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-lg text-center">ไม่พบข้อมูลโปรไฟล์</div>
        <?php else: ?>
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-8">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                        <img src="<?= $display_profile_pic ?>" alt="รูปโปรไฟล์" class="w-32 h-32 sm:w-40 sm:h-40 rounded-full object-cover shadow-lg border-4 border-white flex-shrink-0">
                        <div class="text-center sm:text-left flex-grow">
                            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 flex items-center justify-center sm:justify-start">
                                <?= htmlspecialchars($display_name) ?>
                                <?php if ($is_verified): ?>
                                    <span title="บัญชีนี้ได้รับการยืนยันตัวตนแล้ว">
                                        <svg class="ml-2 w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                <?php endif; ?>
                            </h1>
                            <p class="text-lg text-gray-500"><?= htmlspecialchars($profile_data['username']) ?></p>
                            <p class="text-md text-gray-600 mt-2"><i class="fas fa-building mr-2 text-gray-400"></i><?= htmlspecialchars($display_company_name) ?></p>
                            <p class="text-sm text-gray-400 mt-2">สมาชิกตั้งแต่: <?= $registration_date ?></p>
                            <div class="mt-4">
                                <a href="edit_profile.php" class="inline-block bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-lg font-medium text-sm shadow-md transition-colors">
                                    <i class="fas fa-pencil-alt mr-2"></i>แก้ไขข้อมูลส่วนตัว
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2 space-y-8">
                        <div class="bg-white rounded-2xl shadow-xl p-6">
                            <h2 class="text-2xl font-semibold text-gradient mb-4">ข้อมูลการติดต่อ</h2>
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <i class="fas fa-envelope fa-fw text-gray-400 mt-1 mr-4"></i>
                                    <div>
                                        <p class="font-semibold text-gray-700">อีเมล</p>
                                        <p class="text-gray-600"><?= htmlspecialchars($display_email) ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-phone fa-fw text-gray-400 mt-1 mr-4"></i>
                                    <div>
                                        <p class="font-semibold text-gray-700">เบอร์โทรศัพท์</p>
                                        <p class="text-gray-600"><?= htmlspecialchars($display_tel) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-xl p-6">
                            <h2 class="text-2xl font-semibold text-gradient mb-4">การยืนยันตัวตน</h2>
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

                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h2 class="text-2xl font-semibold text-gradient mb-4">ภาพรวมกิจกรรม</h2>
                        <div class="space-y-6">
                            <div class="flex items-center">
                                <i class="fas fa-coins fa-2x w-8 text-center text-amber-500 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">ยอดใช้จ่ายทั้งหมด</p>
                                    <p class="text-gray-800 text-2xl font-bold"><?= number_format($job_stats['total_spent'], 2) ?> <span class="text-base font-normal">บาท</span></p>
                                </div>
                            </div>
                            <hr>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle fa-2x w-8 text-center text-green-500 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">งานที่เสร็จสิ้น</p>
                                    <p class="text-green-600 text-3xl font-bold"><?= $job_stats['completed'] ?></p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-times-circle fa-2x w-8 text-center text-red-500 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">งานที่ยกเลิก</p>
                                    <p class="text-red-600 text-3xl font-bold"><?= $job_stats['cancelled'] ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="flex items-center">
                                <i class="fas fa-star-half-alt fa-2x w-8 text-center text-yellow-500 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">คะแนนเฉลี่ยที่รีวิว</p>
                                    <p class="text-yellow-600 text-3xl font-bold"><?= number_format($job_stats['avg_rating'], 1) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const updateStatus = urlParams.get('update_status');

            if (updateStatus === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'อัปเดตโปรไฟล์ของคุณเรียบร้อยแล้ว',
                    timer: 2500,
                    showConfirmButton: false
                });
            } else if (updateStatus === 'nochange') {
                Swal.fire({
                    icon: 'info',
                    title: 'ไม่มีการเปลี่ยนแปลง',
                    text: 'ข้อมูลของคุณเป็นปัจจุบันอยู่แล้ว',
                    timer: 2500,
                    showConfirmButton: false
                });
            }

            // ลบ query string ออกจาก URL เพื่อไม่ให้ popup แสดงอีกเมื่อรีเฟรช
            if (updateStatus) {
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({
                    path: newUrl
                }, '', newUrl);
            }
        });
    </script>
</body>

</html>