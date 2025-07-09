<?php
// Tệp: admin/layouts/sidebar.php
// ---
// File này chứa thanh điều hướng bên trái (sidebar).
// Nó sử dụng biến $current_uri từ header.php để làm nổi bật menu hiện tại.

/**
 * Hàm kiểm tra xem một menu có đang active hay không.
 * @param string $path Đường dẫn của menu (ví dụ: '/admin/product')
 * @param string $current_uri URI hiện tại của trang
 * @return string Trả về class 'bg-gray-700' nếu active, ngược lại trả về chuỗi rỗng.
 */
function is_active_nav(string $path, string $current_uri): string
{
    // Kiểm tra xem URI hiện tại có bắt đầu bằng đường dẫn của menu không
    if (str_starts_with($current_uri, BASE_URL . $path)) {
        return 'bg-gray-700';
    }
    return '';
}
?>
<aside class="w-64 min-h-screen bg-gray-800 text-white text-center item-center p-4 flex flex-col">
    <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
    <a href="<?php echo BASE_URL; ?>/admin/auth/logout" 
           class="block text-center py-2.5 px-4 rounded bg-red-500 hover:bg-red-600">
           Đăng xuất
        </a>
    <nav class="flex-grow">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard" 
           class="block py-2.5 px-4 rounded hover:bg-gray-700 <?php echo is_active_nav('/admin/dashboard', $current_uri); ?>">
           Dashboard
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/product" 
           class="block py-2.5 px-4 rounded hover:bg-gray-700 <?php echo is_active_nav('/admin/product', $current_uri); ?>">
           Sản phẩm
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/taxonomy" 
           class="block py-2.5 px-4 rounded hover:bg-gray-700 <?php echo is_active_nav('/admin/taxonomy', $current_uri); ?>">
           Phân loại
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/banner" 
           class="block py-2.5 px-4 rounded hover:bg-gray-700 <?php echo is_active_nav('/admin/banner', $current_uri); ?>">
           Banner
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/productAttribute" 
           class="block py-2.5 px-4 rounded hover:bg-gray-700 <?php echo is_active_nav('/admin/productAttribute', $current_uri); ?>">
           Thuộc tính
        </a>
    </nav>
    
        
    
</aside>
