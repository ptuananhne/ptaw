<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tagify{ --tag-bg: #3b82f6; --tag-text-color: white; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
          <div class="w-64 min-h-screen bg-gray-800 text-white p-4 flex text-center item-center flex-col">
            <h2 class="text-2xl font-bold mb-6">Admin Panel</h2><a href="<?php echo BASE_URL; ?>/<?php echo ADMIN_ROUTE_PREFIX; ?>n/auth/logout" 
                class="block text-center py-2.5 px-4 rounded bg-red-500 hover:bg-red-600">
                Đăng xuất
                </a>
            <nav class="flex-grow">
                <a href="<?php echo BASE_URL; ?>/<?php echo ADMIN_ROUTE_PREFIX; ?>/dashboard" class="block py-2.5 px-4 rounded hover:bg-gray-700">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/<?php echo ADMIN_ROUTE_PREFIX; ?>/product" class="block py-2.5 px-4 rounded hover:bg-gray-700">Sản phẩm</a>
                <a href="<?php echo BASE_URL; ?>/<?php echo ADMIN_ROUTE_PREFIX; ?>/taxonomy" class="block py-2.5 px-4 rounded hover:bg-gray-700">Phân loại</a>
                <a href="<?php echo BASE_URL; ?>/<?php echo ADMIN_ROUTE_PREFIX; ?>/banner" class="block py-2.5 px-4 rounded hover:bg-gray-700">Banner</a>
                <a href="<?php echo BASE_URL; ?>/<?php echo ADMIN_ROUTE_PREFIX; ?>/productattribute" class="block py-2.5 px-4 rounded hover:bg-gray-700">Thuộc tính</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold"><?php echo $data['title']; ?></h1>
                <a href="<?php echo BASE_URL; ?>/<?php echo ADMIN_ROUTE_PREFIX; ?>/productAttribute" class="text-blue-500 hover:text-blue-700">&larr; Quay lại danh sách thuộc tính</a>
            </div>
            <?php flash('attribute_message'); ?>

            <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
                <form action="<?php echo BASE_URL; ?>/<?php echo ADMIN_ROUTE_PREFIX; ?>/productAttribute/update/<?php echo $data['attribute']->id; ?>" method="POST">
                    <div class="mb-6">
                        <label for="name" class="block text-gray-700 font-bold mb-2">Tên thuộc tính:</label>
                        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($data['attribute']->name); ?>" class="shadow-sm border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-6">
                        <label for="terms" class="block text-gray-700 font-bold mb-2">Các giá trị (nhấn Enter để thêm):</label>
                        <input name="terms" id="terms" value='<?php echo json_encode(array_column($data['attribute']->terms, 'name')); ?>'>
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        var input = document.querySelector('input[name=terms]');
        new Tagify(input);
    </script>
</body>
</html>
