<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Phút 89') ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script>
      // Chạy ngay lập tức để áp dụng theme từ localStorage trước khi trang được hiển thị
      (function() {
        try {
          const theme = localStorage.getItem('theme');
          if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
          }
        } catch (e) {
          // Bỏ qua lỗi nếu localStorage không khả dụng
        }
      })();
    </script>
    <!-- =================================================================== -->

    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=6.2">
</head>

<body class="<?= $page_class ?? '' ?>">
    <!-- Overlay for mobile menu -->
    <div class="menu-overlay" id="menu-overlay"></div>

    <!-- Page Container -->
    <div class="page-container">

        <!-- Mobile Sidebar (Placed outside for stacking context) -->
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

        <!-- Main Header -->
        <header class="top-header">
            <div class="header-container">
                <a href="<?= BASE_URL ?>" class="header-logo">Phút 89</a>

                <div class="header-search-wrapper">
                    <form action="<?= BASE_URL ?>/search" method="GET" style="width: 100%;">
                        <input type="search" name="q" placeholder="Tìm kiếm sản phẩm..." class="header-search-bar" value="<?= htmlspecialchars($keyword ?? '') ?>">
                    </form>
                </div>

                <div class="header-right">
                    <!-- Desktop navigation and actions -->
                    <div class="header-actions">
                        <nav class="header-nav">
                            <ul>
                                <li>
                                    <a href="<?= BASE_URL ?>" class="<?= !isset($current_category_slug) ? 'active' : '' ?>">
                                        <i class="fa-solid fa-house"></i>
                                        <span>Trang chủ</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa-solid fa-address-book"></i>
                                        <span>Liên hệ</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>

                        <button id="theme-toggle" class="header-action-btn theme-toggle-btn" aria-label="Chuyển đổi chế độ sáng/tối">
                            <i class="fa-solid fa-moon"></i>
                            <i class="fa-solid fa-sun"></i>
                        </button>
                    </div>

                    <!-- Mobile menu hamburger button -->
                    <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Mở menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Wrapper (for main content and desktop sidebar) -->
        <div class="main-wrapper">
            <!-- Desktop Sidebar -->
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
