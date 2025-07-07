<?php
class LoginController extends Controller
{
    public function index()
    {
        // Nếu đã đăng nhập, chuyển thẳng đến dashboard
        if (isset($_SESSION['admin_logged_in'])) {
            redirect(BASE_URL . '/admin.php?url=dashboard');
        }

        $data = ['title' => 'Đăng nhập Quản trị'];

        // Xử lý khi người dùng gửi form đăng nhập
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // Nạp model Admin
            $adminModel = $this->model('Admin');
            $admin = $adminModel->findByUsername($username);

            // Xác thực người dùng
            // password_verify sẽ so sánh mật khẩu người dùng nhập với chuỗi hash trong DB
            if ($admin && password_verify($password, $admin->password_hash)) {
                // Đăng nhập thành công, lưu thông tin vào session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin->username;
                $_SESSION['admin_id'] = $admin->id;
                redirect(BASE_URL . '/admin.php?url=dashboard');
            } else {
                // Đăng nhập thất bại
                $data['error'] = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
            }
        }

        // Hiển thị view đăng nhập
        $this->view('admin/login', $data);
    }

    /**
     * Xử lý đăng xuất.
     */
    public function logout()
    {
        // Hủy tất cả session
        session_unset();
        session_destroy();
        // Chuyển về trang đăng nhập
        redirect(BASE_URL . '/admin.php?url=login');
    }
}
