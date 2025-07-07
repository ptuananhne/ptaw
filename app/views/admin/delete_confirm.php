<?php $this->view('admin/layouts/header', $data); ?>

<div class="page-container">
    <h1 class="page-title"><?= htmlspecialchars($data['title']) ?></h1>

    <?php if ($data['product']): ?>
        <div class="confirm-box">
            <p>Bạn có thực sự muốn xóa sản phẩm sau không?</p>
            <h3>"<?= htmlspecialchars($data['product']->name) ?>"</h3>

            <form action="<?= BASE_URL ?>/admin.php?url=adminProduct/delete/<?= $data['product']->id ?>" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-delete">Có, Xóa ngay</button>
            </form>
            <a href="<?= BASE_URL ?>/admin.php?url=adminProduct" class="btn btn-secondary">Không, Quay lại</a>
        </div>
    <?php else: ?>
        <p>Không tìm thấy sản phẩm để xóa.</p>
        <a href="<?= BASE_URL ?>/admin.php?url=adminProduct" class="btn btn-secondary">Quay lại</a>
    <?php endif; ?>

</div>

<?php $this->view('admin/layouts/footer'); ?>