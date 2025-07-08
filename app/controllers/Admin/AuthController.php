<?php
class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập hoặc xử lý dữ liệu POST từ form.
     */
    public function login()
    {
        $data = ['title' => 'Admin Login'];

        // Nếu đã đăng nhập rồi thì chuyển hướng vào dashboard
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit();
        }

        // Xử lý khi người dùng gửi form (phương thức POST)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Làm sạch dữ liệu đầu vào
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            // Tải model Admin
            $adminModel = $this->model('Admin');

            // Tìm admin trong CSDL
            $admin = $adminModel->findByUsername($username);

            if ($admin && password_verify($password, $admin->password_hash)) {
                // Mật khẩu chính xác, tạo session
                $_SESSION['admin_id'] = $admin->id;
                $_SESSION['admin_username'] = $admin->username;
                $_SESSION['admin_logged_in'] = true;

                // Chuyển hướng đến trang dashboard
                header('Location: ' . BASE_URL . '/admin/dashboard');
                exit();
            } else {
                // Sai username hoặc password
                $data['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
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
        // Hủy tất cả các biến session
        $_SESSION = [];
        session_destroy();

        // Chuyển hướng về trang đăng nhập
        header('Location: ' . BASE_URL . '/admin/auth/login');
        exit();
    }

    /**
     * Hàm index mặc định, sẽ gọi đến hàm login.
     */
    public function index()
    {
        $this->login();
    }
}
