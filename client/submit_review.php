<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

require_once '../connect.php';

if (!isset($_GET['request_id']) || !is_numeric($_GET['request_id'])) {
    header("Location: my_requests.php");
    exit();
}

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

$request_id = $_GET['request_id'];
$client_id = $_SESSION['user_id'];

// ดึงข้อมูลงานและนักออกแบบ
$sql = "SELECT cjr.title, c.contract_id, u.user_id as designer_id, CONCAT(u.first_name, ' ', u.last_name) as designer_name
        FROM client_job_requests cjr
        JOIN contracts c ON cjr.request_id = c.request_id
        JOIN users u ON cjr.designer_id = u.user_id
        WHERE cjr.request_id = ? AND cjr.client_id = ? AND cjr.status = 'completed'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $request_id, $client_id);
$stmt->execute();
$result = $stmt->get_result();
$job_info = $result->fetch_assoc();


if (!$job_info) {
    echo "ไม่พบข้อมูลงาน หรือ งานยังไม่เสร็จสมบูรณ์";
    exit();
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ให้คะแนนและรีวิว</title>
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
        .rating-stars label { color: #d1d5db; transition: color 0.2s; }
        .rating-stars input:checked ~ label,
        .rating-stars label:hover,
        .rating-stars label:hover ~ label { color: #f59e0b; }
    </style>
</head>
<body class="bg-gray-100">
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
    <div class="container mx-auto max-w-2xl px-4 py-8">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold mb-2">ให้คะแนนและรีวิว</h1>
            <p class="text-gray-600 mb-1">สำหรับงาน: <span class="font-semibold"><?= htmlspecialchars($job_info['title']) ?></span></p>
            <p class="text-gray-600 mb-6">นักออกแบบ: <span class="font-semibold"><?= htmlspecialchars($job_info['designer_name']) ?></span></p>

            <form action="process_review.php" method="POST">
                <input type="hidden" name="request_id" value="<?= $request_id ?>">
                <input type="hidden" name="contract_id" value="<?= $job_info['contract_id'] ?>">
                <input type="hidden" name="designer_id" value="<?= $job_info['designer_id'] ?>">

                <div class="mb-6">
                    <label class="block text-gray-700 text-lg font-bold mb-2">คะแนนความพึงพอใจ</label>
                    <div class="rating-stars flex flex-row-reverse justify-end text-4xl cursor-pointer">
                        <input type="radio" id="star5" name="rating" value="5" class="hidden" required/><label for="star5" title="ยอดเยี่ยม">★</label>
                        <input type="radio" id="star4" name="rating" value="4" class="hidden"/><label for="star4" title="ดีมาก">★</label>
                        <input type="radio" id="star3" name="rating" value="3" class="hidden"/><label for="star3" title="ปานกลาง">★</label>
                        <input type="radio" id="star2" name="rating" value="2" class="hidden"/><label for="star2" title="พอใช้">★</label>
                        <input type="radio" id="star1" name="rating" value="1" class="hidden"/><label for="star1" title="ควรปรับปรุง">★</label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="comment" class="block text-gray-700 text-lg font-bold mb-2">ความคิดเห็นเพิ่มเติม</label>
                    <textarea name="comment" id="comment" rows="5" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="เล่าประสบการณ์ของคุณ..."></textarea>
                </div>

                <div class="flex items-center justify-end">
                    <a href="my_requests.php" class="text-gray-600 mr-4">ยกเลิก</a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">ส่งรีวิว</button>
                </div>
            </form>
        </div>
    </div>
</body>
<?php include '../includes/footer.php'; ?>
</html>