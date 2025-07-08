<?php $this->view('admin/layouts/header', $data); ?>

<div class="content-container">
    <form action="<?= $isEdit ? BASE_URL . '/admin/products/edit/' . $id : BASE_URL . '/admin/products/create' ?>" method="POST" enctype="multipart/form-data" id="productForm">
        <div class="page-header">
            <h1 class="page-title"><?= htmlspecialchars($title) ?></h1>
            <div class="form-actions-header">
                <a href="<?= BASE_URL ?>/admin/products" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
            </div>
        </div>

        <div class="form-layout">
            <!-- Cột trái: Thông tin chính -->
            <div class="form-main-column">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Thông tin chung</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Tên sản phẩm</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="slug">Slug (URL thân thiện)</label>
                            <input type="text" id="slug" name="slug" class="form-control" value="<?= htmlspecialchars($slug ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea id="description" name="description" class="form-control" rows="8"><?= htmlspecialchars($description ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Giá & Phân loại</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="price">Giá</label>
                                <input type="number" id="price" name="price" class="form-control" value="<?= htmlspecialchars($price ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Danh mục</label>
                                <select id="category_id" name="category_id" class="form-control" required>
                                    <option value="">-- Chọn --</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category->id ?>" <?= (isset($category_id) && $category_id == $category->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="brand_id">Thương hiệu</label>
                                <select id="brand_id" name="brand_id" class="form-control" required>
                                    <option value="">-- Chọn --</option>
                                    <?php foreach ($brands as $brand): ?>
                                        <option value="<?= $brand->id ?>" <?= (isset($brand_id) && $brand_id == $brand->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($brand->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Thông số kỹ thuật (JSON)</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <textarea id="specifications" name="specifications" class="form-control" rows="5" placeholder='{"Màn hình": "OLED 6.7 inch", "Chip": "Apple A17 Pro"}'><?= htmlspecialchars($specifications ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Hình ảnh -->
            <div class="form-side-column">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ảnh đại diện</h3>
                    </div>
                    <div class="card-body">
                        <div class="main-image-preview">
                            <img id="mainImagePreview" src="<?= isset($image_url) && !empty($image_url) ? BASE_URL . '/uploads/' . $image_url : 'https://placehold.co/300x300/e2e8f0/475569?text=Chưa+có+ảnh' ?>" alt="Ảnh đại diện">
                        </div>
                        <p class="form-text">Chọn một ảnh từ thư viện bên dưới để đặt làm ảnh đại diện.</p>
                        <input type="hidden" name="current_main_image" value="<?= htmlspecialchars($image_url ?? '') ?>">
                        <input type="hidden" name="new_main_image_url" id="new_main_image_url">
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Thư viện ảnh</h3>
                    </div>
                    <div class="card-body">
                        <div id="gallery-container">
                            <?php if (!empty($gallery)): ?>
                                <?php foreach ($gallery as $image): ?>
                                    <div class="gallery-item <?= ($image->image_url == ($image_url ?? '')) ? 'is-main' : '' ?>" id="gallery-item-<?= $image->id ?>">
                                        <img src="<?= BASE_URL ?>/uploads/<?= $image->image_url ?>" alt="Gallery Image">
                                        <div class="gallery-item-actions">
                                            <button type="button" class="btn-icon" onclick="setAsMainImage(this, '<?= $image->image_url ?>')" title="Đặt làm ảnh đại diện">⭐</button>
                                            <button type="button" class="btn-icon btn-delete" onclick="deleteGalleryImage(this, <?= $image->id ?>)" title="Xóa ảnh">🗑️</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div id="gallery-upload-new">
                            <input type="file" name="gallery_images[]" id="gallery_images_input" class="form-control-file" accept="image/*" multiple>
                            <label for="gallery_images_input" class="upload-box">
                                <span>+ Thêm ảnh vào thư viện</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $this->view('admin/layouts/footer', ['page_scripts' => ['admin-products-unified.js']]); ?>