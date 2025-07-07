<?php
// File này dùng để tạo chuỗi mã hóa mật khẩu mới, đảm bảo tương thích với server của bạn.

// 1. Đặt mật khẩu bạn muốn sử dụng ở đây
$password_to_hash = '123';

// 2. PHP sẽ tạo chuỗi mã hóa từ mật khẩu trên
$hashed_password = password_hash($password_to_hash, PASSWORD_DEFAULT);

// 3. Hiển thị kết quả để bạn sao chép
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tạo Chuỗi Mã Hóa Mật Khẩu</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            line-height: 1.6;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            color: #555;
        }

        textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 10px;
        }

        .highlight {
            background-color: #e7e7e7;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Công cụ tạo chuỗi mã hóa (Hash)</h1>
        <p>Mật khẩu đang được mã hóa là: <strong class="highlight"><?= htmlspecialchars($password_to_hash) ?></strong></p>
        <hr>
        <p><strong>SAO CHÉP TOÀN BỘ</strong> chuỗi mã hóa dưới đây và dán vào cột <strong class="highlight">password_hash</strong> trong cơ sở dữ liệu của bạn:</p>
        <textarea rows="3" readonly onclick="this.select();"><?= htmlspecialchars($hashed_password) ?></textarea>
    </div>
</body>

</html>