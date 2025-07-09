<?php $this->view('client/layouts/header', $data); ?>

<div class="product-detail-page-v2" data-product-type="<?= $product->product_type ?>">
    <div class="container">
        <nav class="breadcrumb-v2" aria-label="breadcrumb">
            <ol>
                <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
                <li><a href="<?= BASE_URL . '/category/' . $product->category_slug ?>"><?= htmlspecialchars($product->category_name) ?></a></li>
                <li class="active" aria-current="page"><?= htmlspecialchars($product->name) ?></li>
            </ol>
        </nav>

        <div class="product-layout">
            <div class="product-gallery-v2">
                <div class="main-image-v2">
                    <img id="main-product-image" src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url) ?>" alt="Ảnh chính của <?= htmlspecialchars($product->name) ?>" onerror="this.onerror=null;this.src='https://placehold.co/600x600/f8f9fa/ccc?text=Image+Not+Found';">
                </div>
                <?php if (!empty($gallery) && count($gallery) > 0): ?>
                    <div class="thumbnail-list-v2">
                        <div class="thumbnail-item-v2 active" data-image-src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url) ?>">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url) ?>" alt="Thumbnail <?= htmlspecialchars($product->name) ?>">
                        </div>
                        <?php foreach ($gallery as $image): ?>
                            <div class="thumbnail-item-v2" data-image-src="<?= BASE_URL . '/' . htmlspecialchars($image->image_url) ?>">
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($image->image_url) ?>" alt="<?= htmlspecialchars($image->alt_text ?? 'Thumbnail') ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="product-info-v2">
                <h1 class="product-title-v2"><?= htmlspecialchars($product->name) ?></h1>
                
                <div class="product-meta-v2">
                    <span>Thương hiệu: <a href="#"><?= htmlspecialchars($product->brand_name ?? 'Chưa xác định') ?></a></span>
                    <span class="meta-divider">|</span>
                    <span>Danh mục: <a href="<?= BASE_URL . '/category/' . $product->category_slug ?>"><?= htmlspecialchars($product->category_name) ?></a></span>
                </div>

                <div class="product-price-box-v2">
                    <span id="product-price-display" class="price-value">
                        <?php if ($product->product_type == 'variable'): ?>
                            Chọn phiên bản để xem giá
                        <?php else: ?>
                            <?= (isset($product->price) && $product->price > 0) ? number_format($product->price, 0, ',', '.') . ' đ' : 'Liên hệ' ?>
                        <?php endif; ?>
                    </span>
                </div>

                <?php if ($product->product_type == 'variable' && !empty($variants) && !empty($product_attributes)): ?>
                    <div id="product-variants-container" class="product-variants-v2">
                        <?php foreach ($product_attributes as $attribute): ?>
                            <div class="variant-group-v2" data-attribute-group-name="<?= htmlspecialchars($attribute['name']) ?>">
                                <label class="variant-label-v2"><?= htmlspecialchars($attribute['name']) ?>:</label>
                                <div class="variant-options-v2">
                                    <?php foreach ($attribute['values'] as $value): ?>
                                        <div class="variant-option-v2">
                                            <?php 
                                                $display_value = is_scalar($value) ? $value : 'Lỗi';
                                                $unique_id = md5($attribute['name'] . $display_value);
                                            ?>
                                            <input type="radio" id="attr-<?= $unique_id ?>" name="attribute_<?= htmlspecialchars($attribute['name']) ?>" value="<?= htmlspecialchars($display_value) ?>" data-attribute-name="<?= htmlspecialchars($attribute['name']) ?>" <?= $display_value === 'Lỗi' ? 'disabled' : '' ?>>
                                            <label for="attr-<?= $unique_id ?>"><?= htmlspecialchars($display_value) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                     <script id="product-variants-data" type="application/json"><?= json_encode($variants) ?></script>
                <?php endif; ?>

                <div class="contact-actions-v2">
                    <p class="contact-title">Liên hệ tư vấn:</p>
                    <div class="button-group">
                        <a href="https://m.me/YOUR_PAGE_ID" target="_blank" class="contact-btn-v2 facebook" id="fb-messenger-btn">
                            <i class="fab fa-facebook-messenger"></i> <span>Messenger</span>
                        </a>
                        <a href="https://zalo.me/0357575601" target="_blank" class="contact-btn-v2 zalo">
                            <i class="fas fa-comment-dots"></i> <span>Chat Zalo</span>
                        </a>
                        <a href="tel:0357975610" class="contact-btn-v2 phone">
                            <i class="fas fa-phone-alt"></i> <span>Gọi ngay</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="product-details-tabs-v2">
            <div class="tab-headers">
                <button class="tab-link active" data-tab="description">Mô tả sản phẩm</button>
                <button class="tab-link" data-tab="specifications">Thông số kỹ thuật</button>
            </div>
            <div class="tab-content">
                <div id="specifications" class="tab-pane active">
                    <?php if (!empty($product->specifications) && ($specs = json_decode($product->specifications, true)) && is_array($specs) && count($specs) > 0): ?>
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
                    <?php else: ?>
                        <p>Sản phẩm này chưa có thông số kỹ thuật chi tiết.</p>
                    <?php endif; ?>
                </div>
                <div id="description" class="tab-pane ">
                    <?= !empty($product->description) ? nl2br(htmlspecialchars($product->description)) : '<p>Chưa có mô tả cho sản phẩm này.</p>' ?>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php $this->view('client/layouts/footer', $data); ?>
