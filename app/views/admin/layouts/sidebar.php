<aside class="admin-sidebar">
    <a href="<?= BASE_URL ?>/admin/dashboard" class="sidebar-brand">PTA Admin</a>
    <nav>
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item">
                <a href="<?= BASE_URL ?>/admin/dashboard" class="sidebar-nav-link <?= (strpos($_GET['url'], 'admin/dashboard') !== false) ? 'active-link' : '' ?>">
                    <span class="icon"><!-- SVG icon for dashboard --></span>
                    Bảng điều khiển
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="<?= BASE_URL ?>/admin/products" class="sidebar-nav-link <?= (strpos($_GET['url'], 'admin/products') !== false) ? 'active-link' : '' ?>">
                    <span class="icon"><!-- SVG icon for products --></span>
                    Quản lý Sản phẩm
                </a>
            </li>
            <!-- Thêm các link quản lý khác ở đây -->
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>" target="_blank" class="sidebar-footer-link">Xem trang web</a>
    </div>
</aside>