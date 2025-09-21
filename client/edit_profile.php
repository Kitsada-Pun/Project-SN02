<?php
session_start();
require_once '../connect.php';

// 1. ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';

// 2. ดึงข้อมูลโปรไฟล์ปัจจุบันของผู้ว่าจ้าง
$sql_fetch = "SELECT u.first_name, u.last_name, u.email, u.phone_number, p.company_name, p.profile_picture_url
              FROM users u
              LEFT JOIN profiles p ON u.user_id = p.user_id
              WHERE u.user_id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("i", $user_id);
$stmt_fetch->execute();
$profile = $stmt_fetch->get_result()->fetch_assoc();
$stmt_fetch->close();

// 3. ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม (ไม่รับ first_name, last_name)
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $company_name = $_POST['company_name'] ?? '';
    $changes_made = false;

    // ตรวจสอบว่ามีการเปลี่ยนแปลงข้อมูลหรือไม่
    if (($email !== $profile['email']) || ($phone_number !== $profile['phone_number']) || ($company_name !== $profile['company_name']) || !empty($_FILES['profile_picture']['name'])) {
        $changes_made = true;
    }

    if ($changes_made) {
        // อัปเดตตาราง users (เฉพาะข้อมูลที่แก้ไขได้)
        $sql_users = "UPDATE users SET email = ?, phone_number = ? WHERE user_id = ?";
        $stmt_users = $conn->prepare($sql_users);
        $stmt_users->bind_param("ssi", $email, $phone_number, $user_id);
        $stmt_users->execute();
        $stmt_users->close();

        // จัดการการอัปโหลดรูปโปรไฟล์
        $profile_picture_path = $profile['profile_picture_url'];
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $upload_dir = '../uploads/profile_pictures/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                if (!empty($profile_picture_path) && file_exists('..' . $profile_picture_path)) {
                    unlink('..' . $profile_picture_path);
                }
                $profile_picture_path = str_replace('../', '/', $destination);
            } else {
                $error = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
            }
        }

        // อัปเดตตาราง profiles
        $sql_profiles = "UPDATE profiles SET company_name = ?, profile_picture_url = ? WHERE user_id = ?";
        $stmt_profiles = $conn->prepare($sql_profiles);
        $stmt_profiles->bind_param("ssi", $company_name, $profile_picture_path, $user_id);

        if (empty($error) && $stmt_profiles->execute()) {
            header("Location: view_profile_client.php?update_status=success");
            exit();
        } else {
            $error = $error ?: "เกิดข้อผิดพลาด: " . $stmt_profiles->error;
        }
        $stmt_profiles->close();

    } else {
        header("Location: view_profile_client.php?update_status=nochange");
        exit();
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

// ========== [แก้ไข] ตรรกะการแสดงรูปโปรไฟล์ ==========
$raw_pic_path = $profile['profile_picture_url'] ?? '';
$display_profile_pic = '../dist/img/user8.jpg'; // Default image
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
    <title>แก้ไขโปรไฟล์ | PixelLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <div class="space-x-4 flex items-center">
                <span class="font-medium text-slate-700">สวัสดี, <?= htmlspecialchars($loggedInUserName) ?>!</span>
                <!-- <a href="view_profile.php?user_id=<?= $_SESSION['user_id']; ?>" class="btn-primary text-white px-5 py-2 rounded-lg font-medium shadow-md">ดูโปรไฟล์</a> -->
                <a href="../logout.php" class="btn-danger text-white px-5 py-2 rounded-lg font-medium shadow-md">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl p-6 md:p-10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">แก้ไขข้อมูลส่วนตัว</h1>
                <p class="text-gray-500">อัปเดตข้อมูลของคุณให้เป็นปัจจุบันอยู่เสมอ</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 text-center"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form id="edit-profile-form" action="edit_profile.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="text-center">
                    <img id="profile_pic_preview" src="<?= $display_profile_pic ?>" alt="รูปโปรไฟล์" class="w-40 h-40 rounded-full object-cover shadow-lg border-4 border-white mx-auto mb-4">
                    <label for="profile_picture" class="cursor-pointer bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium text-sm shadow-md transition-colors">
                        <i class="fas fa-camera mr-2"></i>เปลี่ยนรูปโปรไฟล์
                        <input type="file" id="profile_picture" name="profile_picture" class="hidden" onchange="previewImage(event)">
                    </label>
                </div>
                
                <hr>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-gray-700 font-medium mb-2">ชื่อจริง</label>
                        <input type="text" id="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label for="last_name" class="block text-gray-700 font-medium mb-2">นามสกุล</label>
                        <input type="text" id="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
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
                    <label for="company_name" class="block text-gray-700 font-medium mb-2">ชื่อบริษัท (ถ้ามี)</label>
                    <input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($profile['company_name'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t">
                    <a href="view_profile_client.php" class="text-gray-600 bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-lg font-medium text-sm transition-colors">ยกเลิก</a>
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg font-medium text-sm shadow-md transition-colors">
                        <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewImage(event) {
            if (event.target.files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(){
                    const output = document.getElementById('profile_pic_preview');
                    output.src = reader.result;
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        }
        
        document.getElementById('edit-profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'ยืนยันการเปลี่ยนแปลง?',
                text: "คุณต้องการบันทึกข้อมูลที่แก้ไขใช่หรือไม่",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, บันทึกเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            })
        });
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>