<?php
// QUAN TRỌNG: Kế thừa từ AdminController để được bảo vệ
require_once '../app/core/AdminController.php';

class DashboardController extends AdminController
{
    public function __construct()
    {
        // Gọi constructor của lớp cha (AdminController) để kiểm tra đăng nhập
        parent::__construct();
    }

    public function index()
    {
        // Tải các model cần thiết để lấy số liệu thống kê
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        $brandModel = $this->model('Brand');

        $data = [
            'title' => 'Bảng điều khiển',
            'total_products' => $productModel->countAll(),
            'total_categories' => $categoryModel->countAll(),
            'total_brands' => $brandModel->countAll(),
            // Thêm các dữ liệu khác bạn muốn hiển thị ở đây
        ];

        $this->view('admin/dashboard', $data);
    }
}
