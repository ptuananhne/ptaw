<?php

class AuthController extends Controller
{
    private $adminModel;

    public function __construct()
    {
        $this->adminModel = $this->model('Admin');
    }

    /**
     * Hiển thị và xử lý form đăng nhập
     */
    public function login()
    {
        // Nếu đã đăng nhập, chuyển hướng đến dashboard
        if (isset($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit;
        }

        $data = ['username' => '', 'password' => '', 'error' => ''];

        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // SỬA LỖI: Loại bỏ hàm filter_input_array với hằng số đã lỗi thời.
            // Thay vào đó, chúng ta sẽ xử lý trực tiếp dữ liệu đã được làm sạch.
            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'error' => ''
            ];

            // Validate dữ liệu
            if (empty($data['username']) || empty($data['password'])) {
                $data['error'] = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
            }

            // Nếu không có lỗi validation
            if (empty($data['error'])) {
                // Tìm admin bằng username
                $admin = $this->adminModel->findByUsername($data['username']);

                // Kiểm tra admin và so sánh mật khẩu
                if ($admin && password_verify($data['password'], $admin->password_hash)) {
                    // Đăng nhập thành công, tạo session
                    $_SESSION['admin_id'] = $admin->id;
                    $_SESSION['admin_username'] = $admin->username;
                    // Chuyển hướng đến trang dashboard
                    header('Location: ' . BASE_URL . '/admin/dashboard');
                    exit;
                } else {
                    // Đăng nhập thất bại
                    $data['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
                }
            }
        }

        // Hiển thị lại form đăng nhập với lỗi (nếu có)
        $this->view('admin/login', $data);
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        session_destroy();
        header('Location: ' . BASE_URL . '/admin/auth/login');
        exit;
    }
}
