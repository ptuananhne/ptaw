<?php
// Get current URL to determine the active page
$current_url = $_GET['url'] ?? 'dashboard';
$url_parts = explode('/', $current_url);
$active_page = $url_parts[0];

// Helper function to check and assign the 'active-link' class
function get_active_class($page, $active_page)
{
    return ($page === $active_page) ? 'active-link' : '';
}
?>
<aside class="admin-sidebar">
    <a class="sidebar-brand" href="<?= BASE_URL ?>/admin.php?url=dashboard">
        PTA Admin Panel
    </a>
    <ul class="sidebar-nav">
        <li class="sidebar-nav-item">
            <?php if ($active_page === 'dashboard'): ?>
                <span class="active-indicator"></span>
            <?php endif; ?>
            <a class="sidebar-nav-link <?= get_active_class('dashboard', $active_page) ?>" href="<?= BASE_URL ?>/admin.php?url=dashboard">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Bảng điều khiển</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <?php if ($active_page === 'adminProduct'): ?>
                <span class="active-indicator"></span>
            <?php endif; ?>
            <a class="sidebar-nav-link <?= get_active_class('adminProduct', $active_page) ?>" href="<?= BASE_URL ?>/admin.php?url=adminProduct">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span>Sản phẩm</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <?php if ($active_page === 'adminCategory'): ?>
                <span class="active-indicator"></span>
            <?php endif; ?>
            <a class="sidebar-nav-link <?= get_active_class('adminCategory', $active_page) ?>" href="<?= BASE_URL ?>/admin.php?url=adminCategory">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Danh mục</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <?php if ($active_page === 'adminBrand'): ?>
                <span class="active-indicator"></span>
            <?php endif; ?>
            <a class="sidebar-nav-link <?= get_active_class('adminBrand', $active_page) ?>" href="<?= BASE_URL ?>/admin.php?url=adminBrand">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v1h-4V5a2 2 0 01-2-2H7a2 2 0 01-2 2v12a2 2 0 012 2h10a2 2 0 012-2v-1h-4"></path>
                </svg>
                <span>Thương hiệu</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>" target="_blank" class="sidebar-footer-link">
            Xem trang web
            <span style="margin-left: 0.5rem;" aria-hidden="true">&rarr;</span>
        </a>
    </div>
</aside>