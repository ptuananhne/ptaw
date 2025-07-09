<?php $this->view('client/layouts/header', $data); ?>
<?php require_once 'components/product_card.php'; 
?>

<div class="page-header">
    <nav class="breadcrumb" aria-label="breadcrumb">
        <ol>
            <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="active" aria-current="page"><?= htmlspecialchars($category->name) ?></li>
        </ol>
    </nav>
    <h1 class="page-title"><?= htmlspecialchars($category->name) ?></h1>
</div>

<form class="filter-bar" method="GET" action="">
    <div class="filter-group">
        <label for="brand-filter">Thương hiệu</label>
        <select name="brand" id="brand-filter" onchange="this.form.submit()">
            <option value="">Tất cả thương hiệu</option>
            <?php foreach ($brands as $brand): ?>
                <option value="<?= $brand->id ?>" <?= (isset($filters['brand']) && $filters['brand'] == $brand->id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($brand->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label for="sort-filter">Sắp xếp</label>
        <select name="sort" id="sort-filter" onchange="this.form.submit()">
            <option value="views_desc" <?= (isset($filters['sort']) && $filters['sort'] == 'views_desc') ? 'selected' : '' ?>>Phổ biến nhất</option>
            <option value="newest" <?= (isset($filters['sort']) && $filters['sort'] == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
            <option value="price_asc" <?= (isset($filters['sort']) && $filters['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
            <option value="price_desc" <?= (isset($filters['sort']) && $filters['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
        </select>
    </div>
    <button type="submit" class="filter-button">Lọc sản phẩm</button>
</form>

<div class="product-grid">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <?php render_product_card($product); 
            ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>Không tìm thấy sản phẩm nào phù hợp với lựa chọn của bạn.</p>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($pagination) && $pagination['total'] > 1): ?>
    <nav class="pagination" aria-label="Product navigation">
        <ul>
            <?php
            $queryString = http_build_query($filters ?? []);
            ?>
            <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                <li>
                    <a href="?page=<?= $i ?>&<?= $queryString ?>"
                        class="<?= ($pagination['current'] == $i) ? 'active' : '' ?>"
                        aria-label="Go to page <?= $i ?>"
                        <?= ($pagination['current'] == $i) ? 'aria-current="page"' : '' ?>>
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php $this->view('client/layouts/footer', $data); ?>