<?php
// Tệp: admin/layouts/header.php (Đã sửa lỗi)
// ---
// File này chứa phần đầu của trang HTML, bao gồm <head>, CSS, và mở đầu của <body>.
// Nó cũng sẽ gọi file sidebar.php.

// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lấy đường dẫn URI hiện tại
$current_uri = $_SERVER['REQUEST_URI'];
$current_path = strtok($current_uri, '?');

// --- SỬA LỖI QUAN TRỌNG ---
// AuthController đặt 'admin_id' vào session, không phải 'user_id'.
// Phải kiểm tra đúng key session, nếu không sẽ bị chuyển hướng sai.
// Đồng thời, không chuyển hướng nếu đã ở trang login.
if (!isset($_SESSION['admin_id']) && !str_ends_with($current_path, '/admin/login')) {
    // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập và không phải đang ở trang login
    header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/login');
    exit();
}

// Cung cấp giá trị mặc định cho tiêu đề trang
$pageTitle = $data['title'] ?? 'Trang Quản Trị';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Tải các thư viện CSS/JS chung -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Các CSS cho từng trang cụ thể sẽ được nhúng ở đây -->
    <?php if (isset($extra_css) && is_array($extra_css)): ?>
        <?php foreach ($extra_css as $css_link): ?>
            <link rel="stylesheet" href="<?php echo $css_link; ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <?php include __DIR__ . '/sidebar.php'; // Nhúng sidebar vào ?>
        
        <!-- Mở đầu cho nội dung chính của trang -->
        <main class="flex-1 p-10">
