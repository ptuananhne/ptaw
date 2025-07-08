<?php
/*
 * App Core Class
 * Creates URL & loads core controller
 * URL FORMAT - /controller/method/params
 * Phiên bản này xử lý cả route cho Admin và Client một cách rõ ràng.
 */
class App
{
    // Các giá trị mặc định
    protected $currentController = 'HomeController'; // Mặc định cho Client
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->getUrl();

        // --- B1: KIỂM TRA ROUTE ADMIN HAY CLIENT ---
        if (isset($url[0]) && strtolower($url[0]) == 'admin') {
            // === ĐÂY LÀ ROUTE CỦA ADMIN ===
            $controllerPath = '../app/controllers/Admin/';
            array_shift($url); // Xóa 'admin' khỏi URL, ta còn lại [controller, method, ...]

            // B2 (Admin): Tìm Controller. Mặc định là DashboardController.
            // Ví dụ URL: /admin/auth/login -> controller là 'auth'
            if (isset($url[0]) && file_exists($controllerPath . ucwords($url[0]) . 'Controller.php')) {
                $this->currentController = ucwords($url[0]) . 'Controller';
                array_shift($url); // Xóa controller khỏi URL, ta còn lại [method, ...]
            } else {
                // Nếu không có controller trong URL (vd: /admin), dùng controller mặc định
                $this->currentController = 'DashboardController';
            }
        } else {
            // === ĐÂY LÀ ROUTE CỦA CLIENT ===
            $controllerPath = '../app/controllers/Client/';

            // B2 (Client): Tìm Controller. Mặc định là HomeController.
            // Ví dụ URL: /category/dien-thoai -> controller là 'category'
            if (isset($url[0]) && file_exists($controllerPath . ucwords($url[0]) . 'Controller.php')) {
                $this->currentController = ucwords($url[0]) . 'Controller';
                array_shift($url);
            }
        }

        // --- CÁC BƯỚC CHUNG ---
        // B3: Tải và khởi tạo Controller
        $controllerFile = $controllerPath . $this->currentController . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            die("Lỗi hệ thống: Không tìm thấy file controller '{$this->currentController}' tại: '{$controllerFile}'");
        }
        $this->currentController = new $this->currentController;

        // B4: Tìm phương thức (Method)
        // Ví dụ URL: /admin/auth/login -> method là 'login'
        if (isset($url[0]) && method_exists($this->currentController, $url[0])) {
            $this->currentMethod = $url[0];
            array_shift($url);
        }

        // B5: Lấy các tham số (Params)
        $this->params = $url ? array_values($url) : [];

        // B6: Gọi phương thức
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    /**
     * Lấy và làm sạch URL
     * @return array
     */
    public function getUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
