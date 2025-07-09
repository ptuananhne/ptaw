
        </main> <!-- Đóng thẻ main của nội dung chính -->
    </div> <!-- Đóng thẻ flex container -->

    <!-- Các script chung có thể đặt ở đây -->

    <!-- Các JS cho từng trang cụ thể có thể được thêm vào đây nếu cần -->
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js_link): ?>
            <script src="<?php echo $js_link; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Hoặc có thể nhúng một khối script riêng của từng trang -->
    <?php if (isset($page_js)): ?>
        <script>
            <?php include $page_js; ?>
        </script>
    <?php endif; ?>
</body>
</html>
