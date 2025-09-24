<?php
// ดึงชื่อผู้ใช้ (โค้ดนี้จะใช้ตัวแปร $conn จากไฟล์ about.php)
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
?>
<nav class="bg-white/80 backdrop-blur-sm p-4 shadow-md sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <a href="client/main.php">
            <img src="dist/img/logo.png" alt="PixelLink Logo" class="h-12 transition-transform hover:scale-105">
        </a>
        <div class="space-x-4 flex items-center">
            <span class="font-medium text-slate-700">สวัสดี, <?= htmlspecialchars($loggedInUserName) ?>!</span>
            <!-- <a href="view_profile.php?user_id=<?= $_SESSION['user_id']; ?>" class="btn-primary text-white px-5 py-2 rounded-lg font-medium shadow-md">ดูโปรไฟล์</a> -->
            <a href="logout.php" class="btn-danger text-white px-5 py-2 rounded-lg font-medium shadow-md">ออกจากระบบ</a>
        </div>
    </div>
</nav>