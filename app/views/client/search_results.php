<?php $this->view('client/layouts/header', $data); ?>

<div class="page-header">
    <h1 class="page-title">
        Kết quả tìm kiếm
    </h1>
    <p class="search-summary">
        Tìm thấy <?= $total_results ?> kết quả cho từ khóa "<strong><?= htmlspecialchars($keyword) ?></strong>"
    </p>
</div>

<div class="product-grid">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
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
    <?php else: ?>
        <div class="empty-state">
            <p>Không tìm thấy sản phẩm nào phù hợp với từ khóa của bạn.</p>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($pagination) && $pagination['total'] > 1): ?>
    <nav class="pagination">
        <ul>
            <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                <li>
                    <a href="?q=<?= urlencode($keyword) ?>&page=<?= $i ?>"
                        class="<?= ($pagination['current'] == $i) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php $this->view('client/layouts/footer', $data); ?>