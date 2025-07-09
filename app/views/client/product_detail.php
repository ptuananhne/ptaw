<?php $this->view('client/layouts/header', $data); ?>

<!-- 
    ===================================================================
    == TRANG CHI TIẾT SẢN PHẨM - GIAO DIỆN NÂNG CẤP V2
    ==
    == - Sử dụng class "product-detail-page-v2" để áp dụng style mới.
    == - Cấu trúc HTML được tổ chức lại hoàn toàn để hiện đại và dễ bảo trì hơn.
    == - Tận dụng các class CSS đã được chuẩn bị sẵn trong style.css.
    ===================================================================
-->
<div class="product-detail-page-v2" data-product-type="<?= $product->product_type ?>">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb-v2" aria-label="breadcrumb">
            <ol>
                <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
                <li><a href="<?= BASE_URL . '/category/' . $product->category_slug ?>"><?= htmlspecialchars($product->category_name) ?></a></li>
                <li class="active" aria-current="page"><?= htmlspecialchars($product->name) ?></li>
            </ol>
        </nav>

        <!-- Layout chính của sản phẩm -->
        <div class="product-layout">
            <!-- Cột thư viện ảnh -->
            <div class="product-gallery-v2">
                <div class="main-image-v2" id="main-image-container">
                    <img id="main-product-image-v2" src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url) ?>" alt="Ảnh chính của <?= htmlspecialchars($product->name) ?>">
                </div>
                <?php if (!empty($gallery) && count($gallery) > 0): ?>
                    <div class="thumbnail-list-v2">
                        <!-- Ảnh đại diện luôn là ảnh đầu tiên -->
                        <div class="thumbnail-item-v2 active">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url) ?>" alt="Thumbnail của <?= htmlspecialchars($product->name) ?>">
                        </div>
                        <!-- Các ảnh khác trong gallery -->
                        <?php foreach ($gallery as $image): ?>
                            <div class="thumbnail-item-v2">
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($image->image_url) ?>" alt="<?= htmlspecialchars($image->alt_text ?? 'Ảnh gallery') ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cột thông tin sản phẩm -->
            <div class="product-info-v2">
                <h1 class="product-title-v2"><?= htmlspecialchars($product->name) ?></h1>
                
                <div class="product-meta-v2">
                    <span>Thương hiệu: <a href="#"><?= htmlspecialchars($product->brand_name ?? 'Chưa xác định') ?></a></span>
                    <span class="meta-divider">|</span>
                    <span>Danh mục: <a href="<?= BASE_URL . '/category/' . $product->category_slug ?>"><?= htmlspecialchars($product->category_name) ?></a></span>
                </div>

                <div class="product-price-box-v2">
                    <?php if ($product->product_type == 'variable'): ?>
                        <p class="price-value" id="product-price-display">Chọn phiên bản</p>
                    <?php else: ?>
                        <p class="price-value"><?= (isset($product->price) && $product->price > 0) ? number_format($product->price, 0, ',', '.') . ' đ' : 'Liên hệ' ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Khu vực chọn phiên bản -->
                <?php if ($product->product_type == 'variable' && !empty($variants) && !empty($product_attributes)): ?>
                    <div class="product-variants-v2" id="product-variants-container">
                        <?php foreach ($product_attributes as $attribute): ?>
                            <div class="variant-group-v2">
                                <label class="variant-label-v2"><?= htmlspecialchars($attribute['name']) ?>:</label>
                                <div class="variant-options-v2">
                                    <?php foreach ($attribute['values'] as $value): ?>
                                        <div class="variant-option-v2">
                                            <input type="radio" 
                                                   id="attr-v2-<?= md5($attribute['name'] . $value) ?>" 
                                                   name="attribute_<?= htmlspecialchars($attribute['name']) ?>" 
                                                   value="<?= htmlspecialchars($value) ?>"
                                                   data-attribute-name="<?= htmlspecialchars($attribute['name']) ?>">
                                            <label for="attr-v2-<?= md5($attribute['name'] . $value) ?>"><?= htmlspecialchars($value) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Dữ liệu JSON cho các phiên bản, cần cho JS xử lý -->
                    <script id="product-variants-data" type="application/json"><?= json_encode($variants) ?></script>
                <?php endif; ?>

                <!-- Các nút hành động liên hệ -->
                <div class="contact-actions-v2">
                    <p class="contact-title">Tư vấn hoặc đặt hàng ngay:</p>
                    <div class="button-group">
                        <a href="https://m.me/YOUR_PAGE_ID" target="_blank" class="contact-btn-v2 facebook">
                            <i class="fab fa-facebook-messenger"></i> <!-- Cần FontAwesome -->
                            <span>Messenger</span>
                        </a>
                        <a href="https://zalo.me/0357575601" target="_blank" class="contact-btn-v2 zalo">
                            <i class="fa fa-comment-dots"></i> <!-- Cần FontAwesome -->
                            <span>Zalo</span>
                        </a>
                        <a href="tel:0357975610" class="contact-btn-v2 phone">
                            <i class="fa fa-phone-alt"></i> <!-- Cần FontAwesome -->
                            <span>Gọi ngay</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs mô tả và thông số kỹ thuật -->
        <div class="product-details-tabs-v2">
            <div class="tab-headers">
                <button class="tab-link active" data-tab="tab-description">Mô tả chi tiết</button>
                <?php if (!empty($product->specifications) && ($specs = json_decode($product->specifications, true)) && is_array($specs) && count($specs) > 0): ?>
                    <button class="tab-link" data-tab="tab-specs">Thông số kỹ thuật</button>
                <?php endif; ?>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-description">
                    <?= !empty($product->description) ? $product->description : '<p>Chưa có mô tả cho sản phẩm này.</p>' ?>
                </div>
                <?php if (!empty($product->specifications) && ($specs = json_decode($product->specifications, true)) && is_array($specs) && count($specs) > 0): ?>
                    <div class="tab-pane" id="tab-specs">
                        <table class="specs-table-v2">
                            <tbody>
                                <?php foreach ($specs as $key => $value): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($key) ?></td>
                                        <td><?= htmlspecialchars(is_array($value) ? implode(', ', $value) : $value) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Ghi chú: Để icon hiển thị, bạn cần thêm thư viện FontAwesome vào header, ví dụ: -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"> -->

<?php $this->view('client/layouts/footer', $data); ?>
