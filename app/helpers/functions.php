<?php

/**
 * Chuyển hướng người dùng đến một URL được chỉ định.
 * @param string $url URL đích.
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit();
}

/**
 * Định dạng số thành tiền tệ Việt Nam.
 * @param float $number Số tiền cần định dạng.
 * @return string Chuỗi tiền tệ đã định dạng (ví dụ: 32,000,000 ₫).
 */
function format_currency($number)
{
    if (!is_numeric($number)) {
        return 'N/A';
    }
    return number_format($number, 0, ',', '.') . ' ₫';
}

/**
 * Cắt ngắn một chuỗi văn bản và thêm dấu "..."
 * @param string $text Chuỗi cần cắt.
 * @param int $maxLength Độ dài tối đa.
 * @return string Chuỗi đã được cắt ngắn.
 */
function truncate_text($text, $maxLength = 100)
{
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength) . '...';
    }
    return $text;
}

/**
 * Tạo slug từ một chuỗi (ví dụ: "Sản Phẩm Mới" -> "san-pham-moi").
 * Rất hữu ích cho việc tạo URL thân thiện SEO.
 * @param string $string Chuỗi đầu vào.
 * @return string Slug đã được tạo.
 */
function create_slug($string)
{
    $string = preg_replace('/[^\pL\d]+/u', '-', $string);
    $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    $string = preg_replace('/[^-\w]+/', '', $string);
    $string = trim($string, '-');
    $string = preg_replace('/-+/', '-', $string);
    $string = strtolower($string);
    if (empty($string)) {
        return 'n-a';
    }
    return $string;
}
