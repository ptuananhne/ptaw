<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 min-h-screen bg-gray-800 text-white p-4">
            <h2 class="text-2xl font-bold mb-6">Admin</h2>
            <nav>
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="block py-2.5 px-4 rounded hover:bg-gray-700">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/product" class="block py-2.5 px-4 rounded bg-gray-700">Sản phẩm</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold"><?php echo $data['title']; ?></h1>
                <a href="<?php echo BASE_URL; ?>/admin/product" class="text-blue-500 hover:text-blue-700">&larr; Quay lại danh sách</a>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md">
                <form action="<?php echo BASE_URL; ?>/admin/product/add" method="POST" enctype="multipart/form-data">
                    <!-- Tên sản phẩm -->
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Tên sản phẩm:</label>
                        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>" class="shadow appearance-none border <?php echo !empty($data['errors']['name']) ? 'border-red-500' : ''; ?> rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php if (!empty($data['errors']['name'])): ?><p class="text-red-500 text-xs italic mt-2"><?php echo $data['errors']['name']; ?></p><?php endif; ?>
                    </div>

                    <!-- Giá -->
                    <div class="mb-4">
                        <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Giá:</label>
                        <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($data['price'] ?? ''); ?>" class="shadow appearance-none border <?php echo !empty($data['errors']['price']) ? 'border-red-500' : ''; ?> rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php if (!empty($data['errors']['price'])): ?><p class="text-red-500 text-xs italic mt-2"><?php echo $data['errors']['price']; ?></p><?php endif; ?>
                    </div>

                    <!-- Danh mục & Thương hiệu -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Danh mục:</label>
                            <select name="category_id" id="category_id" class="shadow appearance-none border <?php echo !empty($data['errors']['category_id']) ? 'border-red-500' : ''; ?> rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($data['categories'] as $category): ?>
                                    <option value="<?php echo $category->id; ?>" <?php echo (isset($data['category_id']) && $data['category_id'] == $category->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($data['errors']['category_id'])): ?><p class="text-red-500 text-xs italic mt-2"><?php echo $data['errors']['category_id']; ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label for="brand_id" class="block text-gray-700 text-sm font-bold mb-2">Thương hiệu:</label>
                            <select name="brand_id" id="brand_id" class="shadow appearance-none border <?php echo !empty($data['errors']['brand_id']) ? 'border-red-500' : ''; ?> rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">-- Chọn thương hiệu --</option>
                                <?php foreach ($data['brands'] as $brand): ?>
                                    <option value="<?php echo $brand->id; ?>" <?php echo (isset($data['brand_id']) && $data['brand_id'] == $brand->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($brand->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($data['errors']['brand_id'])): ?><p class="text-red-500 text-xs italic mt-2"><?php echo $data['errors']['brand_id']; ?></p><?php endif; ?>
                        </div>
                    </div>

                    <!-- Mô tả -->
                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Mô tả:</label>
                        <textarea name="description" id="description" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                    </div>

                    <!-- Thông số kỹ thuật -->
                    <div class="mb-4">
                        <label for="specifications" class="block text-gray-700 text-sm font-bold mb-2">Thông số kỹ thuật (JSON):</label>
                        <textarea name="specifications" id="specifications" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline font-mono"><?php echo htmlspecialchars($data['specifications'] ?? ''); ?></textarea>
                    </div>

                    <!-- Ảnh sản phẩm -->
                    <div class="mb-6">
                        <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Ảnh đại diện:</label>
                        <input type="file" name="image" id="image" class="shadow appearance-none border <?php echo !empty($data['errors']['image']) ? 'border-red-500' : ''; ?> rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php if (!empty($data['errors']['image'])): ?><p class="text-red-500 text-xs italic mt-2"><?php echo $data['errors']['image']; ?></p><?php endif; ?>
                    </div>

                    <!-- Nút Submit -->
                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Lưu sản phẩm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>