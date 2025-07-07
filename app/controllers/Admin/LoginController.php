<?php
class LoginController extends Controller
{
    public function index()
    {
        if (isset($_SESSION['admin_logged_in'])) {
            redirect(BASE_URL . '/admin.php?url=dashboard');
        }

        $data = ['title' => 'Đăng nhập Quản trị'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $adminModel = $this->model('Admin');
            $admin = $adminModel->findByUsername($username);

            // --- LOGIC GỠ LỖI ---
            // Kiểm tra từng bước để tìm ra lỗi chính xác
            if (!$admin) {
                // Lỗi 1: Tên đăng nhập không tồn tại trong CSDL.
                $data['error'] = 'Tên đăng nhập không tồn tại.';
            } elseif (!password_verify($password, $admin->password_hash)) {
                // Lỗi 2: Tên đăng nhập đúng, nhưng mật khẩu sai.
                $data['error'] = 'Mật khẩu không chính xác.';
            } else {
                // Thành công: Cả hai đều đúng.
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin->username;
                $_SESSION['admin_id'] = $admin->id;
                redirect(BASE_URL . '/admin.php?url=dashboard');
            }
        }

        $this->view('admin/login', $data);
    }

    public function logout()
    {
        session_destroy();
        redirect(BASE_URL . '/admin.php?url=login');
    }
}
