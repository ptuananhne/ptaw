<?php $this->view('client/layouts/header', $data); ?>

<div class="product-detail-page">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="breadcrumb">
        <ol>
            <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li><a href="<?= BASE_URL . '/category/' . $product->category_slug ?>"><?= htmlspecialchars($product->category_name) ?></a></li>
            <li class="active"><?= htmlspecialchars($product->name) ?></li>
        </ol>
    </nav>

    <div class="product-main-info">
        <!-- Product Gallery -->
        <div class="product-gallery">
            <div class="main-image">
                <img id="main-product-image" src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->name) ?>">
            </div>
            <?php if (!empty($gallery)): ?>
                <div class="thumbnail-list">
                    <div class="thumbnail-item active">
                        <img src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                    </div>
                    <?php foreach ($gallery as $image): ?>
                        <div class="thumbnail-item">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($image->image_url) ?>" alt="<?= htmlspecialchars($image->alt_text ?? '') ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="product-info">
            <h1 class="product-title"><?= htmlspecialchars($product->name) ?></h1>
            <div class="product-meta">
                <span>Thương hiệu: <a href="#" class="meta-link"><?= htmlspecialchars($product->brand_name) ?></a></span>
                <span>Danh mục: <a href="<?= BASE_URL . '/category/' . $product->category_slug ?>" class="meta-link"><?= htmlspecialchars($product->category_name) ?></a></span>
            </div>

            <!-- Price Box -->
            <div class="product-price-box">
                <p class="price-label">Giá tham khảo</p>
                <?php if (isset($product->price) && $product->price > 0): ?>
                    <p class="product-price"><?= number_format($product->price, 0, ',', '.') ?>đ</p>
                <?php else: ?>
                    <p class="product-price" style="font-size: 1.5rem;">Liên hệ</p>
                <?php endif; ?>
            </div>

            <!-- Contact Actions -->
            <div class="contact-actions">
                <a href="https://m.me/YOUR_PAGE_ID" target="_blank" class="contact-btn facebook" id="fb-messenger-btn">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M8.2,14.2L6,17L10.9,14.9L11.4,15.1C11.6,15.2 11.8,15.2 12,15.2C14.2,15.2 16,13.9 16,12C16,10.1 14.2,8.8 12,8.8C9.8,8.8 8,10.1 8,12C8,12.7 8.3,13.4 8.8,13.9L8.2,14.2Z" />
                    </svg>
                    <span>Nhắn qua Facebook</span>
                </a>
                <a href="https://zalo.me/0357575601" target="_blank" class="contact-btn zalo">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.5,2H4.5A2.5,2.5 0 0,0 2,4.5V19.5A2.5,2.5 0 0,0 4.5,22H19.5A2.5,2.5 0 0,0 22,19.5V4.5A2.5,2.5 0 0,0 19.5,2M10.1,17.2H8.3L8.8,14.3H7.2L5.4,17.2H3.5L6.3,12.8L3.5,8.5H5.4L7.2,11.4H8.8L8.3,8.5H10.1L7.3,12.8L10.1,17.2M14.5,17.2H12.6V14.7H11.2V13.1H12.6V11.2C12.6,10.1 13.1,9.3 14.3,9.3H15.8V10.9H14.7C14.3,10.9 14.1,11.1 14.1,11.5V13.1H15.9L15.7,14.7H14.1V17.2M20.5,13.2H19.1V14.7H17.4V13.2H16V11.6H17.4V10.1H19.1V11.6H20.5V13.2Z" />
                    </svg>
                    <span>Chat Zalo: 0357575601</span>
                </a>
                <a href="tel:0357975610" class="contact-btn phone">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z" />
                    </svg>
                    <span>Gọi ngay: 0357975610</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <div class="product-description" style="margin-top: 2.5rem;">
        <h3 class="section-heading">Mô tả chi tiết</h3>
        <p><?= nl2br(htmlspecialchars($product->description ?? 'Chưa có mô tả cho sản phẩm này.')) ?></p>
    </div>

    <!-- Product Specifications -->
    <?php if (!empty($product->specifications)): ?>
        <div class="product-specs">
            <h3 class="section-heading">Thông số kỹ thuật</h3>
            <table class="specs-table">
                <tbody>
                    <?php
                    $specs = json_decode($product->specifications, true);
                    if (is_array($specs)):
                        foreach ($specs as $key => $value):
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($key) ?></td>
                                <td><?= htmlspecialchars($value) ?></td>
                            </tr>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<?php $this->view('client/layouts/footer', $data); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gallery Image Logic
        const mainImage = document.getElementById('main-product-image');
        const thumbnails = document.querySelectorAll('.thumbnail-item');
        if (mainImage && thumbnails.length > 0) {
            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    mainImage.src = this.querySelector('img').src;
                });
            });
        }

        // Facebook Messenger Logic
        const fbButton = document.getElementById('fb-messenger-btn');
        if (fbButton) {
            fbButton.addEventListener('click', function(e) {
                // Ngăn hành động mặc định để xử lý copy trước
                e.preventDefault();

                const productUrl = window.location.href;
                const originalText = this.querySelector('span').innerText;

                // Tạo một textarea ẩn để copy
                const textArea = document.createElement("textarea");
                textArea.value = "Chào bạn, tôi quan tâm đến sản phẩm này: " + productUrl;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    // Thông báo cho người dùng
                    this.querySelector('span').innerText = 'Đã sao chép link!';
                } catch (err) {
                    console.error('Không thể sao chép link: ', err);
                    this.querySelector('span').innerText = 'Lỗi sao chép';
                }
                document.body.removeChild(textArea);

                // Mở link messenger sau khi copy
                window.open(this.href, '_blank');

                // Trả lại text ban đầu sau 2 giây
                setTimeout(() => {
                    this.querySelector('span').innerText = originalText;
                }, 2000);
            });
        }
    });
</script>