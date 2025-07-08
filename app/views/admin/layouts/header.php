<!DOCTYPE html>
<html lang="vi" x-data="{ profileMenuOpen: false }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Panel') ?> - PTA</title>
    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin-style.css?v=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin-style.css?v=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin-products.css?v=1.0">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    <div class="admin-wrapper">
        <?php require_once 'sidebar.php'; ?>
        <div class="admin-main-container">
            <header class="admin-header">
                <h2 class="header-title"><?= htmlspecialchars($title ?? '') ?></h2>
                <div class="profile-menu" @click.outside="profileMenuOpen = false">
                    <button class="profile-menu-button" @click="profileMenuOpen = !profileMenuOpen">
                        <span class="username"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
                        <img class="avatar" src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['admin_username'] ?? 'A') ?>&background=random" alt="Avatar">
                    </button>
                    <ul class="profile-menu-dropdown" x-show="profileMenuOpen" x-cloak x-transition>
                        <li><a href="<?= BASE_URL ?>/admin/auth/logout">Đăng xuất</a></li>
                    </ul>
                </div>
            </header>
            <main class="admin-main-content">