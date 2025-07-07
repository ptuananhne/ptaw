<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Trang Quản Trị' ?> - PTA</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Link to the new CSS file -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin-style.css?v=1.1">

    <!-- Alpine.js for interactivity -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
</head>

<body>
    <div class="admin-wrapper" x-data="layoutData()">
        <?php
        // Load sidebar only if logged in
        if (isset($_SESSION['admin_logged_in'])) {
            $this->view('admin/layouts/sidebar');
        }
        ?>
        <div class="admin-main-container">
            <?php if (isset($_SESSION['admin_logged_in'])): ?>
                <!-- Top Header Bar -->
                <header class="admin-header">
                    <h1 class="header-title">
                        <?= htmlspecialchars($title ?? 'Bảng điều khiển') ?>
                    </h1>

                    <!-- Profile Dropdown Menu -->
                    <div class="profile-menu">
                        <button class="profile-menu-button" @click="toggleProfileMenu" @keydown.escape="closeProfileMenu">
                            <span class="username"><?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                            <img class="avatar" src="https://placehold.co/100x100/E2E8F0/4A5568?text=A" alt="Admin Avatar" />
                        </button>
                        <template x-if="isProfileMenuOpen">
                            <ul x-cloak class="profile-menu-dropdown" @click.away="closeProfileMenu" @keydown.escape="closeProfileMenu">
                                <li>
                                    <a href="#">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Hồ sơ</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>/admin.php?url=login/logout">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <span>Đăng xuất</span>
                                    </a>
                                </li>
                            </ul>
                        </template>
                    </div>
                </header>
            <?php endif; ?>

            <!-- Main Content Area -->
            <main class="admin-main-content">
                <div class="content-container">