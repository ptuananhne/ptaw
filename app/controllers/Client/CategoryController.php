<?php
class CategoryController extends Controller
{

    private $categoryModel;
    private $productModel;

    public function __construct()
    {
        $this->categoryModel = $this->model('Category');
        $this->productModel = $this->model('Product');
    }

    /**
     * Hiển thị trang danh sách sản phẩm theo danh mục.
     * @param string $slug Slug của danh mục từ URL.
     */
    public function index($slug = '')
    {
        if (empty($slug)) {
            // Nếu không có slug, chuyển hướng về trang chủ hoặc trang lỗi
            header('Location: ' . BASE_URL);
            exit();
        }

        // Lấy thông tin danh mục hiện tại
        $category = $this->categoryModel->getCategoryBySlug($slug);

        if (!$category) {
            // Xử lý trường hợp không tìm thấy danh mục (hiển thị trang 404)
            echo "404 - Category not found";
            exit();
        }

        // Lấy tất cả sản phẩm thuộc danh mục này
        $products = $this->productModel->getProductsByCategorySlug($slug);

        // Lấy tất cả danh mục để hiển thị trên header
        $allCategories = $this->categoryModel->getAllCategories();

        $data = [
            'title' => 'Danh mục: ' . htmlspecialchars($category->name),
            'category' => $category,
            'products' => $products,
            'categories' => $allCategories // Dữ liệu cho header
        ];

        $this->view('client/category', $data);
    }
}
