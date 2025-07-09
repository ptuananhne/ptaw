<?php
    $this->view('client/layouts/header', $data);
   require_once 'components/product_card.php'; 
?>

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
                <div class="banner-slide">
                    <div class="hero-section-fallback">
                        <h1>Khám Phá Thế Giới Công Nghệ</h1>
                        <p>Sản phẩm công nghệ hàng đầu từ các thương hiệu nổi tiếng.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
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
                        <?php render_product_card($product);  ?>
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
                                <?php render_product_card($product); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button class="slider-btn next" aria-label="Next product">&gt;</button>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
<section class="map-section">
    <h2 class="section-title" style="text-align: center;">Hệ thống Cửa hàng <?php echo (SITE_NAME); ?></h2>
    <div class="map-tabs">
        <button class="map-tab-btn active" data-map="map1">Chi nhánh 1: 41B Lê Duẩn, BMT</button>
        <button class="map-tab-btn" data-map="map2">Chi nhánh 2: 323 Phan Chu Trinh, BMT</button>
    </div>
    <div class="map-content">
        <div id="map1" class="map-pane active">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3892.684959077661!2d108.0410607!3d12.6686496!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31721d185f5a77a1%3A0xc7ccfe798e4d37a6!2zQ-G6p20gxJDhu5MgUGjDunQgODk!5e0!3m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Bản đồ chi nhánh 1"></iframe>
        </div>
        <div id="map2" class="map-pane">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3290.111758933799!2d108.05111430806635!3d12.691476296416534!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3171f7d90106403f%3A0xcb0a19e88c19dd51!2zMzIzIFBoYW4gQ2h1IFRyaW5oLCBUw6NuIEzhu6NpLCBCdcO0biBNYSBUaHVvYywgxJDhuq9rIEzhuqdjIDYzMDAwMCwgVmlF4buHdCBOYW0!5e0!3m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Bản đồ chi nhánh 2"></iframe>
        </div>
    </div>
</section>

<?php $this->view('client/layouts/footer', $data); ?>
