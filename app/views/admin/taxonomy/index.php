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

        .category-item.active {
            background-color: #4A5568;
            color: white;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 min-h-screen bg-gray-800 text-white p-4 flex flex-col">
            <h2 class="text-2xl font-bold mb-6">Admin</h2>
            <nav class="flex-grow">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="block py-2.5 px-4 rounded hover:bg-gray-700">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/product" class="block py-2.5 px-4 rounded hover:bg-gray-700">Sản phẩm</a>
                <a href="<?php echo BASE_URL; ?>/admin/taxonomy" class="block py-2.5 px-4 rounded bg-gray-700">Phân loại</a>
                <!-- LINK MỚI -->
                <a href="<?php echo BASE_URL; ?>/admin/banner" class="block py-2.5 px-4 rounded hover:bg-gray-700">Banner</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold mb-6"><?php echo $data['title']; ?></h1>
            <?php flash('taxonomy_message'); ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Categories Column -->
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col">
                    <h3 class="text-xl font-semibold mb-4 border-b pb-2">Danh mục</h3>
                    <form action="<?php echo BASE_URL; ?>/admin/taxonomy/addCategory" method="POST" class="mb-4">
                        <div class="flex gap-2">
                            <input type="text" name="category_name" placeholder="Tên danh mục mới..." class="flex-1 shadow-sm border rounded px-3 py-2" required>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+</button>
                        </div>
                    </form>
                    <!-- Tối ưu: Thêm ô lọc -->
                    <input type="text" id="category-filter" placeholder="Tìm danh mục..." class="w-full shadow-sm border rounded px-3 py-2 mb-2">
                    <div id="category-list" class="space-y-2 flex-grow overflow-y-auto" style="max-height: 50vh;">
                        <?php foreach ($data['categories'] as $category): ?>
                            <div data-id="<?php echo $category->id; ?>" class="category-item flex justify-between items-center p-2 rounded hover:bg-gray-200 cursor-pointer">
                                <span class="category-name"><?php echo htmlspecialchars($category->name); ?> (<?php echo $category->product_count; ?>)</span>
                                <form action="<?php echo BASE_URL; ?>/admin/taxonomy/deleteCategory/<?php echo $category->id; ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Xóa</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Linking Column -->
                <div id="linking-panel" class="bg-white p-6 rounded-lg shadow-md flex-col hidden">
                    <h3 class="text-xl font-semibold mb-4 border-b pb-2">Liên kết cho <span id="linking-category-name" class="font-bold"></span></h3>
                    <!-- Tối ưu: Thêm ô lọc -->
                    <input type="text" id="brand-link-filter" placeholder="Tìm thương hiệu..." class="w-full shadow-sm border rounded px-3 py-2 mb-2">
                    <form id="linking-form" class="flex-grow flex flex-col">
                        <div id="brand-checkbox-list" class="space-y-2 flex-grow overflow-y-auto" style="max-height: 45vh;">
                            <!-- Checkboxes will be loaded here by JS -->
                        </div>
                        <button type="submit" class="mt-4 w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Lưu liên kết</button>
                    </form>
                </div>

                <!-- Brands Column -->
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col">
                    <h3 class="text-xl font-semibold mb-4 border-b pb-2">Thương hiệu</h3>
                    <form action="<?php echo BASE_URL; ?>/admin/taxonomy/addBrand" method="POST" enctype="multipart/form-data" class="mb-4 space-y-2">
                        <input type="text" name="brand_name" placeholder="Tên thương hiệu mới..." class="w-full shadow-sm border rounded px-3 py-2" required>
                        <input type="file" name="brand_logo" class="w-full text-sm">
                        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Thêm thương hiệu</button>
                    </form>
                    <!-- Tối ưu: Thêm ô lọc -->
                    <input type="text" id="brand-filter" placeholder="Tìm thương hiệu..." class="w-full shadow-sm border rounded px-3 py-2 mb-2">
                    <div id="brand-list" class="space-y-2 flex-grow overflow-y-auto" style="max-height: 40vh;">
                        <?php foreach ($data['brands'] as $brand): ?>
                            <div class="brand-item flex justify-between items-center p-2 rounded">
                                <div class="flex items-center gap-2">
                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($brand->logo_url); ?>" class="w-8 h-8 object-contain" alt="">
                                    <span class="brand-name"><?php echo htmlspecialchars($brand->name); ?> (<?php echo $brand->product_count; ?>)</span>
                                </div>
                                <form action="<?php echo BASE_URL; ?>/admin/taxonomy/deleteBrand/<?php echo $brand->id; ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này?');">
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Xóa</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryList = document.getElementById('category-list');
            const linkingPanel = document.getElementById('linking-panel');
            const linkingCategoryName = document.getElementById('linking-category-name');
            const brandCheckboxList = document.getElementById('brand-checkbox-list');
            const linkingForm = document.getElementById('linking-form');

            // Tối ưu: Lấy các element của ô lọc
            const categoryFilter = document.getElementById('category-filter');
            const brandFilter = document.getElementById('brand-filter');
            const brandLinkFilter = document.getElementById('brand-link-filter');

            let allBrands = <?php echo json_encode($data['brands']); ?>;
            let selectedCategoryId = null;

            categoryList.addEventListener('click', async function(e) {
                const item = e.target.closest('.category-item');
                if (!item) return;

                document.querySelectorAll('.category-item').forEach(el => el.classList.remove('active'));
                item.classList.add('active');
                selectedCategoryId = item.dataset.id;
                linkingCategoryName.textContent = item.querySelector('span.category-name').textContent.split('(')[0].trim();

                const response = await fetch(`<?php echo BASE_URL; ?>/admin/taxonomy/getBrandLinks/${selectedCategoryId}`);
                const linkedBrandIds = await response.json();

                brandCheckboxList.innerHTML = '';
                allBrands.forEach(brand => {
                    const isChecked = linkedBrandIds.includes(String(brand.id)); // So sánh chuỗi
                    brandCheckboxList.innerHTML += `
                <label class="brand-link-item flex items-center p-2 rounded hover:bg-gray-100">
                    <input type="checkbox" name="brand_ids[]" value="${brand.id}" class="h-4 w-4" ${isChecked ? 'checked' : ''}>
                    <img src="<?php echo BASE_URL; ?>/${brand.logo_url}" class="w-6 h-6 object-contain mx-2" alt="">
                    <span class="brand-link-name ml-2">${brand.name}</span>
                </label>
            `;
                });
                linkingPanel.classList.remove('hidden');
                linkingPanel.classList.add('flex');
            });

            linkingForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (!selectedCategoryId) return;

                const formData = new FormData(linkingForm);
                formData.append('category_id', selectedCategoryId);

                const response = await fetch(`<?php echo BASE_URL; ?>/admin/taxonomy/updateLinks`, {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                });
                const result = await response.json();

                alert(result.message);
                if (result.success) {
                    linkingPanel.classList.add('hidden');
                    linkingPanel.classList.remove('flex');
                    document.querySelectorAll('.category-item').forEach(el => el.classList.remove('active'));
                }
            });

            // Tối ưu: Hàm lọc chung
            function filterList(inputElement, listContainerSelector, itemSelector, nameSelector) {
                const filterValue = inputElement.value.toLowerCase();
                const items = document.querySelectorAll(`${listContainerSelector} ${itemSelector}`);
                items.forEach(item => {
                    const name = item.querySelector(nameSelector).textContent.toLowerCase();
                    if (name.includes(filterValue)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            // Tối ưu: Gắn sự kiện cho các ô lọc
            categoryFilter.addEventListener('keyup', () => filterList(categoryFilter, '#category-list', '.category-item', '.category-name'));
            brandFilter.addEventListener('keyup', () => filterList(brandFilter, '#brand-list', '.brand-item', '.brand-name'));
            brandLinkFilter.addEventListener('keyup', () => filterList(brandLinkFilter, '#brand-checkbox-list', '.brand-link-item', '.brand-link-name'));
        });
    </script>
</body>

</html>