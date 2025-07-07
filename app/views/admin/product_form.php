<?php $this->view('admin/layouts/header', $data); ?>

<div class="page-container form-container">
    <h1 class="page-title"><?= htmlspecialchars($data['title']) ?></h1>

    <form action="<?= $data['form_action'] ?>" method="POST" enctype="multipart/form-data" class="admin-form">
        <!-- Tên, Mô tả, Giá, Danh mục, Thương hiệu (giữ nguyên) -->
        <div class="form-group">
            <label for="name" class="form-label">Tên Sản phẩm</label>
            <input type="text" id="name" name="name" class="form-input" value="<?= htmlspecialchars($data['product']->name) ?>" required>
        </div>
        <!-- ... các form group khác giữ nguyên ... -->
        <div class="form-group">
            <label for="description" class="form-label">Mô tả</label>
            <textarea id="description" name="description" class="form-textarea" rows="6"><?= htmlspecialchars($data['product']->description) ?></textarea>
        </div>
        <div class="form-group">
            <label for="price" class="form-label">Giá (VNĐ)</label>
            <input type="number" id="price" name="price" class="form-input" value="<?= htmlspecialchars($data['product']->price) ?>" required>
        </div>
        <div class="form-group">
            <label for="category_id" class="form-label">Danh mục</label>
            <select id="category_id" name="category_id" class="form-select" required>
                <option value="">-- Chọn Danh mục --</option>
                <?php foreach ($data['categories'] as $category): ?>
                    <option value="<?= $category->id ?>" <?= ($category->id == $data['product']->category_id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="brand_id" class="form-label">Thương hiệu</label>
            <select id="brand_id" name="brand_id" class="form-select" required>
                <option value="">-- Chọn Thương hiệu --</option>
                <?php foreach ($data['brands'] as $brand): ?>
                    <option value="<?= $brand->id ?>" <?= ($brand->id == $data['product']->brand_id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($brand->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- KHU VỰC QUẢN LÝ ẢNH MỚI -->
        <div class="form-group">
            <label class="form-label">Quản lý Ảnh sản phẩm</label>
            <div class="image-management-area">
                <!-- Vùng upload ảnh mới -->
                <div class="form-group">
                    <label for="gallery" class="form-label-secondary">Tải ảnh mới (có thể chọn nhiều ảnh)</label>
                    <input type="file" id="gallery" name="gallery[]" class="form-input-file" multiple>
                </div>

                <!-- Vùng chọn ảnh đại diện -->
                <label class="form-label-secondary">Chọn ảnh đại diện từ các ảnh đã tải lên</label>
                <div class="image-selection-grid" id="image-selection-grid">
                    <?php if (empty($data['all_images'])): ?>
                        <p class="no-images-text">Chưa có ảnh nào được tải lên.</p>
                    <?php else: ?>
                        <?php foreach ($data['all_images'] as $image): ?>
                            <div class="image-card">
                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($image->image_url) ?>" alt="Ảnh sản phẩm">
                                <div class="image-overlay">
                                    <input type="radio" name="featured_image_url" value="<?= htmlspecialchars($image->image_url) ?>" id="radio-<?= $image->id ?>" <?= ($image->image_url == $data['product']->image_url) ? 'checked' : '' ?>>
                                    <label for="radio-<?= $image->id ?>">Chọn</label>
                                </div>
                                <button type="button" class="btn-delete-gallery" data-image-id="<?= $image->id ?>" title="Xóa ảnh này">×</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Lưu lại</button>
            <a href="<?= BASE_URL ?>/admin.php?url=adminProduct" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php $this->view('admin/layouts/footer'); ?>