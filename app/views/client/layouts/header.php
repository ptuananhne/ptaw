<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'PTA | Thế giới công nghệ') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>

<body>
    <!-- Overlay cho menu mobile -->
    <div class="menu-overlay" id="menu-overlay"></div>

    <!-- Page Container -->
    <div class="page-container">

        <!-- Sidebar cho di động (Nằm ngoài cùng để không bị ảnh hưởng bởi các layout khác) -->
        <aside class="sidebar-mobile" id="sidebar-mobile">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Danh mục</h2>
                <button class="sidebar-close-btn" id="sidebar-close-btn" aria-label="Đóng menu">&times;</button>
            </div>
            <nav>
                <ul class="category-list">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="<?= BASE_URL . '/category/' . $category->slug ?>"
                                    class="<?= (isset($current_category_slug) && $current_category_slug == $category->slug) ? 'active' : '' ?>">
                                    <?= htmlspecialchars($category->name) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </nav>
        </aside>

        <!-- Header chính -->
        <header class="top-header">
            <div class="header-container">
                <a href="<?= BASE_URL ?>" class="header-logo">PTA</a>

                <div class="header-search-wrapper">
                    <form action="<?= BASE_URL ?>/search" method="GET" style="width: 100%;">
                        <input type="search" name="q" placeholder="Tìm kiếm sản phẩm..." class="header-search-bar" value="<?= htmlspecialchars($keyword ?? '') ?>">
                    </form>
                </div>

                <div class="header-right">
                    <nav class="header-nav">
                        <ul>
                            <li><a href="<?= BASE_URL ?>" class="<?= !isset($current_category_slug) ? 'active' : '' ?>">Trang chủ</a></li>
                            <!-- Thêm các link nav khác ở đây nếu cần -->
                        </ul>
                    </nav>
                    <!-- Nút bấm menu hamburger cho di động -->
                    <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Mở menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Wrapper (cho nội dung chính và sidebar desktop) -->
        <div class="main-wrapper">
            <!-- Sidebar cho Desktop -->
            <aside class="sidebar" id="sidebar-desktop">
                <div class="sidebar-header">
                    <h2 class="sidebar-title">Danh mục</h2>
                </div>
                <nav>
                    <ul class="category-list">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a href="<?= BASE_URL . '/category/' . $category->slug ?>"
                                        class="<?= (isset($current_category_slug) && $current_category_slug == $category->slug) ? 'active' : '' ?>">
                                        <?= htmlspecialchars($category->name) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="main-content">