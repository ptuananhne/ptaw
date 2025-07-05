<?php
// Tải header
$this->view('client/layouts/header', $data);
?>

<div class="mb-8">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="list-none p-0 inline-flex">
            <li class="flex items-center">
                <a href="<?= BASE_URL ?>" class="hover:text-blue-600">Trang chủ</a>
                <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                    <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                </svg>
            </li>
            <li>
                <span class="font-semibold text-gray-700">
                    <?= htmlspecialchars($category->name) ?>
                </span>
            </li>
        </ol>
    </nav>
    <h1 class="text-4xl font-bold text-gray-800 mt-2">
        Danh mục: <?= htmlspecialchars($category->name) ?>
    </h1>
</div>

<!-- Product Listing -->
<section>
    <?php if (!empty($products)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
                    <a href="<?= BASE_URL . '/product/' . $product->slug ?>" class="block">
                        <img src="<?= BASE_URL . '/' . htmlspecialchars($product->image_url ?? 'https://placehold.co/600x600/CCCCCC/FFFFFF?text=No+Image') ?>" alt="<?= htmlspecialchars($product->name) ?>" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <p class="text-sm text-gray-500 mb-1"><?= htmlspecialchars($product->category_name) ?></p>
                            <h3 class="text-lg font-semibold text-gray-900 truncate"><?= htmlspecialchars($product->name) ?></h3>
                            <div class="mt-4">
                                <span class="text-blue-600 font-bold">Xem chi tiết</span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-16 bg-gray-100 rounded-lg">
            <h2 class="text-2xl font-semibold text-gray-700">Rất tiếc!</h2>
            <p class="text-gray-500 mt-2">Chưa có sản phẩm nào trong danh mục này.</p>
        </div>
    <?php endif; ?>
</section>

<?php
// Tải footer
$this->view('client/layouts/footer', $data);
?>