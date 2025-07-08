<?php
// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Tạo hoặc hiển thị thông báo flash.
 * @param string $name Tên của thông báo.
 * @param string $message Nội dung thông báo.
 * @param string $class Lớp CSS cho thông báo (ví dụ: 'bg-green-100 text-green-700').
 */
function flash($name = '', $message = '', $class = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative')
{
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

/**
 * Tạo slug từ một chuỗi.
 * @param string $text
 * @return string
 */
function create_slug($text)
{
    // Chuyển hết sang chữ thường
    $text = strtolower($text);
    // Xóa các ký tự có dấu
    $text = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $text);
    $text = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $text);
    $text = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $text);
    $text = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $text);
    $text = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $text);
    $text = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $text);
    $text = preg_replace('/(đ)/', 'd', $text);
    // Xóa các ký tự đặc biệt
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    // Thay thế khoảng trắng bằng dấu gạch ngang
    $text = preg_replace('/[\s-]+/', '-', $text);
    // Xóa các dấu gạch ngang ở đầu và cuối
    $text = trim($text, '-');
    return $text;
}
