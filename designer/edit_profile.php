<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pixellink";

$condb = new mysqli($servername, $username, $password, $dbname);
if ($condb->connect_error) {
    die("Connection failed: " . $condb->connect_error);
}
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
$condb->set_charset("utf8");

$message = '';
$error = '';
$changes_made = false;

// --- (แก้ไข) ดึงข้อมูล tiktok_url แทน linkedin_url ---
$sql_fetch = "SELECT u.first_name, u.last_name, u.email, u.phone_number, p.company_name, p.bio, p.skills, p.profile_picture_url, p.facebook_url, p.instagram_url, p.tiktok_url
              FROM users u
              LEFT JOIN profiles p ON u.user_id = p.user_id
              WHERE u.user_id = ?";
$stmt_fetch = $condb->prepare($sql_fetch);
$stmt_fetch->bind_param("i", $user_id);
$stmt_fetch->execute();
$profile = $stmt_fetch->get_result()->fetch_assoc();
$stmt_fetch->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ไม่รับค่า first_name และ last_name จากฟอร์มแล้ว
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $company_name = $_POST['company_name'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $skills = $_POST['skills'] ?? '';

    // --- (แก้ไข) รับข้อมูล tiktok_url แทน linkedin_url ---
    $facebook_url = $_POST['facebook_url'] ?? '';
    $instagram_url = $_POST['instagram_url'] ?? '';
    $tiktok_url = $_POST['tiktok_url'] ?? '';

    // --- (แก้ไข) นำ first_name, last_name ออกจากการตรวจสอบการเปลี่ยนแปลง ---
    if ($email !== $profile['email'] || $phone_number !== $profile['phone_number'] || $company_name !== $profile['company_name'] || $bio !== $profile['bio'] || $skills !== $profile['skills'] || !empty($_FILES['profile_picture']['name']) || $facebook_url !== $profile['facebook_url'] || $instagram_url !== $profile['instagram_url'] || $tiktok_url !== $profile['tiktok_url']) {
        $changes_made = true;
    }

    if ($changes_made) {
        // --- (แก้ไข) นำ first_name, last_name ออกจากการอัปเดต ---
        $sql_users = "UPDATE users SET email = ?, phone_number = ? WHERE user_id = ?";
        $stmt_users = $condb->prepare($sql_users);
        $stmt_users->bind_param("ssi", $email, $phone_number, $user_id);
        $stmt_users->execute();
        $stmt_users->close();

        // Handle profile picture upload
        $profile_picture_path = $profile['profile_picture_url'];
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $upload_dir = '../uploads/profile_pictures/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                if (!empty($profile_picture_path) && strpos($profile_picture_path, 'user_placeholder.jpg') === false && file_exists('..' . $profile_picture_path)) {
                    unlink('..' . $profile_picture_path);
                }
                $profile_picture_path = str_replace('..', '', $destination);
            } else {
                $error = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
            }
        }

        // --- (แก้ไข) ตรวจสอบว่ามีโปรไฟล์อยู่แล้วหรือไม่ ถ้าไม่มีให้ INSERT ถ้ามีให้ UPDATE ---
        $sql_check = "SELECT user_id FROM profiles WHERE user_id = ?";
        $stmt_check = $condb->prepare($sql_check);
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $stmt_check->close();

        if ($result_check->num_rows > 0) {
            // ถ้ามีข้อมูลอยู่แล้ว: อัปเดตข้อมูลเดิม
            $sql_profiles = "UPDATE profiles SET company_name = ?, bio = ?, skills = ?, profile_picture_url = ?, facebook_url = ?, instagram_url = ?, tiktok_url = ? WHERE user_id = ?";
            $stmt_profiles = $condb->prepare($sql_profiles);
            // ตรวจสอบว่า profile_picture_path ไม่ใช่ค่าว่างเปล่าก่อน bind
            $pic_path_to_bind = !empty($profile_picture_path) ? $profile_picture_path : $profile['profile_picture_url'];
            $stmt_profiles->bind_param("sssssssi", $company_name, $bio, $skills, $pic_path_to_bind, $facebook_url, $instagram_url, $tiktok_url, $user_id);
        } else {
            // ถ้ายังไม่มีข้อมูล: เพิ่มข้อมูลใหม่
            $sql_profiles = "INSERT INTO profiles (user_id, company_name, bio, skills, profile_picture_url, facebook_url, instagram_url, tiktok_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_profiles = $condb->prepare($sql_profiles);
            $stmt_profiles->bind_param("isssssss", $user_id, $company_name, $bio, $skills, $profile_picture_path, $facebook_url, $instagram_url, $tiktok_url);
        }

        if (empty($error) && $stmt_profiles->execute()) {
            header("Location: view_profile.php?user_id=" . $user_id . "&update_status=success");
            exit();
        } else {
            $error = $error ?: "เกิดข้อผิดพลาดในการอัปเดตโปรไฟล์: " . $stmt_profiles->error;
        }
        $stmt_profiles->close();
    } else {
        header("Location: view_profile.php?user_id=" . $user_id . "&update_status=nochange");
        exit();
    }
}

