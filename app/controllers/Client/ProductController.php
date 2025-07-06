<?php
class ProductController extends Controller
{

    public function __construct()
    {
        // Constructor
    }

    public function index($slug = '')
    {
        if (empty($slug)) {
            header('Location: ' . BASE_URL);
            exit();
        }

        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');

        // Lấy thông tin sản phẩm
        $product = $productModel->getProductBySlug($slug);

        if (!$product) {
            // Xử lý trang 404
            echo "404 - Product not found";
            exit();
        }

        // Tăng lượt xem
        $productModel->incrementViewCount($product->id);

        // Lấy thư viện ảnh
        $gallery = $productModel->getProductGallery($product->id);

        // Lấy danh sách danh mục cho sidebar
        $allCategories = $categoryModel->getAllCategories();

        $data = [
            'title' => htmlspecialchars($product->name),
            'product' => $product,
            'gallery' => $gallery,
            'categories' => $allCategories
        ];

        $this->view('client/product_detail', $data);
    }
}
