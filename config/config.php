<?php
// BẬT CHẾ ĐỘ BÁO LỖI ĐỂ GỠ RỐI (An toàn để thêm vào)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


define('DB_HOST', 'localhost');
define('DB_USER', 'root'); 
define('DB_PASS', '');     
define('DB_NAME', 'pta');

define('BASE_URL', 'camdophut89.com');


define('APP_ROOT', dirname(dirname(__FILE__)));
define('SITE_NAME', 'PHÚT 89');
define('ADMIN_ROUTE_PREFIX', 'adminphut89@!');