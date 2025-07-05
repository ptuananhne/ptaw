<?php
// Bắt đầu session
session_start();

// Tải file cấu hình và các lớp lõi
require_once '../config/config.php';
require_once '../app/models/Database.php'; // <--- DÒNG NÀY ĐƯỢC THÊM VÀO ĐỂ SỬA LỖI
require_once '../app/core/Controller.php';

// Logic routing đơn giản
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url = filter_var($url, FILTER_SANITIZE_URL);
$params = explode('/', $url);

$controllerName = !empty($params[0]) ? ucfirst($params[0]) . 'Controller' : 'HomeController';
$controllerFile = '../app/controllers/Client/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName;

    $actionName = isset($params[1]) ? $params[1] : 'index';

    if (method_exists($controller, $actionName)) {
        unset($params[0], $params[1]);
        call_user_func_array([$controller, $actionName], array_values($params));
    } else {
        // Có thể thay bằng một trang lỗi 404 chuyên nghiệp hơn
        echo "Action '{$actionName}' not found in controller '{$controllerName}'.";
    }
} else {
    // Có thể thay bằng một trang lỗi 404 chuyên nghiệp hơn
    echo "Controller '{$controllerName}' not found.";
}
