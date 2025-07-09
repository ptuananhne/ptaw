<?php
// Tệp: admin/layouts/footer.php
// ---
?>
        </main> <!-- Kết thúc nội dung chính -->
    </div> <!-- Kết thúc flex container -->

    <?php
    // Cho phép các trang con chèn thêm JS vào cuối trang nếu cần
    if (isset($data['footer_scripts'])) {
        echo $data['footer_scripts'];
    }
    ?>
</body>
</html>
