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
    <title>เกี่ยวกับเรา | PixelLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* (CSS ทั้งหมดเหมือนเดิม ไม่ต้องเปลี่ยนแปลง) */
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
// --- [จุดที่แก้ไขหลัก] เพิ่มเงื่อนไขในการเลือก Navbar ---

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

<main class="container mx-auto px-4 py-12">
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center text-gradient">เกี่ยวกับ PixelLink</h1>
        
        <p class="text-lg text-gray-700 leading-relaxed mb-6">
            <strong>PixelLink</strong> คือแพลตฟอร์มที่เชื่อมต่อนักออกแบบกราฟิกฟรีแลนซ์มากความสามารถเข้ากับผู้ว่าจ้างที่กำลังมองหาผลงานสร้างสรรค์คุณภาพสูง เราเชื่อมั่นในพลังของการออกแบบที่ดีและมุ่งมั่นที่จะสร้างชุมชนที่สนับสนุนให้นักออกแบบได้แสดงศักยภาพและเติบโตในสายอาชีพ
        </p>

        <div class="grid md:grid-cols-2 gap-8 text-gray-700 mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-3">ภารกิจของเรา</h2>
                <p class="leading-relaxed">ภารกิจของเราคือการสร้างพื้นที่ที่ปลอดภัยและเชื่อถือได้สำหรับทั้งนักออกแบบและผู้ว่าจ้าง ทำให้กระบวนการจ้างงานเป็นเรื่องง่าย สะดวก และโปร่งใส ตั้งแต่การค้นหานักออกแบบ การเสนอราคา ไปจนถึงการส่งมอบงานและการชำระเงิน</p>
            </div>
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-3">วิสัยทัศน์ของเรา</h2>
                <p class="leading-relaxed">เรามุ่งหวังที่จะเป็นแพลตฟอร์มชั้นนำสำหรับวงการออกแบบกราฟิกในประเทศไทย ที่ซึ่งความคิดสร้างสรรค์ไม่มีขีดจำกัด และเป็นที่ที่นักออกแบบสามารถสร้างรายได้จากความสามารถของตนเองได้อย่างยั่งยืน</p>
            </div>
        </div>

        <div class="text-center">
            <a href="job_listings.php" class="btn-primary text-white px-8 py-3 rounded-lg font-semibold text-lg shadow-lg inline-block">
                ค้นหานักออกแบบเลย
            </a>
        </div>
    </div>
</main>

<?php 
include 'includes/footer.php'; 
?>