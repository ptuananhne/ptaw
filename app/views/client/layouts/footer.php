</main>
<footer class="bg-gray-800 text-white mt-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4">PTA</h3>
                <p class="text-gray-400">Trang web quảng bá sản phẩm công nghệ hàng đầu Việt Nam. Cung cấp thông tin chi tiết, chính xác và nhanh chóng.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Danh mục</h3>
                <ul>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <li class="mb-2">
                                <a href="<?= BASE_URL . '/category/' . $category->slug ?>" class="text-gray-400 hover:text-white transition-colors">
                                    <?= htmlspecialchars($category->name) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Liên hệ</h3>
                <p class="text-gray-400">Email: contact@pta.com</p>
                <p class="text-gray-400">Điện thoại: (0236) 3 123 456</p>
                <p class="text-gray-400">Địa chỉ: Đà Nẵng, Việt Nam</p>
            </div>
        </div>
        <div class="mt-8 border-t border-gray-700 pt-6 text-center text-gray-500">
            <p>&copy; <?= date('Y') ?> PTA. All rights reserved.</p>
        </div>
    </div>
</footer>
</body>

</html>