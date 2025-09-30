<?php
session_start();
date_default_timezone_set('Asia/Bangkok');

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่ และเป็น designer หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'designer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$home_link = 'main.php';

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

// --- ดึงชื่อผู้ใช้ที่ล็อกอิน ---
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


$message = '';
$message_type = '';

// --- จัดการการลบ QR Code ---
if (isset($_GET['action']) && $_GET['action'] === 'delete_qr') {
    // ดึง URL ของไฟล์เก่าเพื่อลบ
    $sql_get_old_qr = "SELECT payment_qr_code_url FROM profiles WHERE user_id = ?";
    $stmt_get_old = $condb->prepare($sql_get_old_qr);
    $stmt_get_old->bind_param("i", $user_id);
    $stmt_get_old->execute();
    $result_old = $stmt_get_old->get_result();
    if ($old_profile = $result_old->fetch_assoc()) {
        if (!empty($old_profile['payment_qr_code_url']) && file_exists('..' . $old_profile['payment_qr_code_url'])) {
            unlink('..' . $old_profile['payment_qr_code_url']);
        }
    }
    $stmt_get_old->close();

    // อัปเดตฐานข้อมูลให้เป็น NULL
    $sql_delete = "UPDATE profiles SET payment_qr_code_url = NULL, bank_name = NULL, account_number = NULL WHERE user_id = ?";
    $stmt_delete = $condb->prepare($sql_delete);
    $stmt_delete->bind_param("i", $user_id);
    if ($stmt_delete->execute()) {
        $message = 'ลบข้อมูลการชำระเงินสำเร็จแล้ว';
        $message_type = 'success';
    } else {
        $message = 'เกิดข้อผิดพลาดในการลบข้อมูล';
        $message_type = 'error';
    }
    $stmt_delete->close();
}


// --- จัดการการอัปเดตข้อมูล (เมื่อฟอร์มถูกส่ง) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_name = $_POST['bank_name'] ?? '';
    $account_number = $_POST['account_number'] ?? '';
    $qr_code_url_to_update = null;

    // --- ตรรกะการอัปโหลดไฟล์ QR Code ---
    if (isset($_FILES['payment_qr_code']) && $_FILES['payment_qr_code']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/qr_codes/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_info = pathinfo($_FILES['payment_qr_code']['name']);
        $file_extension = strtolower($file_info['extension']);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'jfif'];

        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = 'qr_' . $user_id . '_' . time() . '.' . $file_extension;
            $destination = $upload_dir . $new_filename;

            // ดึง URL ของไฟล์เก่าเพื่อลบ
            $sql_get_old_qr = "SELECT payment_qr_code_url FROM profiles WHERE user_id = ?";
            $stmt_get_old = $condb->prepare($sql_get_old_qr);
            $stmt_get_old->bind_param("i", $user_id);
            $stmt_get_old->execute();
            $result_old = $stmt_get_old->get_result();
            if ($old_profile = $result_old->fetch_assoc()) {
                if (!empty($old_profile['payment_qr_code_url']) && file_exists('..' . $old_profile['payment_qr_code_url'])) {
                    unlink('..' . $old_profile['payment_qr_code_url']);
                }
            }
            $stmt_get_old->close();

            if (move_uploaded_file($_FILES['payment_qr_code']['tmp_name'], $destination)) {
                $qr_code_url_to_update = str_replace('..', '', $destination);
            } else {
                $message = 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์';
                $message_type = 'error';
            }
        } else {
            $message = 'ประเภทไฟล์ไม่ถูกต้อง (อนุญาตเฉพาะ jpg, jpeg, png, gif, jfif)';
            $message_type = 'error';
        }
    }

    // --- อัปเดตฐานข้อมูล ---
    if (empty($message)) {
        if ($qr_code_url_to_update !== null) {
            $sql = "UPDATE profiles SET bank_name = ?, account_number = ?, payment_qr_code_url = ? WHERE user_id = ?";
            $stmt = $condb->prepare($sql);
            $stmt->bind_param("sssi", $bank_name, $account_number, $qr_code_url_to_update, $user_id);
        } else {
            $sql = "UPDATE profiles SET bank_name = ?, account_number = ? WHERE user_id = ?";
            $stmt = $condb->prepare($sql);
            $stmt->bind_param("ssi", $bank_name, $account_number, $user_id);
        }

        if ($stmt->execute()) {
            $message = 'บันทึกข้อมูลสำเร็จแล้ว';
            $message_type = 'success';
        } else {
            $message = 'เกิดข้อผิดพลาด: ' . $stmt->error;
            $message_type = 'error';
        }
        $stmt->close();
    }
}


