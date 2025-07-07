<?php $this->view('admin/layouts/header', $data); ?>

<div class="page-container">
    <h1 class="page-title">Quản lý Danh mục & Thương hiệu</h1>

    <div class="taxonomy-grid">
        <!-- Cột Quản lý Danh mục -->
        <div class="taxonomy-column">
            <div class="taxonomy-box">
                <h2 class="taxonomy-title">Quản lý Danh mục</h2>

                <!-- Form thêm danh mục mới -->
                <form action="<?= BASE_URL ?>/admin.php?url=taxonomy/storeCategory" method="POST" class="taxonomy-form">
                    <input type="text" name="name" class="form-input" placeholder="Tên danh mục mới" required>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </form>

                <!-- Bảng danh sách danh mục -->
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Tên Danh mục</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['categories'] as $category): ?>
                                <tr>
                                    <td><?= htmlspecialchars($category->name) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/admin.php?url=taxonomy/deleteCategory/<?= $category->id ?>" class="btn btn-delete btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cột Quản lý Thương hiệu -->
        <div class="taxonomy-column">
            <div class="taxonomy-box">
                <h2 class="taxonomy-title">Quản lý Thương hiệu</h2>

                <!-- Form thêm thương hiệu mới -->
                <form action="<?= BASE_URL ?>/admin.php?url=taxonomy/storeBrand" method="POST" class="taxonomy-form">
                    <input type="text" name="name" class="form-input" placeholder="Tên thương hiệu mới" required>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </form>

                <!-- Bảng danh sách thương hiệu -->
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Tên Thương hiệu</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['brands'] as $brand): ?>
                                <tr>
                                    <td><?= htmlspecialchars($brand->name) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/admin.php?url=taxonomy/deleteBrand/<?= $brand->id ?>" class="btn btn-delete btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này?');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->view('admin/layouts/footer'); ?>