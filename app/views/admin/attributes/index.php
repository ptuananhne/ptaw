<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
       <!-- Sidebar -->
   <div class="w-64 min-h-screen bg-gray-800 text-white p-4 flex flex-col">
            <h2 class="text-2xl font-bold mb-6">Admin</h2>
            <nav class="flex-grow">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="block py-2.5 px-4 rounded hover:bg-gray-700">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/product" class="block py-2.5 px-4 rounded hover:bg-gray-700">Sản phẩm</a>
                <a href="<?php echo BASE_URL; ?>/admin/taxonomy" class="block py-2.5 px-4 rounded hover:bg-gray-700">Phân loại</a>
                <a href="<?php echo BASE_URL; ?>/admin/banner" class="block py-2.5 px-4 rounded bg-gray-700">Banner</a>
                <a href="<?php echo BASE_URL; ?>/admin/productAttribute" class="block py-2.5 px-4 rounded bg-gray-700">Thuộc tính</a>
            </nav>
        </div>


        <!-- Main Content -->
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold mb-6"><?php echo $data['title']; ?></h1>
            <?php flash('attribute_message'); ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Cột trái: Danh sách thuộc tính -->
                <div class="md:col-span-2 bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-4">Danh sách thuộc tính</h3>
                    <div class="space-y-4">
                        <?php foreach ($data['attributes'] as $attribute): ?>
                            <div class="border p-4 rounded-md">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-bold text-lg"><?php echo htmlspecialchars($attribute->name); ?></h4>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            <?php foreach ($attribute->terms as $term): ?>
                                                <span class="bg-gray-200 text-gray-700 text-xs font-semibold px-2.5 py-0.5 rounded-full"><?php echo htmlspecialchars($term->name); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="<?php echo BASE_URL; ?>/admin/productAttribute/edit/<?php echo $attribute->id; ?>" class="text-blue-500 hover:underline">Sửa</a>
                                        <form action="<?php echo BASE_URL; ?>/admin/productAttribute/destroy/<?php echo $attribute->id; ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa thuộc tính này và tất cả giá trị của nó?');">
                                            <button type="submit" class="text-red-500 hover:underline">Xóa</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Cột phải: Thêm thuộc tính mới -->
                <div class="bg-white p-6 rounded-lg shadow h-fit">
                    <h3 class="text-xl font-semibold mb-4">Thêm thuộc tính mới</h3>
                    <form action="<?php echo BASE_URL; ?>/admin/productAttribute/store" method="POST">
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 font-bold mb-2">Tên thuộc tính:</label>
                            <input type="text" name="name" id="name" placeholder="VD: Màu sắc" class="shadow-sm border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Thêm mới</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
