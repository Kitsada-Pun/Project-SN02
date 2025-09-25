<?php
session_start();
// [เพิ่ม] เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล เพื่อให้ nav_designer และ nav_client ใช้งานได้
require_once 'connect.php'; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ติดต่อเรา | PixelLink</title>
    <link rel="icon" type="image/png" href="dist/img/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        * { font-family: 'Kanit', sans-serif; }
        body { background: linear-gradient(135deg, #f0f4f8 0%, #e8edf3 100%); color: #2c3e50; }
        .text-gradient { background: linear-gradient(45deg, #0a5f97, #0d96d2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-primary { background: linear-gradient(45deg, #0a5f97 0%, #0d96d2 100%); color: white; transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(13, 150, 210, 0.4); }
        .btn-danger { background-color: #ef4444; color: white; }
        .btn-danger:hover { background-color: #dc2626; }
        .pixellink-logo-footer { font-weight: 700; font-size: 2.25rem; background: linear-gradient(45deg, #0a5f97, #0d96d2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .pixellink-logo-footer b { color: #0d96d2; }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

<?php
// --- [เพิ่ม] เงื่อนไขในการเลือก Navbar ---

if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
    // ตรวจสอบประเภทของผู้ใช้ที่ล็อกอิน
    if ($_SESSION['user_type'] === 'designer') {
        include 'includes/nav_designer.php'; // แสดง Navbar สำหรับ Designer
    } elseif ($_SESSION['user_type'] === 'client') {
        include 'includes/nav_client.php'; // แสดง Navbar สำหรับ Client
    } else {
        include 'includes/nav_index.php'; // กรณีมี user_type อื่นๆ ที่ไม่รู้จัก
    }
} else {
    // หากยังไม่ได้ล็อกอิน
    include 'includes/nav_index.php'; // แสดง Navbar สำหรับผู้ใช้ทั่วไป
}
?>

<main class="container mx-auto px-4 py-12 flex-grow">
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-2xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center text-gradient">ติดต่อเรา</h1>

        <p class="text-lg text-gray-700 leading-relaxed mb-8 text-center">
            หากคุณมีคำถาม ข้อเสนอแนะ หรือต้องการความช่วยเหลือเกี่ยวกับการใช้งาน PixelLink โปรดอย่าลังเลที่จะติดต่อเราผ่านช่องทางด้านล่างนี้
        </p>

        <div class="space-y-6 text-gray-800">
            <div class="flex items-center">
                <i class="fas fa-envelope fa-2x w-8 text-blue-500"></i>
                <span class="ml-4 text-lg"><strong>อีเมล:</strong> Kitsada.bu@rmuti.ac.th</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-phone fa-2x w-8 text-blue-500"></i>
                <span class="ml-4 text-lg"><strong>โทรศัพท์:</strong> 095-047-2491 (จันทร์ - ศุกร์, 9:00 - 18:00 น.)</span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-map-marker-alt fa-2x w-8 text-blue-500 mt-1"></i>
                <span class="ml-4 text-lg"><strong>ที่อยู่:</strong> 123 อาคารพิกเซล, ถนนครีเอทีฟ, แขวงดีไซน์, เขตบางกอก, กรุงเทพมหานคร 10110</span>
            </div>
        </div>
    </div>
</main>

<?php 
include 'includes/footer.php'; 
?>

</body>
</html>