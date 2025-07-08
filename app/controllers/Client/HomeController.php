<?php

// Đảm bảo không có mã nào nằm ngoài lớp
class HomeController extends Controller
{
    public function __construct()
    {
        // Models sẽ được tải khi cần
    }

    public function index()
    {
        // Tải các model cần thiết
        $categoryModel = $this->model('Category');
        $productModel = $this->model('Product');
        $bannerModel = $this->model('Banner');

        // Lấy dữ liệu từ các model
        $activeBanners = $bannerModel->getAllActiveBanners();
        $featuredProducts = $productModel->getTopViewedProducts(8);
        $allCategories = $categoryModel->getAllCategories();
        $productsByCategory = $productModel->getTopProductsGroupedByAllCategories(8);

        // Chuẩn bị dữ liệu để gửi cho view
        $data = [
            'title' => 'Trang chủ - PTA | Thế giới công nghệ',
            'categories' => $allCategories,
            'banners' => $activeBanners,
            'featuredProducts' => $featuredProducts,
            'productsByCategory' => $productsByCategory
        ];

        // Gọi view và truyền dữ liệu
        $this->view('client/home', $data);
    }
}
