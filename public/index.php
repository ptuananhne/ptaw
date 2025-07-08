<?php
// Bắt đầu session để quản lý trạng thái đăng nhập
session_start();

// Tải các file cấu hình và core cần thiết
require_once '../config/config.php';
require_once '../app/models/Database.php';
require_once '../app/core/Controller.php';

// --- LOGIC ĐIỀU TUYẾN (ROUTING) NÂNG CAO ---

// 1. Phân tích URL
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$url = filter_var($url, FILTER_SANITIZE_URL);
$params = !empty($url) ? explode('/', $url) : [];

// 2. Điều hướng cho khu vực ADMIN
if (isset($params[0]) && $params[0] == 'admin') {
    // Controller mặc định cho admin là AuthController (để login)
    $controllerName = 'AuthController';
    $actionName = 'index'; // Action mặc định

    // Xác định Controller từ URL
    if (!empty($params[1])) {
        $controllerName = ucfirst($params[1]) . 'Controller';
    }

    // Xác định Action từ URL
    if (!empty($params[2])) {
        $actionName = $params[2];
    }

    // Đường dẫn tới file controller của admin
    $controllerFile = '../app/controllers/Admin/' . $controllerName . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controller = new $controllerName();

        // Lấy các tham số còn lại (nếu có)
        $finalParams = array_slice($params, 3);

        // Kiểm tra action có tồn tại không
        if (method_exists($controller, $actionName)) {
            call_user_func_array([$controller, $actionName], $finalParams);
        } else {
            // Lỗi 404 nếu không tìm thấy action
            echo "Admin action not found: " . htmlspecialchars($actionName);
        }
    } else {
        // Lỗi 404 nếu không tìm thấy controller
        echo "Admin controller not found: " . htmlspecialchars($controllerName);
    }
} else {
    // 3. Điều hướng cho khu vực CLIENT (Giao diện người dùng)
    // Giữ nguyên logic cũ của bạn cho client
    $controllerName = !empty($params[0]) ? ucfirst($params[0]) . 'Controller' : 'HomeController';
    $controllerFile = '../app/controllers/Client/' . $controllerName . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controller = new $controllerName();
        unset($params[0]);

        $actionName = 'index';
        if (!empty($params[1]) && method_exists($controller, $params[1])) {
            $actionName = $params[1];
            unset($params[1]);
        }

        $finalParams = $params ? array_values($params) : [];
        call_user_func_array([$controller, $actionName], $finalParams);
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Lỗi 404: Không tìm thấy trang.";
        exit();
    }
}
