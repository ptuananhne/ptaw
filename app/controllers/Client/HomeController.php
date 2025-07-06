<?php
class HomeController extends Controller
{

    public function __construct()
    {
        // Models will be loaded when needed
    }

    public function index()
    {
        $categoryModel = $this->model('Category');
        $productModel = $this->model('Product');
        $bannerModel = $this->model('Banner');

        // Lấy tất cả banner hoạt động cho slider
        $activeBanners = $bannerModel->getAllActiveBanners();

        // Lấy 8 sản phẩm nổi bật (xem nhiều nhất)
        $featuredProducts = $productModel->getTopViewedProducts(8);

        // Lấy tất cả danh mục (vẫn cần cho sidebar)
        $allCategories = $categoryModel->getAllCategories();

        // === TỐI ƯU HÓA TẠI ĐÂY ===
        // Thay vì lặp và query nhiều lần, chúng ta gọi phương thức mới
        // để lấy tất cả sản phẩm cần thiết trong 1 lần query duy nhất.
        $productsByCategory = $productModel->getTopProductsGroupedByAllCategories(8);

        $data = [
            'title' => 'Trang chủ - PTA | Thế giới công nghệ',
            'categories' => $allCategories,
            'banners' => $activeBanners,
            'featuredProducts' => $featuredProducts,
            'productsByCategory' => $productsByCategory
        ];

        $this->view('client/home', $data);
    }
}
