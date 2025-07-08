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

        .spec-row:not(:first-child) .remove-spec {
            display: block;
        }

        .remove-spec {
            display: none;
        }

        .gallery-item {
            position: relative;
        }

        .gallery-item .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 4px;
        }

        .gallery-item:hover .overlay {
            opacity: 1;
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

            <!-- Flash Message -->
            <div class="mb-4">
                <?php flash('product_message'); ?>
            </div>

            <form action="<?php echo BASE_URL; ?>/admin/product/edit/<?php echo $data['product']->id; ?>" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-3 gap-8">
                    <!-- Cột trái: Thông tin cơ bản -->
                    <div class="col-span-2 bg-white p-8 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-6">Thông tin cơ bản</h3>
                        <!-- Tên & Giá -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Tên sản phẩm:</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($data['product']->name ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>
                            <div>
                                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Giá:</label>
                                <input type="number" name="price" value="<?php echo htmlspecialchars($data['product']->price ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>
                        </div>
                        <!-- Mô tả -->
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Mô tả:</label>
                            <textarea name="description" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"><?php echo htmlspecialchars($data['product']->description ?? ''); ?></textarea>
                        </div>

                        <!-- Thông số kỹ thuật -->
                        <div class="mb-4">
                            <h4 class="text-lg font-semibold mb-2">Thông số kỹ thuật</h4>
                            <div id="specifications-container">
                                <?php
                                $specs = json_decode($data['product']->specifications, true);
                                if (!empty($specs) && is_array($specs)):
                                    foreach ($specs as $key => $value): ?>
                                        <div class="flex items-center gap-2 mb-2 spec-row">
                                            <input type="text" name="spec_key[]" placeholder="Tên thông số (VD: Màn hình)" value="<?php echo htmlspecialchars($key); ?>" class="flex-1 shadow-sm border rounded px-3 py-2">
                                            <input type="text" name="spec_value[]" placeholder="Giá trị (VD: 6.7 inch)" value="<?php echo htmlspecialchars($value); ?>" class="flex-1 shadow-sm border rounded px-3 py-2">
                                            <button type="button" class="remove-spec bg-red-500 text-white p-2 rounded">-</button>
                                        </div>
                                    <?php endforeach;
                                else: ?>
                                    <div class="flex items-center gap-2 mb-2 spec-row">
                                        <input type="text" name="spec_key[]" placeholder="Tên thông số (VD: Màn hình)" class="flex-1 shadow-sm border rounded px-3 py-2">
                                        <input type="text" name="spec_value[]" placeholder="Giá trị (VD: 6.7 inch)" class="flex-1 shadow-sm border rounded px-3 py-2">
                                        <button type="button" class="remove-spec bg-red-500 text-white p-2 rounded">-</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="button" id="add-spec" class="mt-2 text-sm text-blue-600 hover:underline">+ Thêm thông số</button>
                        </div>
                    </div>

                    <!-- Cột phải: Phân loại & Ảnh -->
                    <div class="col-span-1 space-y-8">
                        <div class="bg-white p-8 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold mb-6">Phân loại</h3>
                            <!-- Danh mục (Vô hiệu hóa) -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Danh mục (Không thể thay đổi):</label>
                                <input type="text" value="<?php echo htmlspecialchars($data['categories'][array_search($data['product']->category_id, array_column($data['categories'], 'id'))]->name ?? 'N/A'); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 bg-gray-200" disabled>
                            </div>
                            <!-- Thương hiệu -->
                            <div>
                                <label for="brand_id" class="block text-gray-700 text-sm font-bold mb-2">Thương hiệu:</label>
                                <select name="brand_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                                    <?php foreach ($data['brands'] as $brand): ?>
                                        <option value="<?php echo $brand->id; ?>" <?php echo ($data['product']->brand_id == $brand->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($brand->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold mb-6">Thư viện ảnh</h3>
                            <!-- Ảnh đại diện -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Ảnh đại diện:</label>
                                <img id="featured-image-preview" src="<?php echo BASE_URL . '/' . htmlspecialchars($data['product']->image_url); ?>" class="w-full h-48 object-contain rounded border p-1">
                            </div>
                            <!-- Thư viện ảnh -->
                            <div id="gallery-container" class="grid grid-cols-3 gap-2 mb-4">
                                <?php foreach ($data['gallery'] as $image): ?>
                                    <div class="gallery-item" data-id="<?php echo $image->id; ?>">
                                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($image->image_url); ?>" class="w-full h-24 object-cover rounded">
                                        <div class="overlay">
                                            <button type="button" class="set-featured-btn text-xs bg-blue-500 text-white px-2 py-1 rounded">Đại diện</button>
                                            <button type="button" class="delete-image-btn text-xs bg-red-500 text-white px-2 py-1 rounded">Xóa</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Tải ảnh mới -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tải thêm ảnh:</label>
                                <input type="file" name="gallery[]" multiple class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Nút Submit -->
                <div class="mt-8 flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Specifications ---
            const specContainer = document.getElementById('specifications-container');
            document.getElementById('add-spec').addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className = 'flex items-center gap-2 mb-2 spec-row';
                newRow.innerHTML = `
            <input type="text" name="spec_key[]" placeholder="Tên thông số" class="flex-1 shadow-sm border rounded px-3 py-2">
            <input type="text" name="spec_value[]" placeholder="Giá trị" class="flex-1 shadow-sm border rounded px-3 py-2">
            <button type="button" class="remove-spec bg-red-500 text-white p-2 rounded">-</button>
        `;
                specContainer.appendChild(newRow);
            });

            specContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-spec')) {
                    e.target.parentElement.remove();
                }
            });

            // --- Image Gallery ---
            const galleryContainer = document.getElementById('gallery-container');
            const featuredImagePreview = document.getElementById('featured-image-preview');
            const productId = <?php echo $data['product']->id; ?>;

            galleryContainer.addEventListener('click', async function(e) {
                const button = e.target;
                const galleryItem = button.closest('.gallery-item');
                if (!galleryItem) return;

                const imageId = galleryItem.dataset.id;
                const formData = new FormData();
                formData.append('image_id', imageId);
                formData.append('product_id', productId);

                if (button.classList.contains('delete-image-btn')) {
                    if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
                        const response = await fetch('<?php echo BASE_URL; ?>/admin/product/deleteImage', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();
                        if (result.success) {
                            galleryItem.remove();
                        } else {
                            alert('Xóa ảnh thất bại.');
                        }
                    }
                }

                if (button.classList.contains('set-featured-btn')) {
                    const response = await fetch('<?php echo BASE_URL; ?>/admin/product/setFeatured', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        featuredImagePreview.src = galleryItem.querySelector('img').src;
                        alert('Đặt ảnh đại diện thành công.');
                    } else {
                        alert('Đặt ảnh đại diện thất bại.');
                    }
                }
            });
        });
    </script>
</body>

</html>