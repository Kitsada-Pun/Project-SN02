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
    <title>เงื่อนไขและข้อตกลงการใช้งาน | PixelLink</title>
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
        <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center text-gradient">เงื่อนไขและข้อตกลงการใช้งาน</h1>
        
        <p><strong>ปรับปรุงล่าสุด: 25 กันยายน 2025</strong></p>
        
        <p>ยินดีต้อนรับสู่ PixelLink! โปรดอ่านเงื่อนไขและข้อตกลงการใช้งาน ("เงื่อนไข") เหล่านี้อย่างละเอียดก่อนใช้บริการของเรา การเข้าถึงหรือใช้งานแพลตฟอร์มนี้แสดงว่าคุณยอมรับที่จะผูกพันตามเงื่อนไขเหล่านี้</p>

        <h2>1. การใช้งานแพลตฟอร์ม</h2>
        <p>คุณต้องมีอายุอย่างน้อย 18 ปีจึงจะสามารถสร้างบัญชีและใช้บริการของเราได้ คุณตกลงที่จะให้ข้อมูลที่ถูกต้องและเป็นปัจจุบันเสมอ และรับผิดชอบต่อการรักษารหัสผ่านของคุณให้ปลอดภัย</p>

        <h2>2. บทบาทของผู้ว่าจ้างและนักออกแบบ</h2>
        <p>PixelLink เป็นเพียงแพลตฟอร์มกลางในการเชื่อมต่อ สัญญาการจ้างงานทั้งหมดเป็นข้อตกลงโดยตรงระหว่างผู้ว่าจ้างและนักออกแบบ เราไม่ส่วนรับผิดชอบต่อคุณภาพของงานหรือการชำระเงินใดๆ</p>

        <h2>3. การชำระเงิน</h2>
        <p>ผู้ว่าจ้างตกลงที่จะชำระเงินตามข้อเสนอที่ตกลงกันไว้ผ่านระบบของ PixelLink นักออกแบบจะได้รับเงินเมื่อส่งมอบงานสมบูรณ์และได้รับการอนุมัติจากผู้ว่าจ้างแล้ว</p>
        
        <h2>4. เนื้อหาและการเป็นเจ้าของ</h2>
        <p>นักออกแบบยังคงเป็นเจ้าของลิขสิทธิ์ในผลงานของตน จนกว่าจะได้รับการชำระเงินครบถ้วนและส่งมอบไฟล์งานสุดท้ายให้แก่ผู้ว่าจ้าง ซึ่ง ณ เวลานั้นสิทธิ์ในผลงานจะถูกโอนไปยังผู้ว่าจ้างตามข้อตกลง</p>

        <p><em>ขอขอบคุณที่เลือกใช้บริการ PixelLink!</em></p>
    </div>
</main>

<?php 
include 'includes/footer.php'; 
?>

</body>
</html>