// ดึงข้อมูลโปรไฟล์ปัจจุบันเพื่อแสดงในฟอร์ม
$sql_profile = "SELECT bank_name, account_number, payment_qr_code_url FROM profiles WHERE user_id = ?";
$stmt_profile = $condb->prepare($sql_profile);
$stmt_profile->bind_param("i", $user_id);
$stmt_profile->execute();
$profile_data = $stmt_profile->get_result()->fetch_assoc();
$stmt_profile->close();
$condb->close();

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลการชำระเงิน | PixelLink</title>
    <link rel="icon" type="image/png" href="../dist/img/favicon.png">
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
    
    <main class="flex-grow">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl p-6 md:p-10">

                <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">จัดการข้อมูลการชำระเงิน</h1>
                
                <form action="edit_payment.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="space-y-6 mb-8">
                        <div>
                            <label for="bank_name" class="block text-gray-700 font-medium mb-2">ชื่อธนาคาร</label>
                            <input type="text" id="bank_name" name="bank_name" value="<?= htmlspecialchars($profile_data['bank_name'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="เช่น ธนาคารกสิกรไทย">
                        </div>
                        <div>
                            <label for="account_number" class="block text-gray-700 font-medium mb-2">เลขที่บัญชี</label>
                            <input type="text" id="account_number" name="account_number" value="<?= htmlspecialchars($profile_data['account_number'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="เช่น 123-4-56789-0">
                        </div>
                    </div>

                    <hr class="my-8">

                    <div>
                        <label class="block text-gray-700 text-center font-medium mb-4">QR Code สำหรับรับเงิน</label>
                        <div class="mx-auto w-full max-w-sm text-center p-4 border-2 border-dashed rounded-lg">
                            <div class="flex justify-center mb-4">
                                <?php if (!empty($profile_data['payment_qr_code_url'])): ?>
                                    <img id="qr_preview" class="h-64 w-64 object-contain rounded-md" src="<?= '..' . htmlspecialchars($profile_data['payment_qr_code_url']) ?>" alt="Current QR Code">
                                <?php else: ?>
                                    <div id="qr_placeholder" class="h-64 w-64 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                                        <i class="fas fa-qrcode text-6xl"></i>
                                    </div>
                                    <img id="qr_preview" class="h-64 w-64 object-contain rounded-md hidden" alt="QR Code Preview">
                                <?php endif; ?>
                            </div>
                            <label class="block">
                                <span class="sr-only">Choose QR Code</span>
                                <input type="file" name="payment_qr_code" onchange="previewQR(event)" class="block w-full text-sm text-slate-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100" />
                            </label>
                        </div>
                         <?php if (!empty($profile_data['payment_qr_code_url'])): ?>
                            <div class="text-center">
                                <a href="?action=delete_qr" onclick="return confirm('คุณต้องการลบข้อมูลการชำระเงินและ QR Code นี้ใช่หรือไม่?');" class="mt-2 inline-block text-sm text-red-600 hover:text-red-800">
                                   <i class="fas fa-trash-alt mr-1"></i> ลบข้อมูลการชำระเงินและ QR Code
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8 border-t pt-6">
                        <a href="view_profile.php?user_id=<?= $user_id ?>" class="text-gray-600 bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-lg font-medium text-sm transition-colors">ย้อนกลับ</a>
                        <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg font-medium text-sm shadow-md transition-colors">
                            <i class="fas fa-save mr-2"></i>บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <script>
    function previewQR(event) {
        if (event.target.files.length > 0) {
            const reader = new FileReader();
            const output = document.getElementById('qr_preview');
            const placeholder = document.getElementById('qr_placeholder');
            
            reader.onload = function(){
                if(placeholder) {
                    placeholder.classList.add('hidden');
                }
                output.src = reader.result;
                output.classList.remove('hidden');
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    }
    
    <?php if (!empty($message)): ?>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '<?= $message_type ?>',
            title: '<?= $message_type === "success" ? "สำเร็จ!" : "เกิดข้อผิดพลาด!" ?>',
            text: '<?= addslashes($message) ?>',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'edit_payment.php';
            }
        });
    });
    <?php endif; ?>
    </script>
    
    <?php
    include '../includes/footer.php';
    ?>
</body>
</html>