<?php $this->view('client/layouts/header', $data); ?>

<div class="page-header">
    <nav class="breadcrumb" aria-label="breadcrumb">
        <ol>
            <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="active"><?= htmlspecialchars($category->name) ?></li>
        </ol>
    </nav>
    <h1 class="page-title"><?= htmlspecialchars($category->name) ?></h1>
</div>

<!-- Filter Bar -->
<form class="filter-bar" method="GET" action="">
    <div class="filter-group">
        <label for="brand-filter">Thương hiệu</label>
        <select name="brand" id="brand-filter">
            <option value="">Tất cả</option>
            <?php foreach ($brands as $brand): ?>
                <option value="<?= $brand->id ?>" <?= ($filters['brand'] == $brand->id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($brand->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label for="sort-filter">Sắp xếp</label>
        <select name="sort" id="sort-filter">
            <option value="views_desc" <?= ($filters['sort'] == 'views_desc') ? 'selected' : '' ?>>Xem nhiều nhất</option>
            <option value="newest" <?= ($filters['sort'] == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
            <option value="price_asc" <?= ($filters['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
            <option value="price_desc" <?= ($filters['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
        </select>
    </div>
    <button type="submit" class="filter-button">Lọc</button>
</form>

<!-- Product Grid -->
<div class="product-grid">
    <?php if (!empty($products)): ?>

        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <a href="<?= BASE_URL . '/product/' . $product->slug ?>">
                    <div class="product-image-wrapper">
                        <img class="product-image" src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url ?? '') ?>" alt="<?= htmlspecialchars($product->name) ?>">
                    </div>
                    <div class="product-card-content">
                        <div class="product-card-top">
                            <p class="product-card-category"><?= htmlspecialchars($product->category_name) ?></p>
                            <h3 class="product-card-name"><?= htmlspecialchars($product->name) ?></h3>
                        </div>
                        <div class="product-card-bottom">
                            <p class="product-card-price">
                                <?php if (isset($product->price) && $product->price > 0): ?>
                                    <?= number_format($product->price, 0, ',', '.') ?>đ
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
            <p>Không tìm thấy sản phẩm nào phù hợp.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($pagination['total'] > 1): ?>
    <nav class="pagination">
        <ul>
            <?php
            // Chuẩn bị query string cho các bộ lọc hiện tại
            $queryString = http_build_query($filters);
            ?>
            <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                <li>
                    <a href="?page=<?= $i ?>&<?= $queryString ?>"
                        class="<?= ($pagination['current'] == $i) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php $this->view('client/layouts/footer', $data); ?>