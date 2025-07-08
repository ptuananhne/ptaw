<?php $this->view('admin/layouts/header', $data); ?>

<div class="content-container">
    <div class="page-header">
        <h1 class="page-title"><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-primary">Thêm sản phẩm mới</a>
    </div>

    <div class="card">
        <div class="card-header filter-bar">
            <div class="filter-group">
                <label for="searchInput">Tìm kiếm</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Tên sản phẩm...">
            </div>
            <div class="filter-group">
                <label for="categoryFilter">Danh mục</label>
                <select id="categoryFilter" class="form-control">
                    <option value="">Tất cả danh mục</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category->id ?>"><?= htmlspecialchars($category->name) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="brandFilter">Thương hiệu</label>
                <select id="brandFilter" class="form-control">
                    <option value="">Tất cả thương hiệu</option>
                    <?php if (!empty($brands)): ?>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= $brand->id ?>"><?= htmlspecialchars($brand->name) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="card-body">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Lượt xem</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="product-table-body">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->name) ?>" class="product-thumbnail" onerror="this.src='https://placehold.co/60x60/e2e8f0/475569?text=N/A'">
                                </td>
                                <td class="product-name"><?= htmlspecialchars($product->name) ?></td>
                                <td><?= htmlspecialchars($product->category_name) ?></td>
                                <td><?= number_format($product->price, 0, ',', '.') ?>đ</td>
                                <td><?= htmlspecialchars($product->view_count) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/products/edit/<?= $product->id ?>" class="btn btn-sm btn-warning">Sửa</a>
                                    <button class="btn btn-sm btn-danger" onclick="showDeleteModal(<?= $product->id ?>, '<?= htmlspecialchars(addslashes($product->name)) ?>')">Xóa</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Không có sản phẩm nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <?php if ($total_pages > 1): ?>
                <nav class="pagination">
                    <ul>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li>
                                <a href="?page=<?= $i ?>" class="<?= ($i == $current_page) ? 'active' : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="deleteModal" class="modal" x-data="{ open: false }" x-show="open" x-cloak>
    <div class="modal-dialog" @click.outside="open = false">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="modal-close" @click="open = false">&times;</button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa sản phẩm "<strong id="productName"></strong>"?</p>
                <p class="text-danger">Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" action="<?= BASE_URL ?>/admin/products/delete" method="POST">
                    <input type="hidden" name="id" id="productIdToDelete">
                    <button type="button" class="btn btn-secondary" @click="open = false">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->view('admin/layouts/footer', ['page_scripts' => ['admin-products-unified.js']]); ?>