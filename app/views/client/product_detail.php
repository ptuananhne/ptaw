<?php $this->view('client/layouts/header', $data); ?>

<div class="product-detail-page">
    <nav class="breadcrumb" aria-label="breadcrumb">
        <ol>
            <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li><a href="<?= BASE_URL . '/category/' . $product->category_slug ?>"><?= htmlspecialchars($product->category_name) ?></a></li>
            <li class="active"><?= htmlspecialchars($product->name) ?></li>
        </ol>
    </nav>

    <div class="product-main-info">
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

        <div class="product-info">
            <h1 class="product-title"><?= htmlspecialchars($product->name) ?></h1>
            <div class="product-meta">
                <span>Thương hiệu: <a href="#" class="meta-link"><?= htmlspecialchars($product->brand_name) ?></a></span>
                <span>Danh mục: <a href="<?= BASE_URL . '/category/' . $product->category_slug ?>" class="meta-link"><?= htmlspecialchars($product->category_name) ?></a></span>
            </div>

            <div class="product-price-box">
                <p class="price-label">Giá tham khảo</p>
                <?php if (isset($product->price) && $product->price > 0): ?>
                    <p class="product-price"><?= number_format($product->price, 0, ',', '.') ?> đ</p>
                <?php else: ?>
                    <p class="product-price" style="font-size: 1.8rem;">Liên hệ</p>
                <?php endif; ?>
            </div>

            <div class="contact-actions">
                <a href="https://m.me/YOUR_PAGE_ID" target="_blank" class="contact-btn facebook" id="fb-messenger-btn">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm3.12 11.26l-1.88 1.88-2.6-2.6-2.6 2.6-1.88-1.88 2.6-2.6-2.6-2.6 1.88-1.88 2.6 2.6 2.6-2.6 1.88 1.88-2.6 2.6 2.6 2.6zM12 20.5c-4.69 0-8.5-3.81-8.5-8.5S7.31 3.5 12 3.5s8.5 3.81 8.5 8.5-3.81 8.5-8.5 8.5z" transform="scale(1.2) translate(-2.5 -2.5)" />
                    </svg>
                    <span>Nhắn qua Facebook</span>
                </a>
                <a href="https://zalo.me/0357575601" target="_blank" class="contact-btn zalo">
                    <svg fill="currentColor" viewBox="0 0 512 512">
                        <path d="M479.9 111.8C472.2 72.5 436.3 43.1 394 43.1H118C75.7 43.1 39.8 72.5 32.1 111.8L256 273.3 479.9 111.8zM256 310.8L35.9 138.2C33.3 150.3 32 163 32 176v160c0 44.2 35.8 80 80 80h288c44.2 0 80-35.8 80-80V176c0-13-1.3-25.7-3.9-37.8L256 310.8z" />
                    </svg>
                    <span>Chat Zalo: 0357575601</span>
                </a>
                <a href="tel:0357975610" class="contact-btn phone">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1C9.37 21 3 14.63 3 6c0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                    </svg>
                    <span>Gọi ngay: 0357975610</span>
                </a>
            </div>
        </div>
    </div>

    <div class="product-description">
        <h3 class="section-heading">Mô tả chi tiết</h3>
        <div><?= !empty($product->description) ? nl2br(htmlspecialchars($product->description)) : '<p>Chưa có mô tả cho sản phẩm này.</p>' ?></div>
    </div>

    <?php if (!empty($product->specifications) && ($specs = json_decode($product->specifications, true)) && is_array($specs) && count($specs) > 0): ?>
        <div class="product-specs">
            <h3 class="section-heading">Thông số kỹ thuật</h3>
            <table class="specs-table">
                <tbody>
                    <?php foreach ($specs as $key => $value): ?>
                        <tr>
                            <td><?= htmlspecialchars($key) ?></td>
                            <td><?= htmlspecialchars($value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $this->view('client/layouts/footer', $data); ?>