<?php
// BẬT CHẾ ĐỘ BÁO LỖI ĐỂ GỠ RỐI (An toàn để thêm vào)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cấu hình kết nối Cơ sở dữ liệu (Giữ nguyên của bạn)
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Thay bằng user của bạn
define('DB_PASS', '');     // Thay bằng mật khẩu của bạn
define('DB_NAME', 'pta');

// ===================================================================
// CẤU HÌNH ĐƯỜNG DẪN GỐC (URL ROOT) - (Giữ nguyên của bạn)
// ===================================================================
define('BASE_URL', 'http://pta.test');


// (Tùy chọn) Các hằng số khác (Giữ nguyên của bạn)
define('APP_ROOT', dirname(dirname(__FILE__)));
define('SITE_NAME', 'PHÚT 89');
