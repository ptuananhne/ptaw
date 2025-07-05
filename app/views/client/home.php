<?php
$this->view('client/layouts/header', $data);
?>

<section class="hero-section" style="margin-bottom: 3rem; background-color: var(--color-primary); color: white; padding: 3rem 1rem; border-radius: 8px;">
    <h1 style="font-size: 2.25rem; font-weight: 700; text-align: center;">Khám Phá Thế Giới Công Nghệ</h1>
    <p style="font-size: 1.125rem; text-align: center; opacity: 0.9; margin-top: 1rem;">Sản phẩm công nghệ hàng đầu từ các thương hiệu nổi tiếng.</p>
</section>

<section id="featured-products">
    <h2 style="font-size: 1.875rem; font-weight: 700; text-align: center; margin-bottom: 2rem;">Sản phẩm nổi bật</h2>

    <div class="product-grid">
        <?php if (!empty($featured_products)): ?>
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <a href="<?= BASE_URL . '/product/' . $product->slug ?>">
                        <div class="product-image-wrapper">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url ?? '') ?>" alt="<?= htmlspecialchars($product->name) ?>" class="product-image">
                        </div>
                        <div class="product-card-content">
                            <p class="product-card-category"><?= htmlspecialchars($product->category_name) ?></p>
                            <h3 class="product-card-name"><?= htmlspecialchars($product->name) ?></h3>
                            <span class="product-card-link">Xem chi tiết</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">Chưa có sản phẩm nổi bật nào.</p>
        <?php endif; ?>
    </div>
</section>

<?php
$this->view('client/layouts/footer', $data);
?>