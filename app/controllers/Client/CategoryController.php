<?php
class CategoryController extends Controller
{

    public function index($slug = '')
    {
        if (empty($slug)) {
            header('Location: ' . BASE_URL);
            exit();
        }

        $categoryModel = $this->model('Category');
        $productModel = $this->model('Product');

        $category = $categoryModel->getCategoryBySlug($slug);
        if (!$category) {
            echo "404 - Category not found";
            exit();
        }

        // --- LỌC VÀ PHÂN TRANG ---
        $products_per_page = 8;
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $products_per_page;

        // Lấy các tham số lọc từ URL
        $brand_filter = $_GET['brand'] ?? '';
        $sort_filter = $_GET['sort'] ?? 'views_desc';

        $filter_options = [
            'category_slug' => $slug,
            'brand_id' => $brand_filter,
            'sort_by' => $sort_filter,
        ];

        // Đếm tổng số sản phẩm với bộ lọc hiện tại
        $total_products = $productModel->countFilteredProducts($filter_options);
        $total_pages = ceil($total_products / $products_per_page);

        // Lấy sản phẩm cho trang hiện tại
        $products = $productModel->getFilteredProducts(array_merge($filter_options, [
            'limit' => $products_per_page,
            'offset' => $offset
        ]));

        $data = [
            'title' => '' . htmlspecialchars($category->name),
            'category' => $category,
            'products' => $products,
            'brands' => $categoryModel->getBrandsByCategorySlug($slug),
            'categories' => $categoryModel->getAllCategories(),
            'pagination' => ['current' => $current_page, 'total' => $total_pages],
            'filters' => ['brand' => $brand_filter, 'sort' => $sort_filter],
            'current_category_slug' => $slug // Để highlight menu
        ];

        $this->view('client/category', $data);
    }
}
