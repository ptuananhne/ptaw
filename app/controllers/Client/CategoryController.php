<?php
// File: app/controllers/Client/CategoryController.php

class CategoryController extends Controller
{
    /**
     * Hiển thị trang danh sách sản phẩm của một danh mục cụ thể.
     * Hỗ trợ phân trang và lọc theo đúng cấu trúc ban đầu.
     * @param string $slug Slug của danh mục.
     * @param int $page Số trang hiện tại.
     */
    public function index($slug = '', $page = 1)
    {
        // Nếu không có slug, chuyển hướng về trang chủ
        if (empty($slug)) {
            redirect(BASE_URL);
            return;
        }

        // Nạp các model cần thiết
        $categoryModel = $this->model('Category');
        $productModel = $this->model('Product');

        // Lấy thông tin của danh mục hiện tại bằng slug
        $currentCategory = $categoryModel->getCategoryBySlug($slug);

        // Nếu không tìm thấy danh mục, hiển thị trang lỗi 404
        if (!$currentCategory) {
            http_response_code(404);
            $this->view('client/404');
            return;
        }

        // --- PHỤC HỒI LẠI TÊN BIẾN BỘ LỌC THEO YÊU CẦU ---
        $filters = [
            'brand' => $_GET['brand'] ?? null,
            'sort'  => $_GET['sort'] ?? 'views_desc'
        ];

        // --- Xử lý Phân trang ---
        $limit = 8; // Cấu hình: 12 sản phẩm mỗi trang
        $offset = ($page > 1) ? ($page - 1) * $limit : 0;

        // Các tùy chọn để truy vấn CSDL (vẫn dùng key chuẩn của Model)
        $options = [
            'category_slug' => $slug,
            'brand_id'      => $filters['brand'],
            'sort_by'       => $filters['sort'],
            'limit'         => $limit,
            'offset'        => $offset
        ];

        // Lấy danh sách sản phẩm
        $products = $productModel->getFilteredProducts($options);

        // Đếm tổng số sản phẩm để tính toán phân trang
        $totalProducts = $productModel->countFilteredProducts($options);

        // --- PHỤC HỒI LẠI BIẾN $pagination THEO YÊU CẦU ---
        $pagination = [
            'total'   => ceil($totalProducts / $limit),
            'current' => (int)$page
        ];

        // Lấy dữ liệu cho các bộ lọc
        $brandsForFilter = $categoryModel->getBrandsByCategorySlug($slug);
        $allCategories = $categoryModel->getAll();

        // Chuẩn bị toàn bộ dữ liệu để gửi cho View
        $data = [
            'title'      => 'Danh mục ' . htmlspecialchars($currentCategory->name),
            'category'   => $currentCategory,
            'products'   => $products,
            'brands'     => $brandsForFilter,
            'categories' => $allCategories,
            'pagination' => $pagination, // Gửi mảng pagination đúng tên
            'filters'    => $filters     // Gửi mảng filters đúng tên
        ];

        // Tải view và truyền dữ liệu vào
        $this->view('client/category', $data);
    }
}
