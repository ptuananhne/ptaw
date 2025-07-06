<?php
// Bắt đầu session
session_start();

// Tải các file cần thiết
require_once '../config/config.php';
require_once '../app/models/Database.php';
require_once '../app/core/Controller.php';

// --- LOGIC ĐIỀU TUYẾN (ROUTING) ĐÃ ĐƯỢC NÂNG CẤP ---

// 1. Phân tích URL
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$url = filter_var($url, FILTER_SANITIZE_URL);
$params = !empty($url) ? explode('/', $url) : [];

// 2. Xác định Controller
// Controller mặc định là HomeController nếu URL trống
$controllerName = !empty($params[0]) ? ucfirst($params[0]) . 'Controller' : 'HomeController';
$controllerFile = '../app/controllers/Client/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    unset($params[0]); // Xóa controller khỏi mảng tham số

    // 3. Xác định Action (Hành động)
    // Action mặc định là 'index'
    $actionName = 'index';
    if (!empty($params[1]) && method_exists($controller, $params[1])) {
        // Nếu tham số thứ 2 tồn tại và là một method có trong controller, thì nó là action
        $actionName = $params[1];
        unset($params[1]); // Xóa action khỏi mảng tham số
    }

    // 4. Lấy các tham số còn lại
    $finalParams = $params ? array_values($params) : [];

    // 5. Gọi Controller và Action với các tham số
    call_user_func_array([$controller, $actionName], $finalParams);
} else {
    // Xử lý trang 404 nếu không tìm thấy controller
    // Bạn có thể tạo một trang 404 chuyên nghiệp hơn ở đây
    header("HTTP/1.0 404 Not Found");
    echo "Lỗi 404: Không tìm thấy trang.";
    exit();
}
