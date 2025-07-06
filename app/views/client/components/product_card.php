<?php

if (!function_exists('render_product_card')) {
    /**
     * Renders the HTML for a single, enhanced product card.
     *
     * @param object $product The product data object.
     * @return void
     */
    function render_product_card($product)
    {
        // Ensure the $product variable exists to avoid errors
        if (!isset($product)) {
            return;
        }
?>
        <div class="product-card" data-animate>
            <a href="<?= BASE_URL . '/product/' . htmlspecialchars($product->slug ?? '') ?>">
                <div class="product-image-wrapper">
                    <img class="product-image"
                        src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url ?? 'path/to/default/image.jpg') ?>"
                        alt="<?= htmlspecialchars($product->name ?? 'Sản phẩm') ?>"
                        loading="lazy">
                </div>
                <div class="product-card-content">
                    <div>
                        <?php if (!empty($product->category_name)): ?>
                            <p class="product-card-category"><?= htmlspecialchars($product->category_name) ?></p>
                        <?php endif; ?>
                        <h3 class="product-card-name" title="<?= htmlspecialchars($product->name ?? '') ?>">
                            <?= htmlspecialchars($product->name ?? 'Chưa có tên') ?>
                        </h3>
                    </div>
                    <div class="product-card-footer">
                        <p class="product-card-price">
                            <?php if (isset($product->price) && $product->price > 0): ?>
                                <?= number_format($product->price, 0, ',', '.') ?> đ
                            <?php else: ?>
                                Liên hệ
                            <?php endif; ?>
                        </p>
                        <div class="product-card-action">
                            <span class="action-text">Xem chi tiết</span>
                            <span class="action-icon">→</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
<?php
    }
}
