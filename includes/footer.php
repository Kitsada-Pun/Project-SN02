<footer class="bg-gray-900 text-gray-300 py-8 mt-auto">
    <div class="container mx-auto px-4 md:px-6 text-center">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <a href="/pixellink-assistants-main/index.php" class="mb-4 md:mb-0 transition duration-300 hover:opacity-80">
                <img src="https://cdn.discordapp.com/attachments/1391834638418444404/1428463497922216067/logoW.png?ex=68f297cd&is=68f1464d&hm=446f67dd77bec3cc090f11d7d70caaae491f828e5af7125b70954f62efd713fd" alt="PixelLink Logo" class="h-12 w-auto">
            </a>
            <div class="flex flex-wrap justify-center space-x-2 md:space-x-6 text-sm md:text-base">
                <a href="/pixellink-assistants-main/about.php" class="hover:text-white transition duration-300 mb-2 md:mb-0 font-light">เกี่ยวกับเรา</a>
                <a href="/pixellink-assistants-main/contact.php" class="hover:text-white transition duration-300 mb-2 md:mb-0 font-light">ติดต่อเรา</a>
                <a href="/pixellink-assistants-main/terms.php" class="hover:text-white transition duration-300 mb-2 md:mb-0 font-light">เงื่อนไขการใช้งาน</a>
                <a href="/pixellink-assistants-main/privacy.php" class="hover:text-white transition duration-300 mb-2 md:mb-0 font-light">นโยบายความเป็นส่วนตัว</a>
            </div>
        </div>
        <hr class="border-gray-700 my-6">
        <p class="text-xs md:text-sm font-light">&copy; <?php echo date('Y'); ?> PixelLink. All rights reserved.</p>
    </div>
</footer>

<!-- <footer class="bg-gray-900 text-gray-300 py-8 mt-auto">
    <div class="container mx-auto px-4 md:px-6 text-center">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <a href="/pixellink-assistants-main/index.php" class="text-2xl sm:text-3xl font-bold pixellink-logo-footer mb-4 md:mb-0 transition duration-300 hover:opacity-80">Pixel<b>Link</b></a>
            <div class="flex flex-wrap justify-center space-x-2 md:space-x-6 text-sm md:text-base">
                <a href="/pixellink-assistants-main/about.php" class="hover:text-white transition duration-300 mb-2 md:mb-0 font-light">เกี่ยวกับเรา</a>
                <a href="/pixellink-assistants-main/contact.php" class="hover:text-white transition duration-300 mb-2 md:mb-0 font-light">ติดต่อเรา</a>
                <a href="/pixellink-assistants-main/terms.php" class="hover:text-white transition duration-300 mb-2 md:mb-0 font-light">เงื่อนไขการใช้งาน</a>
                <a href="/pixellink-assistants-main/privacy.php" class="hover:text-white transition duration-300 mb-2 md:mb-0 font-light">นโยบายความเป็นส่วนตัว</a>
            </div>
        </div>
        <hr class="border-gray-700 my-6">
        <p class="text-xs md:text-sm font-light">&copy; <?php echo date('Y'); ?> PixelLink. All rights reserved.</p>
    </div>
</footer> -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Optional: JavaScript for smooth scrolling to sections
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
</body>

</html>