<?php
// Tệp: admin/dashboard.php (Đã tái cấu trúc)

// 1. Nhúng header (đã bao gồm kiểm tra session và sidebar)
include __DIR__ . '/layouts/header.php';

// 2. Chuẩn bị dữ liệu cho trang
$username = $data['username'] ?? 'Admin';
$productCount = $data['productCount'] ?? 0;
$categoryCount = $data['categoryCount'] ?? 0;
$brandCount = $data['brandCount'] ?? 0;
$bannerCount = $data['bannerCount'] ?? 0;
?>

<!-- Bắt đầu nội dung chính của trang Dashboard -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Dashboard</h1>
    <div class="flex items-center">
        <span class="text-gray-600 mr-4">Xin chào, <?php echo htmlspecialchars($username); ?>!</span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Products Card -->
    <div class="bg-white rounded-lg shadow p-6 flex items-center">
        <div class="bg-blue-500 rounded-full p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
        </div>
        <div class="ml-4">
            <p class="text-sm text-gray-500">Sản phẩm</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $productCount; ?></p>
        </div>
    </div>

    <!-- Categories Card -->
    <div class="bg-white rounded-lg shadow p-6 flex items-center">
        <div class="bg-green-500 rounded-full p-3">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
        </div>
        <div class="ml-4">
            <p class="text-sm text-gray-500">Danh mục</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $categoryCount; ?></p>
        </div>
    </div>

    <!-- Brands Card -->
    <div class="bg-white rounded-lg shadow p-6 flex items-center">
        <div class="bg-yellow-500 rounded-full p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v1h-14v-1zM5 9h14v10a2 2 0 01-2 2H7a2 2 0 01-2-2V9z" /></svg>
        </div>
        <div class="ml-4">
            <p class="text-sm text-gray-500">Thương hiệu</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $brandCount; ?></p>
        </div>
    </div>

    <!-- Banners Card -->
    <div class="bg-white rounded-lg shadow p-6 flex items-center">
        <div class="bg-purple-500 rounded-full p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l-1-1m-4 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
        </div>
        <div class="ml-4">
            <p class="text-sm text-gray-500">Banner</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $bannerCount; ?></p>
        </div>
    </div>
</div>
<!-- Kết thúc nội dung chính của trang Dashboard -->

<?php
// 3. Nhúng footer
include __DIR__ . '/layouts/footer.php';
?>
