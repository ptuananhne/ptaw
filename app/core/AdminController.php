<?php
class AdminController extends Controller
{
    public function __construct()
    {
        // Kiểm tra xem session admin_logged_in có được set và có giá trị true không
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            // Nếu chưa đăng nhập, hủy mọi session hiện có
            session_destroy();
            // Chuyển hướng về trang đăng nhập của admin
            header('Location: ' . BASE_URL . '/admin/auth/login');
            exit();
        }
    }
}
