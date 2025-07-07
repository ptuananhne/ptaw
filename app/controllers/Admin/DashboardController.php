<?php
class DashboardController extends Controller
{
    public function index()
    {
        // Nạp các model cần thiết để lấy dữ liệu thống kê
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        $brandModel = $this->model('Brand');

        // Lấy dữ liệu thống kê từ các model
        $totalProducts = $productModel->countAll();
        $totalCategories = $categoryModel->countAll();
        $totalBrands = $brandModel->countAll();

        // Chuẩn bị dữ liệu để truyền cho view
        $data = [
            'title' => 'Bảng điều khiển',
            'total_products' => $totalProducts,
            'total_categories' => $totalCategories,
            'total_brands' => $totalBrands,
        ];

        // Hiển thị view dashboard và truyền dữ liệu vào
        $this->view('admin/dashboard', $data);
    }
}
