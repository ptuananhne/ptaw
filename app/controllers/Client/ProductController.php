<?php
class ProductController extends Controller
{
    /**
     * Hiển thị trang chi tiết của một sản phẩm.
     * @param string $slug Slug của sản phẩm.
     */
    public function index($slug = '')
    {
        if (empty($slug)) {
            // Nếu không có slug, có thể chuyển hướng về trang chủ hoặc trang 404
            redirect(BASE_URL);
        }

        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');

        // Lấy thông tin chi tiết sản phẩm bằng slug
        $product = $productModel->getProductBySlug($slug);

        // Nếu không tìm thấy sản phẩm, hiển thị trang lỗi 404
        if (!$product) {
            http_response_code(404);
            $this->view('client/404'); // Giả sử bạn có view 404
            return;
        }

        // Tăng lượt xem cho sản phẩm
        $productModel->incrementViewCount($product->id);

        // Lấy ảnh từ thư viện
        $gallery = $productModel->getProductGallery($product->id);

        // Lấy tất cả danh mục để hiển thị (ví dụ: ở header)
        // SỬA LỖI Ở ĐÂY: Đổi tên hàm từ getAllCategories() thành getAll()
        $allCategories = $categoryModel->getAll();

        $data = [
            'title' => htmlspecialchars($product->name),
            'product' => $product,
            'gallery' => $gallery,
            'categories' => $allCategories
        ];

        $this->view('client/product_detail', $data);
    }
}
