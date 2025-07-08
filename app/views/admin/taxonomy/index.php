<?php
// PHP logic để xác định trang hiện tại
$current_uri = $_SERVER['REQUEST_URI'];
function is_active($path, $current_uri) {
    return strpos($current_uri, $path) !== false;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Thư viện cho Kéo-Thả -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        /* SỬA LỖI: Thêm lại style cho mục được chọn */
        .category-item.active { background-color: #4A5568; color: white; } 
        .sortable-ghost { background-color: #f0f9ff; opacity: 0.7; }
        .drag-handle { cursor: move; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 min-h-screen bg-gray-800 text-white p-4 flex flex-col">
            <h2 class="text-2xl font-bold mb-6">Admin</h2>
            <nav class="flex-grow">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="block py-2.5 px-4 rounded transition duration-200 <?php echo is_active('/admin/dashboard', $current_uri) ? 'bg-gray-700' : 'hover:bg-gray-700'; ?>">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/product" class="block py-2.5 px-4 rounded transition duration-200 <?php echo is_active('/admin/product', $current_uri) ? 'bg-gray-700' : 'hover:bg-gray-700'; ?>">Sản phẩm</a>
                <a href="<?php echo BASE_URL; ?>/admin/taxonomy" class="block py-2.5 px-4 rounded transition duration-200 <?php echo is_active('/admin/taxonomy', $current_uri) ? 'bg-gray-700' : 'hover:bg-gray-700'; ?>">Phân loại</a>
                <a href="<?php echo BASE_URL; ?>/admin/banner" class="block py-2.5 px-4 rounded transition duration-200 <?php echo is_active('/admin/banner', $current_uri) ? 'bg-gray-700' : 'hover:bg-gray-700'; ?>">Banner</a>
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
                    <form id="add-category-form" action="<?php echo BASE_URL; ?>/admin/taxonomy/addCategory" method="POST" class="mb-4">
                        <div class="flex gap-2">
                            <input type="text" name="category_name" placeholder="Tên danh mục mới..." class="flex-1 shadow-sm border rounded px-3 py-2" required>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+</button>
                        </div>
                    </form>
                    <input type="text" id="category-filter" placeholder="Tìm danh mục..." class="w-full shadow-sm border rounded px-3 py-2 mb-2">
                    <div id="category-list" class="space-y-2 flex-grow overflow-y-auto" style="max-height: 50vh;">
                        <?php foreach($data['categories'] as $category): ?>
                        <div data-id="<?php echo $category->id; ?>" class="category-item flex justify-between items-center p-2 rounded hover:bg-gray-200 cursor-pointer">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 drag-handle" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                <span class="category-name"><?php echo htmlspecialchars($category->name); ?> (<?php echo $category->product_count; ?>)</span>
                            </div>
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
                    <form id="add-brand-form" action="<?php echo BASE_URL; ?>/admin/taxonomy/addBrand" method="POST" enctype="multipart/form-data" class="mb-4 space-y-2">
                        <input type="text" name="brand_name" placeholder="Tên thương hiệu mới..." class="w-full shadow-sm border rounded px-3 py-2" required>
                        <input type="file" name="brand_logo" class="w-full text-sm">
                        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Thêm thương hiệu</button>
                    </form>
                    <input type="text" id="brand-filter" placeholder="Tìm thương hiệu..." class="w-full shadow-sm border rounded px-3 py-2 mb-2">
                    <div id="brand-list" class="space-y-2 flex-grow overflow-y-auto" style="max-height: 40vh;">
                        <?php foreach($data['brands'] as $brand): ?>
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

            const categoryFilter = document.getElementById('category-filter');
            const brandFilter = document.getElementById('brand-filter');
            const brandLinkFilter = document.getElementById('brand-link-filter');

            const addCategoryForm = document.getElementById('add-category-form');
            const addBrandForm = document.getElementById('add-brand-form');

            let allBrands = <?php echo json_encode($data['brands']); ?>;
            let selectedCategoryId = null;

            // --- Kéo-Thả Sắp xếp ---
            new Sortable(categoryList, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    const orderIds = Array.from(categoryList.querySelectorAll('.category-item')).map(item => item.dataset.id);
                    const formData = new FormData();
                    orderIds.forEach(id => formData.append('order[]', id));

                    fetch('<?php echo BASE_URL; ?>/admin/taxonomy/updateCategoryOrder', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                alert('Lỗi khi cập nhật thứ tự danh mục.');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

            // --- Logic chính ---
            categoryList.addEventListener('click', async function(e) {
                if (e.target.closest('form')) {
                    return;
                }
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
            // SỬA LỖI: Chuyển đổi ID sang chuỗi trước khi so sánh
            const isChecked = linkedBrandIds.map(String).includes(String(brand.id));
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

            // --- Hàm lọc & Gắn sự kiện ---
            function filterList(inputElement, listContainerSelector, itemSelector, nameSelector) {
                const filterValue = inputElement.value.toLowerCase();
                const items = document.querySelectorAll(`${listContainerSelector} ${itemSelector}`);
                items.forEach(item => {
                    const name = item.querySelector(nameSelector).textContent.toLowerCase();
                    item.style.display = name.includes(filterValue) ? 'flex' : 'none';
                });
            }

            categoryFilter.addEventListener('keyup', () => filterList(categoryFilter, '#category-list', '.category-item', '.category-name'));
            brandFilter.addEventListener('keyup', () => filterList(brandFilter, '#brand-list', '.brand-item', '.brand-name'));
            brandLinkFilter.addEventListener('keyup', () => filterList(brandLinkFilter, '#brand-checkbox-list', '.brand-link-item', '.brand-link-name'));

            function handleFormSubmit(formElement) {
                formElement.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    fetch(this.action, {
                        method: 'POST',
                        body: formData
                    }).then(response => {
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert('Có lỗi xảy ra, vui lòng thử lại.');
                        }
                    }).catch(error => {
                        console.error('Form submission error:', error);
                        alert('Lỗi mạng hoặc server.');
                    });
                });
            }

            handleFormSubmit(addCategoryForm);
            handleFormSubmit(addBrandForm);
        });
    </script>
</body>

</html>