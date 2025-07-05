<?php
class Controller
{
    /**
     * Tải file view và truyền dữ liệu cho nó.
     * @param string $view Đường dẫn tới file view (ví dụ: 'client/home')
     * @param array $data Dữ liệu cần truyền cho view
     */
    public function view($view, $data = [])
    {
        // Giải nén mảng data thành các biến riêng lẻ
        extract($data);
        $viewFile = '../app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: " . $viewFile);
        }
    }

    /**
     * Tải một file model.
     * @param string $model Tên của model (ví dụ: 'Category')
     * @return object Instance của model
     */
    public function model($model)
    {
        $modelFile = '../app/models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("Model not found: " . $modelFile);
        }
    }
}
