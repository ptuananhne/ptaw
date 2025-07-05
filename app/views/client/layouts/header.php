<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'PTA | Thế giới công nghệ') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>

<body>
    <div class="page-container">
        <header class="top-header">
            <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Mở danh mục">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
                </svg>
            </button>

            <div class="header-search-wrapper">
                <input type="search" placeholder="Tìm kiếm sản phẩm..." class="header-search-bar">
            </div>
            <nav class="header-nav">
                <a href="<?= BASE_URL ?>">Trang chủ</a>
            </nav>
        </header>

        <div class="main-wrapper">
            <aside class="sidebar" id="sidebar">
                <h2 class="sidebar-title">Danh mục</h2>
                <ul class="category-list">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="<?= BASE_URL . '/category/' . $category->slug ?>">
                                    <?= htmlspecialchars($category->name) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </aside>

            <main class="main-content">