<?php $this->view('admin/layouts/header', $data); ?>

<h1 class="page-title">Bảng điều khiển</h1>

<!-- Statistics Cards -->
<div class="stats-grid">
    <!-- Card: Total Products -->
    <div class="stat-card">
        <h2 class="stat-card-title">Tổng sản phẩm</h2>
        <p class="stat-card-number blue"><?= $data['total_products'] ?? 0 ?></p>
        <a href="<?= BASE_URL ?>/admin.php?url=adminProduct" class="stat-card-link">
            Quản lý sản phẩm &rarr;
        </a>
    </div>

    <!-- Card: Total Categories -->
    <div class="stat-card">
        <h2 class="stat-card-title">Tổng danh mục</h2>
        <p class="stat-card-number green"><?= $data['total_categories'] ?? 0 ?></p>
        <a href="<?= BASE_URL ?>/admin.php?url=adminCategory" class="stat-card-link">
            Quản lý danh mục &rarr;
        </a>
    </div>

    <!-- Card: Total Brands -->
    <div class="stat-card">
        <h2 class="stat-card-title">Tổng thương hiệu</h2>
        <p class="stat-card-number purple"><?= $data['total_brands'] ?? 0 ?></p>
        <a href="<?= BASE_URL ?>/admin.php?url=adminBrand" class="stat-card-link">
            Quản lý thương hiệu &rarr;
        </a>
    </div>
</div>

<?php $this->view('admin/layouts/footer'); ?>