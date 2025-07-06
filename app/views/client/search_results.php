<?php $this->view('client/layouts/header', $data); ?>
<?php
// Nạp component thẻ sản phẩm
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

<!-- Product Grid -->
<div class="product-grid">

    <!-- 
      ================================================================
      == GỠ LỖI (DEBUGGING)
      == Bỏ comment (xóa /* và */) khối dưới đây để xem nội dung của biến $products.
      == Điều này sẽ giúp bạn biết controller có gửi đúng dữ liệu qua hay không.
      ================================================================
    -->
    <?php /*
    echo '<pre style="background: #f1f1f1; color: #333; padding: 1rem; border-radius: 8px; grid-column: 1 / -1; text-align: left;">';
    echo '<strong>--- DEBUGGING INFO ---</strong><br>';
    echo '<strong>Keyword:</strong> ' . htmlspecialchars($keyword ?? 'N/A') . '<br>';
    echo '<strong>Total Results:</strong> ' . ($total_results ?? 'N/A') . '<br>';
    echo '<strong>Is $products variable empty?</strong> ' . (empty($products) ? 'Yes' : 'No') . '<br>';
    echo '<strong>Content of $products:</strong><br>';
    print_r($products ?? []);
    echo '</pre>';
    */ ?>

    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <?php render_product_card($product); // Gọi hàm để render thẻ sản phẩm 
            ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>Không tìm thấy sản phẩm nào phù hợp với từ khóa của bạn.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if (isset($pagination) && $pagination['total'] > 1): ?>
    <nav class="pagination" aria-label="Search results navigation">
        <ul>
            <?php
            // Xây dựng query string để giữ lại từ khóa khi chuyển trang
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