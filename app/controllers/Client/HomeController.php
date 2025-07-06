<?php
class HomeController extends Controller
{

    public function __construct()
    {
        // Models sẽ được tải khi cần
    }

    public function index()
    {
        $categoryModel = $this->model('Category');
        $productModel = $this->model('Product');
        $bannerModel = $this->model('Banner');

        // Lấy banner
        $activeBanner = $bannerModel->getActiveBanner();

        // Lấy 8 sản phẩm nổi bật (xem nhiều nhất)
        $featuredProducts = $productModel->getTopViewedProducts(8);

        // Lấy tất cả danh mục
        $allCategories = $categoryModel->getAllCategories();

        // Lấy sản phẩm xem nhiều nhất cho mỗi danh mục
        $productsByCategory = [];
        foreach ($allCategories as $category) {
            // Cần có ID trong câu query của getAllCategories
            // Tạm thời sẽ query lại để lấy ID, cách tốt hơn là cập nhật hàm getAllCategories
            $catInfo = $categoryModel->getCategoryBySlug($category->slug);
            if ($catInfo) {
                $productsByCategory[$category->name] = [
                    'slug' => $category->slug,
                    'products' => $productModel->getTopViewedProductsByCategory($catInfo->id, 8)
                ];
            }
        }

        $data = [
            'title' => 'Trang chủ - PTA | Thế giới công nghệ',
            'categories' => $allCategories, // Cho sidebar
            'activeBanner' => $activeBanner,
            'featuredProducts' => $featuredProducts,
            'productsByCategory' => $productsByCategory
        ];

        $this->view('client/home', $data);
    }
}
