<?php
class App
{
    // Các giá trị mặc định
    protected $currentController = 'HomeController'; // Mặc định cho Client
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->getUrl();

        if (isset($url[0]) && strtolower($url[0]) == ADMIN_ROUTE_PREFIX) {
            $controllerPath = '../app/controllers/Admin/';
            array_shift($url); 

            if (isset($url[0]) && file_exists($controllerPath . ucwords($url[0]) . 'Controller.php')) {
                $this->currentController = ucwords($url[0]) . 'Controller';
                array_shift($url); 
            } else {
             
                $this->currentController = 'DashboardController';
            }
        } else {
            $controllerPath = '../app/controllers/Client/';
            if (isset($url[0]) && file_exists($controllerPath . ucwords($url[0]) . 'Controller.php')) {
                $this->currentController = ucwords($url[0]) . 'Controller';
                array_shift($url);
            }
        }
        $controllerFile = $controllerPath . $this->currentController . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            die("Lỗi hệ thống: Không tìm thấy file controller '{$this->currentController}' tại: '{$controllerFile}'");
        }
        $this->currentController = new $this->currentController;

        if (isset($url[0]) && method_exists($this->currentController, $url[0])) {
            $this->currentMethod = $url[0];
            array_shift($url);
        }

        $this->params = $url ? array_values($url) : [];

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
