<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../app/helpers/functions.php';

spl_autoload_register(function ($className) {
    $corePath = '../app/core/' . $className . '.php';
    if (file_exists($corePath)) {
        require_once $corePath;
        return;
    }
    $modelPath = '../app/models/' . $className . '.php';
    if (file_exists($modelPath)) {
        require_once $modelPath;
        return;
    }
    $clientControllerPath = '../app/controllers/Client/' . $className . '.php';
    if (file_exists($clientControllerPath)) {
        require_once $clientControllerPath;
        return;
    }
    $adminControllerPath = '../app/controllers/Admin/' . $className . '.php';
    if (file_exists($adminControllerPath)) {
        require_once $adminControllerPath;
        return;
    }
});
$app = new App();
