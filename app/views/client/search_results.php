<?php $this->view('client/layouts/header', $data); ?>
<?php
require_once 'components/product_card.php';
?>

<div class="page-header">
    <h1 class="page-title">
        Kết quả tìm kiếm
    </h1>
    <?php if (isset($keyword) && !empty($keyword)): ?>
        <p class="search-summary">
            Tìm thấy <?= $total_results ?? 0 ?> kết quả cho từ khóa "<strong><?= htmlspecialchars($keyword) ?></strong>"
        </p>
    <?php endif; ?>
</div>

<div class="product-grid">

    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <?php render_product_card($product);
            ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>Không tìm thấy sản phẩm nào phù hợp với từ khóa của bạn.</p>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($pagination) && $pagination['total'] > 1): ?>
    <nav class="pagination" aria-label="Search results navigation">
        <ul>
            <?php
            $queryParams = ['q' => $keyword ?? ''];
            ?>
            <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                <li>
                    <?php
                    $queryParams['page'] = $i;
                    $queryString = http_build_query($queryParams);
                    ?>
                    <a href="?<?= $queryString ?>"
                        class="<?= ($pagination['current'] == $i) ? 'active' : '' ?>"
                        aria-label="Go to page <?= $i ?>"
                        <?= ($pagination['current'] == $i) ? 'aria-current="page"' : '' ?>>
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php $this->view('client/layouts/footer', $data); ?>