$condb->close();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโปรไฟล์ | PixelLink</title>
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

<body class="bg-slate-100">
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
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl p-6 md:p-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">แก้ไขโปรไฟล์</h1>

            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="space-y-6">

                <div class="text-center">
                    <img id="profile_pic_preview" src="<?= !empty($profile['profile_picture_url']) ? '..' . htmlspecialchars($profile['profile_picture_url']) : '../dist/img/logo.png' ?>" alt="รูปโปรไฟล์" class="w-40 h-40 rounded-full object-cover shadow-lg border-4 border-white mx-auto mb-4">
                    <label for="profile_picture" class="cursor-pointer bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium text-sm shadow-md transition-colors">
                        <i class="fas fa-camera mr-2"></i>เปลี่ยนรูปโปรไฟล์
                        <input type="file" id="profile_picture" name="profile_picture" class="hidden" onchange="previewImage(event)">
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-gray-700 font-medium mb-2">ชื่อจริง</label>
                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label for="last_name" class="block text-gray-700 font-medium mb-2">นามสกุล</label>
                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">อีเมล</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="phone_number" class="block text-gray-700 font-medium mb-2">เบอร์โทรศัพท์</label>
                        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($profile['phone_number'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label for="company_name" class="block text-gray-700 font-medium mb-2">บริษัท/สังกัด (ถ้ามี)</label>
                    <input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($profile['company_name'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="bio" class="block text-gray-700 font-medium mb-2">เกี่ยวกับฉัน (Bio)</label>
                    <textarea id="bio" name="bio" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                </div>
                <div>
                    <label for="skills" class="block text-gray-700 font-medium mb-2">ทักษะ (คั่นด้วยเครื่องหมายจุลภาค ,)</label>
                    <input type="text" id="skills" name="skills" value="<?= htmlspecialchars($profile['skills'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="เช่น Photoshop, Illustrator, UI/UX Design">
                </div>

                <div class="border-t pt-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">ลิงก์โซเชียลมีเดีย</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="facebook_url" class="block text-gray-700 font-medium mb-2"><i class="fab fa-facebook-square text-blue-600 mr-2"></i>Facebook URL</label>
                            <input type="url" id="facebook_url" name="facebook_url" value="<?= htmlspecialchars($profile['facebook_url'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.facebook.com/yourprofile">
                        </div>
                        <div>
                            <label for="instagram_url" class="block text-gray-700 font-medium mb-2"><i class="fab fa-instagram-square text-pink-500 mr-2"></i>Instagram URL</label>
                            <input type="url" id="instagram_url" name="instagram_url" value="<?= htmlspecialchars($profile['instagram_url'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.instagram.com/yourprofile">
                        </div>
                        <div>
                            <label for="tiktok_url" class="block text-gray-700 font-medium mb-2"><i class="fab fa-tiktok text-black mr-2"></i>TikTok URL</label>
                            <input type="url" id="tiktok_url" name="tiktok_url" value="<?= htmlspecialchars($profile['tiktok_url'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.tiktok.com/@yourprofile">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <a href="view_profile.php?user_id=<?= $user_id ?>" class="text-gray-600 bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-lg font-medium text-sm transition-colors">ยกเลิก</a>
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg font-medium text-sm shadow-md transition-colors">
                        <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profile_pic_preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    <?php include '../includes/footer.php'; ?>
</body>

</html>