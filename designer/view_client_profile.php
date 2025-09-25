<?php
session_start();
// --- [แก้ไข] อนุญาตให้นักออกแบบที่ล็อกอินแล้วเท่านั้นที่สามารถเข้าดูหน้านี้ได้ ---
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    header("Location: ../login.php");
    exit();
}

require_once '../connect.php';

// --- ตรวจสอบว่ามี user_id ส่งมาใน URL หรือไม่ ---
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    // หากไม่มี ให้กลับไปหน้าหลักของ designer
    header("Location: main.php");
    exit();
}

$client_id = $_GET['user_id'];
$profile_data = null;
$job_history = [];

$loggedInUserName = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Designer';
// --- ดึงชื่อผู้ใช้ที่ล็อกอิน ---
if (isset($_SESSION['user_id'])) {
    $loggedInUserName = $_SESSION['username'] ?? $_SESSION['full_name'] ?? '';
    if (empty($loggedInUserName)) {
        $user_id = $_SESSION['user_id'];
        $sql_user = "SELECT first_name, last_name FROM users WHERE user_id = ?";
        $stmt_user = $conn->prepare($sql_user);
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

// --- ดึงข้อมูลโปรไฟล์ของผู้ว่าจ้าง (Client) ---
$sql_profile = "
    SELECT u.user_id, u.username, u.first_name, u.last_name, u.email, u.registration_date, u.is_verified,
           p.company_name, p.profile_picture_url
    FROM users u
    LEFT JOIN profiles p ON u.user_id = p.user_id
    WHERE u.user_id = ? AND u.user_type = 'client'";

$stmt_profile = $conn->prepare($sql_profile);
if ($stmt_profile) {
    $stmt_profile->bind_param("i", $client_id);
    $stmt_profile->execute();
    $result_profile = $stmt_profile->get_result();
    if ($result_profile->num_rows === 1) {
        $profile_data = $result_profile->fetch_assoc();
    } else {
        // หากไม่พบผู้ใช้งานหรือผู้ใช้ไม่ใช่ Client
        echo "ไม่พบโปรไฟล์ผู้ว่าจ้าง";
        exit();
    }
    $stmt_profile->close();
}

// --- [แก้ไข] ดึงประวัติการจ้างงานเฉพาะที่เกี่ยวข้องกับ Designer คนปัจจุบัน ---
$sql_jobs = "
    SELECT 
        cjr.title, 
        c.end_date AS completed_date, 
        r.rating, 
        r.comment
    FROM client_job_requests cjr
    JOIN contracts c ON cjr.request_id = c.request_id
    LEFT JOIN reviews r ON c.contract_id = r.contract_id AND r.reviewer_id = cjr.client_id
    WHERE cjr.client_id = ? AND cjr.designer_id = ? AND cjr.status = 'completed'
    ORDER BY c.end_date DESC";

$stmt_jobs = $conn->prepare($sql_jobs);
if ($stmt_jobs) {
    $stmt_jobs->bind_param("ii", $client_id, $_SESSION['user_id']);
    $stmt_jobs->execute();
    $result_jobs = $stmt_jobs->get_result();
    $job_history = $result_jobs->fetch_all(MYSQLI_ASSOC);
    $stmt_jobs->close();
}

$conn->close();

$default_profile_pic = '../dist/img/avatar.png';
$profile_picture = (!empty($profile_data['profile_picture_url']) && file_exists('..' . $profile_data['profile_picture_url'])) ? '..' . $profile_data['profile_picture_url'] : $default_profile_pic;
?>
<!DOCTYPE html>
<html lang="th" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ผู้ว่าจ้าง - <?= htmlspecialchars($profile_data['first_name']) ?></title>
    <link rel="icon" type="image/png" href="../dist/img/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&display=swap" rel="stylesheet">

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

        .verified-badge-svg {
            width: 1.25rem;
            /* 20px */
            height: 1.25rem;
            /* 20px */
            margin-left: 0.25rem;
            /* 4px */
            vertical-align: middle;
            display: inline-block;
            /* ทำให้จัดตำแหน่งได้ง่ายขึ้น */
        }
    </style>

</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    <nav class="bg-white/80 backdrop-blur-sm p-4 shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <a href="main.php">
                <img src="../dist/img/logo.png" alt="PixelLink Logo" class="h-12 transition-transform hover:scale-105">
            </a>
            <div class="space-x-4 flex items-center">
                <span class="font-medium text-slate-700">สวัสดี, <?= htmlspecialchars($loggedInUserName) ?>!</span>
                <a href="view_profile.php?user_id=<?= $_SESSION['user_id']; ?>" class="btn-primary text-white px-5 py-2 rounded-lg font-medium shadow-md">ดูโปรไฟล์</a>
                <a href="../logout.php" class="btn-danger text-white px-5 py-2 rounded-lg font-medium shadow-md">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-cover bg-center h-40" style="background-image: url('../dist/img/cover.png');"></div>
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-center sm:items-end -mt-20">
                    <img src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture" class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                    <div class="mt-4 sm:mt-0 sm:ml-6">
                        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                            <?= htmlspecialchars($profile_data['first_name'] . ' ' . $profile_data['last_name']) ?>
                            <?php if ($profile_data['is_verified']) : ?>
                                <span class="ml-2 text-blue-500" title="ยืนยันตัวตนแล้ว"><i class="fas fa-check-circle"></i></span>
                            <?php endif; ?>
                        </h1>
                        <p class="text-gray-600">
                            <?= !empty($profile_data['company_name']) ? htmlspecialchars($profile_data['company_name']) : 'ผู้ว่าจ้างอิสระ' ?>
                        </p>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">ข้อมูลผู้ว่าจ้าง</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                        <div><i class="fas fa-user w-6 text-gray-400"></i> <strong>ชื่อผู้ใช้:</strong> <?= htmlspecialchars($profile_data['username']) ?></div>
                        <div><i class="fas fa-envelope w-6 text-gray-400"></i> <strong>อีเมล:</strong> <?= htmlspecialchars($profile_data['email']) ?></div>
                        <div><i class="fas fa-calendar-alt w-6 text-gray-400"></i> <strong>วันที่ลงทะเบียน:</strong> <?= date('d M Y', strtotime($profile_data['registration_date'])) ?></div>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">ประวัติการจ้างงานและรีวิว (ที่ทำร่วมกับคุณ)</h2>
                    <?php if (empty($job_history)) : ?>
                        <p class="text-gray-500">ยังไม่มีประวัติการจ้างงานที่เสร็จสมบูรณ์</p>
                    <?php else : ?>
                        <div class="space-y-4">
                            <?php foreach ($job_history as $job) : ?>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <h3 class="font-bold text-gray-800"><?= htmlspecialchars($job['title']) ?></h3>
                                    <?php if (!empty($job['rating'])) : ?>
                                        <div class="flex items-center my-1">
                                            <div class="flex text-yellow-400">
                                                <?php
                                                $full_stars = floor($job['rating']);
                                                $half_star = ($job['rating'] - $full_stars) >= 0.5;
                                                $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                                                ?>
                                                <?php for ($i = 0; $i < $full_stars; $i++) echo '<i class="fas fa-star"></i>'; ?>
                                                <?php if ($half_star) echo '<i class="fas fa-star-half-alt"></i>'; ?>
                                                <?php for ($i = 0; $i < $empty_stars; $i++) echo '<i class="far fa-star"></i>'; ?>
                                            </div>
                                            <span class="ml-2 text-sm font-bold text-gray-700"><?= number_format($job['rating'], 1) ?></span>
                                        </div>
                                        <p class="text-gray-600 text-sm italic">"<?= htmlspecialchars($job['comment']) ?>"</p>
                                    <?php else : ?>
                                        <p class="text-sm text-gray-400 mt-1">ยังไม่มีรีวิวสำหรับงานนี้</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>