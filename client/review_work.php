<?php
session_start();
date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

require_once '../connect.php';

$client_id = $_SESSION['user_id'];
$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;

if ($request_id === 0) {
    die("ไม่พบคำขอจ้างงาน");
}

// ดึงข้อมูลงาน, นักออกแบบ, และไฟล์ที่ส่งมา
$sql = "
    SELECT 
        cjr.request_id,
        cjr.title,
        cjr.description,
        cjr.attachment_path AS draft_file_path,
        cjr.status,
        cjr.revision_count,
        u.user_id AS designer_id,
        CONCAT(u.first_name, ' ', u.last_name) AS designer_name,
        p.profile_picture_url AS designer_pfp,
        ja.offered_price
    FROM client_job_requests cjr
    JOIN users u ON cjr.designer_id = u.user_id
    LEFT JOIN profiles p ON u.user_id = p.user_id
    LEFT JOIN job_applications ja ON cjr.request_id = ja.request_id AND ja.status = 'accepted'
    WHERE cjr.request_id = ? AND cjr.client_id = ? 
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $request_id, $client_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$request || $request['status'] !== 'draft_submitted') {
    die("ไม่พบข้อมูลงาน หรือสถานะงานไม่ถูกต้อง");
}

?>
<!DOCTYPE html>
<html lang="th" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบงาน - <?= htmlspecialchars($request['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Kanit', sans-serif;
        }

        .action-card {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease-in-out;
        }

        .file-preview {
            max-height: 70vh;
        }
    </style>
</head>

<body class="bg-gray-100">

    <?php include '../includes/nav.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-4">ตรวจสอบงานฉบับร่าง</h1>
                <p class="text-sm text-gray-500 mb-6">นักออกแบบได้ส่งไฟล์งานฉบับร่างมาให้คุณตรวจสอบแล้ว</p>

                <div class="bg-gray-200 rounded-lg flex items-center justify-center p-4 min-h-[400px]">
                    <?php if (!empty($request['draft_file_path']) && file_exists($request['draft_file_path'])) : ?>
                        <img src="<?= htmlspecialchars($request['draft_file_path']) ?>" alt="Draft Preview" class="max-w-full max-h-[70vh] rounded-md object-contain">
                    <?php else : ?>
                        <p class="text-gray-500">ไม่พบไฟล์ตัวอย่าง</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">รายละเอียดโปรเจกต์</h2>
                    <div class="space-y-3 text-sm">
                        <p class="text-gray-600"><strong>ชื่องาน:</strong><br><span class="text-gray-800"><?= htmlspecialchars($request['title']) ?></span></p>
                        <p class="text-gray-600"><strong>นักออกแบบ:</strong><br><span class="text-blue-600 font-semibold"><?= htmlspecialchars($request['designer_name']) ?></span></p>
                        <p class="text-gray-600"><strong>ราคาที่ตกลง:</strong><br><span class="text-green-600 font-bold text-lg">฿<?= number_format($request['offered_price'], 2) ?></span></p>
                    </div>

                    <hr class="my-6">

                    <h3 class="text-lg font-bold text-gray-800 mb-4">ดำเนินการต่อ</h3>
                    <div class="space-y-3">
                        <button id="accept-work-btn" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-all">
                            <i class="fa-solid fa-check-circle"></i>
                            ยอมรับงานและชำระเงินส่วนที่เหลือ
                        </button>

                        <?php
                        $revisions_left = 2 - $request['revision_count'];
                        if ($revisions_left > 0) :
                        ?>
                            <button id="request-revision-btn" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-yellow-500 text-white rounded-lg font-semibold hover:bg-yellow-600 transition-all">
                                <i class="fa-solid fa-pencil-alt"></i>
                                ขอแก้ไขงาน
                            </button>
                            <p class="text-xs text-center text-gray-500">คุณสามารถขอแก้ไขงานได้อีก <?= $revisions_left ?> ครั้ง</p>
                        <?php else: ?>
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
                                <i class="fa-solid fa-times-circle"></i>
                                ใช้สิทธิ์แก้ไขครบแล้ว
                            </button>
                            <p class="text-xs text-center text-gray-500">กรุณากดยอมรับงานเพื่อดำเนินการต่อ</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const requestId = <?= $request_id ?>;

            // 1. จัดการการคลิกปุ่ม "ยอมรับงาน"
            $('#accept-work-btn').on('click', function() {
                Swal.fire({
                    title: 'ยืนยันการยอมรับงาน?',
                    text: "ระบบจะนำคุณไปยังหน้าชำระเงินส่วนที่เหลือ",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'ใช่, ยอมรับงาน',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ส่ง request ไปยัง server เพื่อเปลี่ยนสถานะ
                        $.ajax({
                            url: 'action_review.php',
                            method: 'POST',
                            data: {
                                action: 'accept_draft',
                                request_id: requestId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire('ยอมรับงานแล้ว!', 'กำลังนำคุณไปยังหน้าชำระเงิน', 'success').then(() => {
                                        window.location.href = `final_payment.php?request_id=${requestId}`;
                                    });
                                } else {
                                    Swal.fire('เกิดข้อผิดพลาด', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            });

            // 2. จัดการการคลิกปุ่ม "ขอแก้ไขงาน"
            $('#request-revision-btn').on('click', function() {
                Swal.fire({
                    title: 'ระบุรายละเอียดที่ต้องการแก้ไข',
                    input: 'textarea',
                    inputPlaceholder: 'กรุณาอธิบายรายละเอียดที่ต้องการแก้ไขให้ชัดเจน...',
                    inputAttributes: {
                        'aria-label': 'Type your message here'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'ส่งข้อความ',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#F59E0B',
                    preConfirm: (text) => {
                        if (!text) {
                            Swal.showValidationMessage('กรุณาระบุรายละเอียดการแก้ไข');
                        }
                        return text;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'action_review.php',
                            method: 'POST',
                            data: {
                                action: 'request_revision',
                                request_id: requestId,
                                message: result.value
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire('ส่งคำขอแก้ไขแล้ว!', 'ระบบได้แจ้งให้นักออกแบบทราบแล้ว', 'success').then(() => {
                                        window.location.href = 'my_requests.php';
                                    });
                                } else {
                                    Swal.fire('เกิดข้อผิดพลาด', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>

</body>

</html>