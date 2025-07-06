<?php $this->view('client/layouts/header', $data); ?>

<div class="product-detail-page">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="breadcrumb">
        <ol>
            <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li><a href="<?= BASE_URL . '/category/' . $product->category_slug ?>"><?= htmlspecialchars($product->category_name) ?></a></li>
            <li><?= htmlspecialchars($product->name) ?></li>
        </ol>
    </nav>

    <div class="product-main-info">
        <!-- Product Gallery -->
        <div class="product-gallery">
            <div class="main-image">
                <img id="main-product-image" src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->name) ?>">
            </div>
            <?php if (!empty($gallery)): ?>
                <div class="thumbnail-list">
                    <!-- Ảnh đại diện -->
                    <div class="thumbnail-item active">
                        <img src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                    </div>
                    <!-- Các ảnh trong gallery -->
                    <?php foreach ($gallery as $image): ?>
                        <div class="thumbnail-item">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($image->image_url) ?>" alt="<?= htmlspecialchars($image->alt_text) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="product-info">
            <h1 class="product-title"><?= htmlspecialchars($product->name) ?></h1>
            <div class="product-meta">
                <span>Thương hiệu: <a href="#" class="meta-link"><?= htmlspecialchars($product->brand_name) ?></a></span>
                <span>Danh mục: <a href="<?= BASE_URL . '/category/' . $product->category_slug ?>" class="meta-link"><?= htmlspecialchars($product->category_name) ?></a></span>
            </div>
            <div class="product-description">
                <h3 class="section-heading">Mô tả sản phẩm</h3>
                <p><?= nl2br(htmlspecialchars($product->description)) ?></p>
            </div>
        </div>
    </div>

    <!-- Product Specifications -->
    <?php if (!empty($product->specifications)): ?>
        <div class="product-specs">
            <h3 class="section-heading">Thông số kỹ thuật</h3>
            <table class="specs-table">
                <tbody>
                    <?php
                    $specs = json_decode($product->specifications, true);
                    if (is_array($specs)):
                        foreach ($specs as $key => $value):
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($key) ?></td>
                                <td><?= htmlspecialchars($value) ?></td>
                            </tr>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<?php $this->view('client/layouts/footer', $data); ?>

<!-- Thêm JS cho gallery ảnh -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainImage = document.getElementById('main-product-image');
        const thumbnails = document.querySelectorAll('.thumbnail-item');

        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                // Bỏ active ở tất cả thumbnail
                thumbnails.forEach(t => t.classList.remove('active'));
                // Thêm active cho thumbnail được click
                this.classList.add('active');
                // Thay đổi ảnh chính
                mainImage.src = this.querySelector('img').src;
            });
        });
    });
</script>