<?php
// Cấu hình kết nối Cơ sở dữ liệu
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Thay bằng user của bạn
define('DB_PASS', '');     // Thay bằng mật khẩu của bạn
define('DB_NAME', 'pta');

// ===================================================================
// CẤU HÌNH ĐƯỜNG DẪN GỐC (URL ROOT) - SỬA Ở ĐÂY
// Thay thế 'http://pta.test' bằng địa chỉ web bạn dùng để truy cập.
// Ví dụ: http://localhost/pta, http://pta.test, ...
// Đảm bảo không có dấu gạch chéo (/) ở cuối.
// ===================================================================
define('BASE_URL', 'http://pta.test');


// (Tùy chọn) Các hằng số khác
define('APP_ROOT', dirname(dirname(__FILE__)));
define('SITE_NAME', 'PTA | Bán hàng trực tuyến');
