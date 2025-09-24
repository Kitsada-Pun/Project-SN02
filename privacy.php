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
    <title>นโยบายความเป็นส่วนตัว | PixelLink</title>
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
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-4xl mx-auto prose lg:prose-lg">
        <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center text-gradient">นโยบายความเป็นส่วนตัว</h1>
        
        <p><strong>ปรับปรุงล่าสุด: 25 กันยายน 2025</strong></p>
        
        <p>PixelLink ("เรา") ให้ความสำคัญกับความเป็นส่วนตัวของผู้ใช้งาน ("คุณ") นโยบายนี้อธิบายถึงวิธีที่เรารวบรวม ใช้ และปกป้องข้อมูลส่วนบุคคลของคุณ</p>

        <h2>1. ข้อมูลที่เรารวบรวม</h2>
        <ul>
            <li><strong>ข้อมูลที่คุณให้เราโดยตรง:</strong> เช่น ชื่อ, อีเมล, ข้อมูลโปรไฟล์, และรายละเอียดงาน เมื่อคุณลงทะเบียนหรือใช้งานแพลตฟอร์ม</li>
            <li><strong>ข้อมูลการใช้งาน:</strong> เราอาจรวบรวมข้อมูลเกี่ยวกับการโต้ตอบของคุณกับบริการของเรา เช่น หน้าที่เข้าชม, ลิงก์ที่คลิก</li>
        </ul>

        <h2>2. เราใช้ข้อมูลของคุณอย่างไร</h2>
        <ul>
            <li>เพื่อให้บริการและปรับปรุงแพลตฟอร์มของเรา</li>
            <li>เพื่ออำนวยความสะดวกในการสื่อสารระหว่างผู้ว่าจ้างและนักออกแบบ</li>
            <li>เพื่อดำเนินการชำระเงิน</li>
            <li>เพื่อส่งการแจ้งเตือนและการอัปเดตที่สำคัญ</li>
        </ul>

        <h2>3. การเปิดเผยข้อมูล</h2>
        <p>เราจะไม่ขายหรือให้เช่าข้อมูลส่วนบุคคลของคุณแก่บุคคลที่สาม เราอาจเปิดเผยข้อมูลของคุณแก่ผู้ให้บริการที่เชื่อถือได้ซึ่งช่วยเราในการดำเนินงาน หรือเมื่อกฎหมายกำหนด</p>

        <h2>4. ความปลอดภัยของข้อมูล</h2>
        <p>เราใช้มาตรการรักษาความปลอดภัยที่เหมาะสมเพื่อป้องกันการเข้าถึงหรือการเปิดเผยข้อมูลของคุณโดยไม่ได้รับอนุญาต</p>

        <p>หากคุณมีคำถามเกี่ยวกับนโยบายความเป็นส่วนตัวนี้ โปรดติดต่อเราที่ support@pixellink.com</p>
    </div>
</main>

<?php 
include 'includes/footer.php'; 
?>

</body>
</html>