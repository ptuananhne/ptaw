<?php
    $this->view('client/layouts/header', $data);
    // Nạp file chứa hàm render_product_card() để có thể sử dụng ở dưới
   require_once 'components/product_card.php'; // Nạp tệp hàm component 
?>

<!-- Banner Section -->
<section class="banner-section">
    <div class="banner-slider-container">
        <div class="banner-track">
            <?php if (!empty($data['banners'])): ?>
                <?php foreach ($data['banners'] as $banner): ?>
                    <div class="banner-slide">
                        <a href="<?php echo htmlspecialchars($banner->link_url ?? '#'); ?>">
                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($banner->image_url); ?>" alt="<?php echo htmlspecialchars($banner->title ?? 'Banner'); ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback content when no banners are active -->
                <div class="banner-slide">
                    <div class="hero-section-fallback">
                        <h1>Khám Phá Thế Giới Công Nghệ</h1>
                        <p>Sản phẩm công nghệ hàng đầu từ các thương hiệu nổi tiếng.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Navigation Buttons -->
        <?php if (!empty($data['banners']) && count($data['banners']) > 1): ?>
            <button class="banner-btn prev" aria-label="Previous Banner">&#10094;</button>
            <button class="banner-btn next" aria-label="Next Banner">&#10095;</button>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($data['featuredProducts'])): ?>
    <section class="product-carousel-section">
        <h2 class="section-title">Sản phẩm nổi bật</h2>
        <div class="product-slider">
            <button class="slider-btn prev" aria-label="Previous product">&lt;</button>
            <div class="slider-track-container">
                <div class="slider-track">
                    <?php foreach ($data['featuredProducts'] as $product): ?>
                        <?php render_product_card($product); // Gọi hàm để render thẻ sản phẩm ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="slider-btn next" aria-label="Next product">&gt;</button>
        </div>
    </section>
<?php endif; ?>


<?php if (!empty($data['productsByCategory'])): ?>
    <?php foreach ($data['productsByCategory'] as $categoryName => $categoryData): ?>
        <?php if (!empty($categoryData['products'])): ?>
            <section class="product-carousel-section">
                <div class="section-header">
                    <h2 class="section-title"><?= htmlspecialchars($categoryName) ?></h2>
                    <a href="<?= BASE_URL . '/category/' . $categoryData['slug'] ?>" class="see-all-link">Xem tất cả &gt;</a>
                </div>
                <div class="product-slider">
                    <button class="slider-btn prev" aria-label="Previous product">&lt;</button>
                    <div class="slider-track-container">
                        <div class="slider-track">
                            <?php foreach ($categoryData['products'] as $product): ?>
                                <?php render_product_card($product); // Chỉ cần gọi hàm này, nó đã chứa thẻ product-card ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button class="slider-btn next" aria-label="Next product">&gt;</button>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>


<?php $this->view('client/layouts/footer', $data); ?>
