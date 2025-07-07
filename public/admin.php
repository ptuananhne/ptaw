<?php
// Bắt đầu session để quản lý trạng thái đăng nhập
session_start();

// Nạp các file cấu hình và file lõi cần thiết
require_once '../config/config.php';
require_once '../app/core/Controller.php';
require_once '../app/models/Database.php';
require_once '../app/helpers/functions.php'; // File chứa các hàm tiện ích

/**
 * LOGIC ĐIỀU HƯỚNG (ROUTING) CHO TRANG ADMIN
 * Cấu trúc URL: /admin.php?url=controller/action/param1/param2
 */

// 1. Phân tích URL để xác định Controller, Action và các tham số
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'dashboard';
$url = filter_var($url, FILTER_SANITIZE_URL);
$params = !empty($url) ? explode('/', $url) : [];

// 2. Xác định Controller
// Mặc định là DashboardController nếu không có controller nào được chỉ định
$controllerName = 'DashboardController';
if (!empty($params[0])) {
    $controllerName = ucfirst($params[0]) . 'Controller';
}

// Luôn tìm controller trong thư mục /app/controllers/Admin/
$controllerFile = '../app/controllers/Admin/' . $controllerName . '.php';

// 3. Kiểm tra đăng nhập
// Nếu người dùng chưa đăng nhập và đang không cố truy cập trang Login, chuyển hướng họ về trang login.
if ($controllerName != 'LoginController' && !isset($_SESSION['admin_logged_in'])) {
    redirect(BASE_URL . '/admin.php?url=login');
}

// 4. Nạp và khởi tạo Controller
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    // Khởi tạo controller (ví dụ: new DashboardController())
    $controller = new $controllerName();
    unset($params[0]); // Xóa controller khỏi mảng params

    // 5. Xác định Action (phương thức)
    // Action mặc định là 'index'
    $actionName = 'index';
    if (!empty($params[1]) && method_exists($controller, $params[1])) {
        $actionName = $params[1];
        unset($params[1]); // Xóa action khỏi mảng params
    }

    // 6. Lấy các tham số còn lại
    $finalParams = $params ? array_values($params) : [];

    // 7. Gọi Controller và Action với các tham số tương ứng
    call_user_func_array([$controller, $actionName], $finalParams);
} else {
    // Nếu không tìm thấy file controller, hiển thị lỗi 404
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "The page you are looking for was not found.";
    exit();
}
