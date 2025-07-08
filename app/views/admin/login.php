<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Login') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin-style.css?v=1.0">
</head>

<body class="login-page">
    <div class="login-form-container">
        <h1 class="login-form-title">Đăng nhập Admin</h1>

        <?php if (isset($error) && !empty($error)): ?>
            <div class="login-error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/admin/auth/login" method="POST">
            <div class="login-form-group">
                <label for="username" class="login-form-label">Tên đăng nhập</label>
                <input type="text" id="username" name="username" class="login-form-input" required autofocus>
            </div>
            <div class="login-form-group">
                <label for="password" class="login-form-label">Mật khẩu</label>
                <input type="password" id="password" name="password" class="login-form-input" required>
            </div>
            <button type="submit" class="login-submit-button">Đăng nhập</button>
        </form>
        <p class="login-footer-text">&copy; <?= date('Y') ?> PTA</p>
    </div>
</body>

</html>