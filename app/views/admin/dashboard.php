<?php $this->view('admin/layouts/header', $data); ?>

<div class="content-container">
    <h1 class="page-title">Bảng điều khiển</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <h3 class="stat-card-title">Tổng sản phẩm</h3>
            <p class="stat-card-number blue"><?= $total_products ?? 0 ?></p>
            <a href="#" class="stat-card-link">Quản lý sản phẩm &rarr;</a>
        </div>

        <div class="stat-card">
            <h3 class="stat-card-title">Tổng danh mục</h3>
            <p class="stat-card-number green"><?= $total_categories ?? 0 ?></p>
            <a href="#" class="stat-card-link">Quản lý danh mục &rarr;</a>
        </div>

        <div class="stat-card">
            <h3 class="stat-card-title">Tổng thương hiệu</h3>
            <p class="stat-card-number purple"><?= $total_brands ?? 0 ?></p>
            <a href="#" class="stat-card-link">Quản lý thương hiệu &rarr;</a>
        </div>
    </div>
</div>

<?php $this->view('admin/layouts/footer'); ?>