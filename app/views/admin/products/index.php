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
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold"><?php echo $data['title']; ?></h1>
                <a href="<?php echo BASE_URL; ?>/admin/auth/logout" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Đăng xuất</a>
            </div>

            <!-- Flash Message -->
            <div class="mb-4">
                <?php flash('product_message'); ?>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 bg-white p-4 rounded-lg shadow">
                <input type="text" id="filter-name" placeholder="Tìm theo tên sản phẩm..." class="px-4 py-2 border rounded-md">
                <select id="filter-category" class="px-4 py-2 border rounded-md">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($data['categories'] as $category): ?>
                        <option value="<?php echo $category->id; ?>"><?php echo htmlspecialchars($category->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="filter-brand" class="px-4 py-2 border rounded-md">
                    <option value="">Tất cả thương hiệu</option>
                    <?php foreach ($data['brands'] as $brand): ?>
                        <option value="<?php echo $brand->id; ?>"><?php echo htmlspecialchars($brand->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Sửa link Thêm Mới -->
                <a href="<?php echo BASE_URL; ?>/admin/product/add" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center leading-tight">+ Thêm Mới</a>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ảnh</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tên sản phẩm</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Giá</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lượt xem</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        <!-- Dữ liệu sản phẩm và phân trang sẽ được chèn vào đây bởi JavaScript -->
                    </tbody>
                </table>
                <div id="pagination-container" class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
                    <!-- Pagination links will be generated here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterName = document.getElementById('filter-name');
            const filterCategory = document.getElementById('filter-category');
            const filterBrand = document.getElementById('filter-brand');
            const tableBody = document.getElementById('product-table-body');
            const paginationContainer = document.getElementById('pagination-container');

            let initialData = {
                products: <?php echo json_encode($data['products']); ?>,
                totalPages: <?php echo $data['totalPages']; ?>,
                currentPage: <?php echo $data['currentPage']; ?>
            };

            function renderProducts(products) {
                tableBody.innerHTML = '';
                if (products.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-500">Không tìm thấy sản phẩm nào phù hợp.</td></tr>';
                    return;
                }
                products.forEach(product => {
                    const priceFormatted = new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(product.price);
                    const row = `
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><img src="${'<?php echo BASE_URL; ?>/' + escapeHTML(product.image_url)}" alt="${escapeHTML(product.name)}" class="w-16 h-16 object-cover rounded"></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap">${escapeHTML(product.name)}</p></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap">${priceFormatted}</p></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap">${product.view_count}</p></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <a href="<?php echo BASE_URL; ?>/admin/product/edit/${product.id}" class="text-indigo-600 hover:text-indigo-900 mr-4">Sửa</a>
                        <form action="<?php echo BASE_URL; ?>/admin/product/delete/${product.id}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                            <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                        </form>
                    </td>
                </tr>`;
                    tableBody.innerHTML += row;
                });
            }

            function renderPagination(totalPages, currentPage) {
                paginationContainer.innerHTML = '';
                if (totalPages <= 1) return;

                let paginationHTML = '<div class="flex items-center">';
                // Previous Button
                paginationHTML += `<button data-page="${currentPage - 1}" class="pagination-link mx-1 px-3 py-1 rounded-md text-sm font-medium ${currentPage === 1 ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}" ${currentPage === 1 ? 'disabled' : ''}>&laquo; Trước</button>`;

                // Page Numbers
                for (let i = 1; i <= totalPages; i++) {
                    paginationHTML += `<button data-page="${i}" class="pagination-link mx-1 px-3 py-1 rounded-md text-sm font-medium ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'}">${i}</button>`;
                }

                // Next Button
                paginationHTML += `<button data-page="${currentPage + 1}" class="pagination-link mx-1 px-3 py-1 rounded-md text-sm font-medium ${currentPage === totalPages ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}" ${currentPage === totalPages ? 'disabled' : ''}>Sau &raquo;</button>`;
                paginationHTML += '</div>';
                paginationContainer.innerHTML = paginationHTML;
            }

            async function fetchAndRender(page = 1) {
                const name = filterName.value;
                const category = filterCategory.value;
                const brand = filterBrand.value;
                const url = `<?php echo BASE_URL; ?>/admin/product/ajax?page=${page}&name=${encodeURIComponent(name)}&category=${encodeURIComponent(category)}&brand=${encodeURIComponent(brand)}`;

                try {
                    const response = await fetch(url);
                    const data = await response.json();
                    renderProducts(data.products);
                    renderPagination(data.totalPages, data.currentPage);
                } catch (error) {
                    console.error('Lỗi khi tải dữ liệu:', error);
                }
            }

            async function updateBrandFilter(categoryId) {
                const url = `<?php echo BASE_URL; ?>/admin/product/getBrandsForCategory/${categoryId}`;
                try {
                    const response = await fetch(url);
                    const brands = await response.json();
                    filterBrand.innerHTML = '<option value="">Tất cả thương hiệu</option>';
                    brands.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand.id;
                        option.textContent = escapeHTML(brand.name);
                        filterBrand.appendChild(option);
                    });
                } catch (error) {
                    console.error('Lỗi khi lấy danh sách thương hiệu:', error);
                }
            }

            function escapeHTML(str) {
                if (str === null || str === undefined) return '';
                return str.toString().replace(/[&<>"']/g, match => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                })[match]);
            }

            // Event Listeners
            filterName.addEventListener('keyup', () => fetchAndRender(1));
            filterBrand.addEventListener('change', () => fetchAndRender(1));
            filterCategory.addEventListener('change', function() {
                updateBrandFilter(this.value).then(() => fetchAndRender(1));
            });
            paginationContainer.addEventListener('click', function(e) {
                if (e.target.matches('button.pagination-link')) {
                    e.preventDefault();
                    const page = e.target.dataset.page;
                    if (page) {
                        fetchAndRender(parseInt(page));
                    }
                }
            });

            // Initial Render
            renderProducts(initialData.products);
            renderPagination(initialData.totalPages, initialData.currentPage);
        });
    </script>
</body>

</html>