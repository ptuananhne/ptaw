<?php
// Tệp: admin/layouts/header.php
// ---
// Bắt đầu session nếu chưa có để kiểm tra đăng nhập
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tối ưu bảo mật: Kiểm tra người dùng đã đăng nhập chưa.
// Nếu chưa, chuyển hướng về trang đăng nhập.
if (!isset($_SESSION['user_id'])) {
    // Giả sử BASE_URL đã được định nghĩa ở file config trung tâm
    header('Location: ' . BASE_URL . '/admin/login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? htmlspecialchars($data['title']) : 'Admin Panel'; ?></title>
    
    <!-- Các thư viện CSS và Font chung -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Các style chung khác có thể thêm vào đây */
    </style>
    
    <?php
    // Cho phép các trang con chèn thêm CSS hoặc JS vào <head> nếu cần
    if (isset($data['head_content'])) {
        echo $data['head_content'];
    }
    ?>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar chung cho tất cả các trang admin -->
        <aside class="w-64 min-h-screen bg-gray-800 text-white p-4 flex flex-col">
            <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
            <nav class="flex-grow">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="block py-2.5 px-4 rounded hover:bg-gray-700">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/product" class="block py-2.5 px-4 rounded hover:bg-gray-700">Sản phẩm</a>
                <a href="<?php echo BASE_URL; ?>/admin/taxonomy" class="block py-2.5 px-4 rounded hover:bg-gray-700">Phân loại</a>
                <a href="<?php echo BASE_URL; ?>/admin/banner" class="block py-2.5 px-4 rounded hover:bg-gray-700">Banner</a>
                <a href="<?php echo BASE_URL; ?>/admin/productAttribute" class="block py-2.5 px-4 rounded hover:bg-gray-700">Thuộc tính</a>
            </nav>
            <div class="mt-auto">
                 <!-- Nút đăng xuất -->
                 <a href="<?php echo BASE_URL; ?>/admin/auth/logout" class="block w-full text-center py-2.5 px-4 rounded bg-red-600 hover:bg-red-700">Đăng xuất</a>
            </div>
        </aside>

        <!-- Bắt đầu nội dung chính của trang -->
        <main class="flex-1 p-10 overflow-y-auto">
