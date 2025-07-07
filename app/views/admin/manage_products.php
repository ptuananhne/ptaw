<?php $this->view('admin/layouts/header', $data); ?>

<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Danh sách Sản phẩm</h1>
        <a href="<?= BASE_URL ?>/admin.php?url=adminProduct/create" class="btn btn-primary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Thêm Sản phẩm mới
        </a>
    </div>

    <!-- Filter Form -->
    <div class="filter-container">
        <form action="<?= BASE_URL ?>/admin.php" method="GET" class="filter-form">
            <input type="hidden" name="url" value="adminProduct">
            <div class="filter-group">
                <input type="text" name="keyword" class="form-input" placeholder="Tìm theo tên sản phẩm..." value="<?= htmlspecialchars($data['filters']['keyword']) ?>">
            </div>
            <div class="filter-group">
                <select name="category_id" id="category-filter" class="form-select">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($data['categories'] as $category): ?>
                        <option value="<?= $category->id ?>" <?= ($data['filters']['category_id'] == $category->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <select name="brand_id" id="brand-filter" class="form-select">
                    <option value="">Tất cả thương hiệu</option>
                    <?php foreach ($data['brands'] as $brand): ?>
                        <option value="<?= $brand->id ?>" <?= ($data['filters']['brand_id'] == $brand->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <select name="sort_by" class="form-select">
                    <option value="newest" <?= ($data['filters']['sort_by'] == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="views_desc" <?= ($data['filters']['sort_by'] == 'views_desc') ? 'selected' : '' ?>>Xem nhiều nhất</option>
                    <option value="price_asc" <?= ($data['filters']['sort_by'] == 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                    <option value="price_desc" <?= ($data['filters']['sort_by'] == 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                </select>
            </div>
            <div class="filter-group filter-buttons">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <a href="<?= BASE_URL ?>/admin.php?url=adminProduct" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Thương hiệu</th>
                    <th>Giá</th>
                    <th>Lượt xem</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['products'])): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">Không tìm thấy sản phẩm nào phù hợp.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['products'] as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product->id) ?></td>
                            <td>
                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($product->image_url ?? 'uploads/placeholder.png') ?>" alt="<?= htmlspecialchars($product->name) ?>" class="table-image" onerror="this.onerror=null;this.src='https://placehold.co/60x60/E2E8F0/4A5568?text=N/A';">
                            </td>
                            <td class="product-name"><?= htmlspecialchars($product->name) ?></td>
                            <td><?= htmlspecialchars($product->category_name ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($product->brand_name ?? 'N/A') ?></td>
                            <td><?= number_format($product->price, 0, ',', '.') ?> ₫</td>
                            <td><?= htmlspecialchars($product->view_count) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= BASE_URL ?>/admin.php?url=adminProduct/edit/<?= $product->id ?>" class="btn btn-edit">Sửa</a>
                                    <a href="<?= BASE_URL ?>/admin.php?url=adminProduct/delete/<?= $product->id ?>" class="btn btn-delete">Xóa</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->view('admin/layouts/footer'); ?>