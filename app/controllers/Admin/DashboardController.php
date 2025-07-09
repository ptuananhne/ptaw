<?php

class DashboardController extends Controller
{
    private $statisticModel;

    public function __construct()
    {
        // Bảo vệ: Yêu cầu đăng nhập để truy cập
        if (!isset($_SESSION['admin_id'])) {
            // SỬA LỖI: Chuyển hướng đến đúng controller xử lý login là AuthController
           header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/auth/login');
            exit;
        }

        // Tải model thống kê
        $this->statisticModel = $this->model('Statistic');
    }

    /**
     * Hiển thị trang dashboard với các số liệu thống kê
     */
    public function index()
    {
        // Lấy dữ liệu thống kê từ model
        $productCount = $this->statisticModel->getProductCount();
        $categoryCount = $this->statisticModel->getCategoryCount();
        $brandCount = $this->statisticModel->getBrandCount();
        $bannerCount = $this->statisticModel->getBannerCount();

        $data = [
            'title' => 'Trang quản trị',
            'username' => $_SESSION['admin_username'] ?? 'Admin',
            'productCount' => $productCount,
            'categoryCount' => $categoryCount,
            'brandCount' => $brandCount,
            'bannerCount' => $bannerCount,
        ];

        // Tải view cho trang dashboard và truyền dữ liệu
        $this->view('admin/dashboard', $data);
    }
}
