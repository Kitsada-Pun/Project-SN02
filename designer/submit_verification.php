<?php
session_start();
require_once '../connect.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

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
// ตรวจสอบว่าเคยส่งเอกสารที่ยังรออนุมัติอยู่หรือไม่
$already_submitted = false;
$check_sql = "SELECT id FROM verification_submissions WHERE user_id = ? AND status = 'pending'";
$stmt_check = $conn->prepare($check_sql);
if ($stmt_check) {
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        $already_submitted = true;
    }
    $stmt_check->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$already_submitted) {
    if (isset($_FILES['verification_doc']) && $_FILES['verification_doc']['error'] === UPLOAD_ERR_OK) {
        
        $upload_dir = '../uploads/verification_docs/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_info = pathinfo($_FILES['verification_doc']['name']);
        $file_extension = strtolower($file_info['extension']);
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = 'verify_' . $_SESSION['user_type'] . '_' . $user_id . '_' . time() . '.' . $file_extension;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['verification_doc']['tmp_name'], $destination)) {
                
                // ========== [เพิ่ม] บันทึกข้อมูลลงฐานข้อมูล ==========
                $document_path = str_replace('../', '/', $destination);
                $insert_sql = "INSERT INTO verification_submissions (user_id, document_path, status) VALUES (?, ?, 'pending')";
                $stmt_insert = $conn->prepare($insert_sql);
                if ($stmt_insert) {
                    $stmt_insert->bind_param("is", $user_id, $document_path);
                    if ($stmt_insert->execute()) {
                        $message = 'ส่งเอกสารสำหรับการยืนยันตัวตนสำเร็จแล้ว! กรุณารอการตรวจสอบจากผู้ดูแลระบบ';
                        $message_type = 'success';
                    } else {
                        $message = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลลงฐานข้อมูล';
                        $message_type = 'error';
                    }
                    $stmt_insert->close();
                } else {
                    $message = 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL';
                    $message_type = 'error';
                }
                // ========== [สิ้นสุดส่วนที่เพิ่ม] ==========

            } else {
                $message = 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์';
                $message_type = 'error';
            }
        } else {
            $message = 'ประเภทไฟล์ไม่ถูกต้อง (อนุญาตเฉพาะ PDF, JPG, JPEG, PNG)';
            $message_type = 'error';
        }
    } else {
        $message = 'กรุณาเลือกไฟล์ที่ต้องการอัปโหลด';
        $message_type = 'error';
    }
}

// ถ้าเคยส่งแล้วให้แสดงข้อความนี้
if ($already_submitted && $message_type !== 'success') {
    $message = 'คุณได้ยื่นเอกสารเพื่อรอการตรวจสอบแล้ว ขณะนี้เอกสารของคุณอยู่ระหว่างดำเนินการ';
    $message_type = 'info';
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันตัวตน | PixelLink</title>
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

        .dragover {
            background-color: #e0f2fe;
            border-color: #3b82f6;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen flex flex-col">
    <?php include '../includes/nav.php'; ?>

    <main class="container mx-auto px-4 py-8 flex-grow flex items-center">
        <div class="w-full max-w-xl mx-auto bg-white rounded-2xl shadow-xl p-6 md:p-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-2 text-center">ยืนยันตัวตนนักออกแบบ</h1>
            <p class="text-center text-gray-500 mb-6">ส่งเอกสารตรวจสอบประวัติอาชญากรรมเพื่อรับเครื่องหมายยืนยันตัวตน</p>

            <?php if ($message && $message_type === 'success'): ?>
                <div class="bg-green-100 text-green-800 p-4 rounded-lg text-center">
                    <p class="font-bold">ส่งเอกสารสำเร็จ!</p>
                    <p><?= htmlspecialchars($message) ?></p>
                    <a href="view_profile.php?user_id=<?= $user_id ?>" class="mt-4 inline-block text-blue-600 hover:underline">กลับไปที่หน้าโปรไฟล์</a>
                </div>
            <?php else: ?>
                <form id="upload-form" action="submit_verification.php" method="POST" enctype="multipart/form-data">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">เอกสารตรวจสอบประวัติอาชญากรรม</label>
                            <div id="drop-zone" class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md transition-colors duration-300">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-file-import fa-3x text-gray-400"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="verification_doc_input" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>อัปโหลดไฟล์</span>
                                            <input id="verification_doc_input" name="verification_doc" type="file" class="sr-only" required accept=".pdf,.png,.jpg,.jpeg">
                                        </label>
                                        <p class="pl-1">หรือลากและวางที่นี่</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, PNG, JPG, JPEG ไม่เกิน 5MB</p>
                                    <p id="file-name-display" class="text-sm font-semibold text-green-600 mt-2"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-4 pt-6 mt-6 border-t">
                        <a href="view_profile.php?user_id=<?= $user_id ?>" class="text-gray-600 bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-lg font-medium text-sm transition-colors">ย้อนกลับ</a>
                        <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg font-medium text-sm shadow-md transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>ส่งเอกสาร
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('verification_doc_input');
        const fileNameDisplay = document.getElementById('file-name-display');
        const uploadForm = document.getElementById('upload-form');

        function updateFileName(file) {
            if (file) {
                fileNameDisplay.textContent = `ไฟล์ที่เลือก: ${file.name}`;
            } else {
                fileNameDisplay.textContent = '';
            }
        }

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                updateFileName(files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                updateFileName(fileInput.files[0]);
            }
        });

        // ========== [เพิ่ม] JavaScript สำหรับ Popup ยืนยันก่อนส่ง ==========
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault(); // หยุดการส่งฟอร์มตามปกติ

            Swal.fire({
                title: 'ยืนยันการส่งเอกสาร?',
                text: "คุณแน่ใจหรือไม่ว่าต้องการส่งเอกสารนี้เพื่อการยืนยันตัวตน",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, ส่งเอกสาร!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ถ้าผู้ใช้ยืนยัน ให้ทำการส่งฟอร์ม
                    e.target.submit();
                }
            })
        });

        <?php if ($message && $message_type === 'error'): ?>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: '<?= addslashes($message) ?>',
            });
        <?php endif; ?>
    </script>
</body>

</html>