<?php
// Bắt đầu session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tải file config
require_once '../config/config.php';

// Tải các helpers
require_once '../app/helpers/functions.php';

// Autoload Core Libraries
spl_autoload_register(function ($className) {
    // Kiểm tra trong thư mục core
    $corePath = '../app/core/' . $className . '.php';
    if (file_exists($corePath)) {
        require_once $corePath;
        return;
    }

    // Kiểm tra trong thư mục models
    $modelPath = '../app/models/' . $className . '.php';
    if (file_exists($modelPath)) {
        require_once $modelPath;
        return;
    }

    // Kiểm tra trong thư mục controllers/Client
    $clientControllerPath = '../app/controllers/Client/' . $className . '.php';
    if (file_exists($clientControllerPath)) {
        require_once $clientControllerPath;
        return;
    }

    // Kiểm tra trong thư mục controllers/Admin
    $adminControllerPath = '../app/controllers/Admin/' . $className . '.php';
    if (file_exists($adminControllerPath)) {
        require_once $adminControllerPath;
        return;
    }
});


// Khởi tạo ứng dụng
$app = new App();
