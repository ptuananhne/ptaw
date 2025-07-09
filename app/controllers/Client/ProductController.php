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
            // Thay vì echo, nên gọi một view 404 chuyên nghiệp hơn
            $this->view('client/404'); 
            return;
        }

        // Tăng lượt xem
        $productModel->incrementViewCount($product->id);

        // Lấy thư viện ảnh
        $gallery = $productModel->getProductGallery($product->id);

        // Lấy danh sách danh mục cho sidebar
        $allCategories = $categoryModel->getAllCategories();

        // Khởi tạo mảng $data
        $data = [
            'title' => htmlspecialchars($product->name),
            'product' => $product,
            'gallery' => $gallery,
            'categories' => $allCategories
        ];
        
        // =============================================================
        // === BỔ SUNG LOGIC LẤY DỮ LIỆU BIẾN THỂ (NÂNG CẤP V6.1) ===
        // =============================================================
        // Kiểm tra nếu sản phẩm là loại có biến thể
        if ($product->product_type === 'variable') {
            // Lấy tất cả các biến thể từ CSDL
            $data['variants'] = $productModel->getProductVariants($product->id);
            // Giải mã chuỗi JSON trong cột 'attributes' của sản phẩm chính 
            // để biết cần hiển thị những nhóm lựa chọn nào (VD: Màu sắc, Dung lượng)
            $data['product_attributes'] = json_decode($product->attributes, true);
        }
        // =============================================================

        // Truyền tất cả dữ liệu sang view
        $this->view('client/product_detail', $data);
    }
}