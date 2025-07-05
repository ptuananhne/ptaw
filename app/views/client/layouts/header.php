<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'PTA | Thế giới công nghệ') ?></title>
    <!-- Tải Tailwind CSS từ CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- File CSS tùy chỉnh -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <script>
        // Cấu hình Tailwind để sử dụng font Be Vietnam Pro
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Be Vietnam Pro', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 font-sans">
    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="<?= BASE_URL ?>" class="text-2xl font-bold text-blue-600">PTA</a>
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-4">
                            <a href="<?= BASE_URL ?>" class="text-gray-700 hover:bg-blue-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Trang chủ</a>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <a href="<?= BASE_URL . '/category/' . $category->slug ?>" class="text-gray-700 hover:bg-blue-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                        <?= htmlspecialchars($category->name) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="relative">
                        <input type="search" placeholder="Tìm kiếm sản phẩm..." class="bg-gray-100 border-2 border-gray-200 rounded-full py-2 px-4 pl-10 focus:outline-none focus:border-blue-500 w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button type="button" class="bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Mở menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </nav>
    </header>
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">