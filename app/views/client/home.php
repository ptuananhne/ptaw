<?php
// Tải header (bao gồm cả phần mở đầu của thẻ main)
$this->view('client/layouts/header', $data);
?>

<!-- Hero Section -->
<div class="bg-blue-600 rounded-lg shadow-lg overflow-hidden mb-12">
    <div class="p-8 md:p-12 flex flex-col md:flex-row items-center">
        <div class="md:w-1/2 text-white text-center md:text-left">
            <h1 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight">Khám Phá Thế Giới Công Nghệ</h1>
            <p class="text-lg text-blue-200 mb-6">Tất cả sản phẩm công nghệ hàng đầu từ các thương hiệu nổi tiếng đều có tại PTA.</p>
            <a href="#featured-products" class="bg-white text-blue-600 font-bold py-3 px-6 rounded-full hover:bg-blue-100 transition duration-300">Xem sản phẩm nổi bật</a>
        </div>
        <div class="md:w-1/2 mt-8 md:mt-0">
            <img src="https://placehold.co/800x600/FFFFFF/3B82F6?text=PTA+Store" alt="Hero Image" class="rounded-lg shadow-2xl">
        </div>
    </div>
</div>

<!-- Featured Products Section -->
<section id="featured-products">
    <h2 class="text-3xl font-bold text-gray-800 mb-2 text-center">Sản phẩm nổi bật</h2>
    <p class="text-center text-gray-500 mb-8">Những sản phẩm đang được quan tâm nhất hiện nay</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php if (!empty($featured_products)): ?>
            <?php foreach ($featured_products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
                    <a href="<?= BASE_URL . '/product/' . $product->slug ?>" class="block">
                        <img src="<?= htmlspecialchars($product->image_url ?? 'https://placehold.co/600x600/CCCCCC/FFFFFF?text=No+Image') ?>" alt="<?= htmlspecialchars($product->name) ?>" class="w-full h-56 object-cover">
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
        <?php else: ?>
            <p class="col-span-full text-center text-gray-500">Chưa có sản phẩm nổi bật nào.</p>
        <?php endif; ?>
    </div>
</section>

<?php
// Tải footer (bao gồm cả phần kết thúc của thẻ main)
$this->view('client/layouts/footer', $data);
?>