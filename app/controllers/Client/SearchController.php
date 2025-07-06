<?php
class SearchController extends Controller
{

    public function index()
    {
        // Sửa lại cách lấy và làm sạch từ khóa
        $keyword = trim(filter_input(INPUT_GET, 'q', FILTER_DEFAULT) ?? '');

        if (empty($keyword)) {
            header('Location: ' . BASE_URL);
            exit();
        }

        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');

        // Logic phân trang
        $products_per_page = 8;
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $products_per_page;

        $search_options = [
            'keyword' => $keyword
        ];

        // Đếm tổng số sản phẩm tìm thấy
        $total_products = $productModel->countSearchedProducts($search_options);
        $total_pages = ceil($total_products / $products_per_page);

        // Lấy sản phẩm cho trang hiện tại
        $products = $productModel->searchProducts(array_merge($search_options, [
            'limit' => $products_per_page,
            'offset' => $offset
        ]));

        $data = [
            'title' => 'Kết quả tìm kiếm cho: "' . htmlspecialchars($keyword) . '"',
            'keyword' => $keyword,
            'products' => $products,
            'categories' => $categoryModel->getAllCategories(),
            'pagination' => ['current' => $current_page, 'total' => $total_pages],
            'total_results' => $total_products
        ];

        $this->view('client/search_results', $data);
    }
}
