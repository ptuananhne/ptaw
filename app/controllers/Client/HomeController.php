<?php
class HomeController extends Controller
{

    private $categoryModel;
    private $productModel;

    public function __construct()
    {
        // Khởi tạo các model cần thiết
        $this->categoryModel = $this->model('Category');
        $this->productModel = $this->model('Product');
    }

    public function index()
    {
        // Lấy dữ liệu thật từ CSDL
        $categories = $this->categoryModel->getAllCategories();
        $featured_products = $this->productModel->getFeaturedProducts(4);

        $data = [
            'title' => 'Trang chủ - PTA | Thế giới công nghệ',
            'categories' => $categories,
            'featured_products' => $featured_products
        ];

        // Tải view trang chủ và truyền dữ liệu vào
        $this->view('client/home', $data);
    }
}
