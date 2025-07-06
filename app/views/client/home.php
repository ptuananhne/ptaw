<?php $this->view('client/layouts/header', $data); ?>

<section class="banner-section">
    <div class="banner-slider-container">
        <div class="banner-track">
            <?php if (!empty($banners)): ?>
                <?php foreach ($banners as $banner): ?>
                    <div class="banner-slide">
                        <a href="<?= htmlspecialchars($banner->link_url ?? '#') ?>">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($banner->image_url) ?>" alt="<?= htmlspecialchars($banner->title ?? 'Banner') ?>">
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
        <?php if (!empty($banners) && count($banners) > 1): ?>
            <button class="banner-btn prev" aria-label="Previous Banner">&lt;</button>
            <button class="banner-btn next" aria-label="Next Banner">&gt;</button>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($featuredProducts)): ?>
    <section class="product-carousel-section">
        <h2 class="section-title">Sản phẩm nổi bật</h2>
        <div class="product-slider">
            <button class="slider-btn prev" aria-label="Previous product">&lt;</button>
            <div class="slider-track-container">
                <div class="slider-track">
                    <?php foreach ($featuredProducts as $product): ?>
                        <div class="product-card">
                            <a href="<?= BASE_URL . '/product/' . $product->slug ?>">
                                <div class="product-image-wrapper">
                                    <img class="product-image" src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url ?? '') ?>" alt="<?= htmlspecialchars($product->name) ?>">
                                </div>
                                <div class="product-card-content">
                                    <div>
                                        <p class="product-card-category"><?= htmlspecialchars($product->category_name) ?></p>
                                        <h3 class="product-card-name"><?= htmlspecialchars($product->name) ?></h3>
                                    </div>
                                    <div>
                                        <p class="product-card-price">
                                            <?php if (isset($product->price) && $product->price > 0): ?>
                                                <?= number_format($product->price, 0, ',', '.') ?> đ
                                            <?php else: ?>
                                                Liên hệ
                                            <?php endif; ?>
                                        </p>
                                        <span class="product-card-link">Xem chi tiết</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="slider-btn next" aria-label="Next product">&gt;</button>
        </div>
    </section>
<?php endif; ?>


<?php if (!empty($productsByCategory)): ?>
    <?php foreach ($productsByCategory as $categoryName => $categoryData): ?>
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
                                <div class="product-card">
                                    <a href="<?= BASE_URL . '/product/' . $product->slug ?>">
                                        <div class="product-image-wrapper">
                                            <img class="product-image" src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url ?? '') ?>" alt="<?= htmlspecialchars($product->name) ?>">
                                        </div>
                                        <div class="product-card-content">
                                            <div>
                                                <p class="product-card-category"><?= htmlspecialchars($product->category_name) ?></p>
                                                <h3 class="product-card-name"><?= htmlspecialchars($product->name) ?></h3>
                                            </div>
                                            <div>
                                                <p class="product-card-price">
                                                    <?php if (isset($product->price) && $product->price > 0): ?>
                                                        <?= number_format($product->price, 0, ',', '.') ?> đ
                                                    <?php else: ?>
                                                        Liên hệ
                                                    <?php endif; ?>
                                                </p>
                                                <span class="product-card-link">Xem chi tiết</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
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