<?php
// (โค้ด PHP ส่วนบนของคุณจะยังคงอยู่เหมือนเดิม)
// ...
$loggedInUserName = $_SESSION['username'] ?? $_SESSION['full_name'] ?? 'Designer';
if (isset($_SESSION['user_id']) && empty($_SESSION['full_name'])) {
    $user_id = $_SESSION['user_id'];
    // ... (โค้ดดึงชื่อจาก DB ของคุณ) ...
}
?>

<nav class="bg-white/80 backdrop-blur-sm p-4 shadow-md sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <a href="designer/main.php">
            <img src="dist/img/logo.png" alt="PixelLink Logo" class="h-12 transition-transform hover:scale-105">
        </a>
        
        <div class="space-x-2 sm:space-x-4 flex items-center flex-nowrap">
            
            <span class="font-medium text-slate-700 text-xs sm:text-base whitespace-nowrap">
                สวัสดี, <?= htmlspecialchars($loggedInUserName) ?>!
            </span>

            <a href="view_profile.php?user_id=<?= $_SESSION['user_id']; ?>" class="btn-primary text-white text-xs sm:text-base px-3 sm:px-5 py-2 rounded-lg font-medium shadow-md whitespace-nowrap">ดูโปรไฟล์</a>
            <a href="logout.php" class="btn-danger text-white text-xs sm:text-base px-3 sm:px-5 py-2 rounded-lg font-medium shadow-md whitespace-nowrap">ออกจากระบบ</a>
        </div>
    </div>
</nav>