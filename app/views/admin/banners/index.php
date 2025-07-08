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
        <div class="w-64 min-h-screen bg-gray-800 text-white p-4 flex flex-col">
            <h2 class="text-2xl font-bold mb-6">Admin</h2>
            <nav class="flex-grow">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="block py-2.5 px-4 rounded hover:bg-gray-700">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/product" class="block py-2.5 px-4 rounded hover:bg-gray-700">Sản phẩm</a>
                <a href="<?php echo BASE_URL; ?>/admin/taxonomy" class="block py-2.5 px-4 rounded hover:bg-gray-700">Phân loại</a>
                <a href="<?php echo BASE_URL; ?>/admin/banner" class="block py-2.5 px-4 rounded bg-gray-700">Banner</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold mb-6"><?php echo $data['title']; ?></h1>
            <?php flash('banner_message'); ?>

            <div class="mb-6">
                <button id="add-banner-btn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">+ Thêm Banner mới</button>
            </div>

            <!-- Banners Table -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ảnh</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tiêu đề</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Đường dẫn</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['banners'] as $banner): ?>
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($banner->image_url); ?>" alt="<?php echo htmlspecialchars($banner->title); ?>" class="w-32 h-16 object-cover rounded">
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($banner->title); ?></p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <a href="<?php echo htmlspecialchars($banner->link_url); ?>" target="_blank" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($banner->link_url); ?></a>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                    <?php if ($banner->is_active): ?>
                                        <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                            <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                            <span class="relative">Hoạt động</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight">
                                            <span aria-hidden class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                            <span class="relative">Tạm ẩn</span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <button class="edit-btn text-indigo-600 hover:text-indigo-900 mr-4" data-banner='<?php echo json_encode($banner); ?>'>Sửa</button>
                                    <form action="<?php echo BASE_URL; ?>/admin/banner/delete/<?php echo $banner->id; ?>" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa banner này?');">
                                        <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="banner-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Thêm Banner mới</h3>
                <div class="mt-2 px-7 py-3">
                    <form id="banner-form" method="POST" enctype="multipart/form-data">
                        <div class="mb-4 text-left">
                            <label for="title" class="block text-sm font-medium text-gray-700">Tiêu đề</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div class="mb-4 text-left">
                            <label for="link_url" class="block text-sm font-medium text-gray-700">Đường dẫn (URL)</label>
                            <input type="url" name="link_url" id="link_url" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="https://example.com">
                        </div>
                        <div class="mb-4 text-left">
                            <label for="image" class="block text-sm font-medium text-gray-700">Ảnh Banner</label>
                            <input type="file" name="image" id="image" class="mt-1 block w-full text-sm">
                            <img id="image-preview" src="" class="mt-2 w-full h-auto rounded hidden">
                        </div>
                        <div class="mb-4 text-left">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-900">Hiển thị banner</span>
                            </label>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button id="submit-btn" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                Lưu
                            </button>
                            <button id="close-modal-btn" type="button" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Hủy
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('banner-modal');
            const addBtn = document.getElementById('add-banner-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const bannerForm = document.getElementById('banner-form');
            const modalTitle = document.getElementById('modal-title');
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');

            function openModal() {
                modal.classList.remove('hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                bannerForm.reset();
                bannerForm.action = '';
                imagePreview.classList.add('hidden');
                imagePreview.src = '';
            }

            addBtn.addEventListener('click', () => {
                modalTitle.textContent = 'Thêm Banner mới';
                bannerForm.action = '<?php echo BASE_URL; ?>/admin/banner/add';
                openModal();
            });

            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const banner = JSON.parse(this.dataset.banner);
                    modalTitle.textContent = 'Sửa Banner';
                    bannerForm.action = `<?php echo BASE_URL; ?>/admin/banner/edit/${banner.id}`;

                    document.getElementById('title').value = banner.title;
                    document.getElementById('link_url').value = banner.link_url;
                    document.getElementById('is_active').checked = banner.is_active == 1;

                    imagePreview.src = `<?php echo BASE_URL; ?>/${banner.image_url}`;
                    imagePreview.classList.remove('hidden');

                    openModal();
                });
            });

            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            closeModalBtn.addEventListener('click', closeModal);
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        });
    </script>
</body>

</html>