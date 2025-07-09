<?php
// Đổi tên class để tránh xung đột với PHP 8+
class ProductAttributeController extends Controller
{
    // Đổi tên biến model để nhất quán
    private $productAttributeModel;

    public function __construct()
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/auth/login');
            exit;
        }
        // Gọi đến model đã được đổi tên
        $this->productAttributeModel = $this->model('ProductAttribute');
    }

    public function index()
    {
        $data = [
            'title' => 'Quản lý Thuộc tính',
            'attributes' => $this->productAttributeModel->getAllWithTerms()
        ];
        $this->view('admin/attributes/index', $data);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            if (!empty($name)) {
                if ($this->productAttributeModel->create($name)) {
                    flash('attribute_message', 'Thêm thuộc tính thành công.');
                } else {
                    flash('attribute_message', 'Thêm thất bại, có thể tên thuộc tính đã tồn tại.', 'bg-red-100 text-red-700');
                }
            } else {
                flash('attribute_message', 'Tên thuộc tính không được để trống.', 'bg-red-100 text-red-700');
            }
        }
        header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/productAttribute');
        exit;
    }

    public function edit($id)
    {
        $attribute = $this->productAttributeModel->findById($id);
        if (!$attribute) {
            // Cập nhật đường dẫn
            header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/productAttribute');
            exit;
        }

        $data = [
            'title' => 'Sửa Thuộc tính: ' . htmlspecialchars($attribute->name),
            'attribute' => $attribute
        ];
        $this->view('admin/attributes/edit', $data);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $terms_json = $_POST['terms'] ?? '[]';
            
            $terms_data = json_decode($terms_json, true);
            $terms = is_array($terms_data) ? array_map(fn($t) => $t['value'], $terms_data) : [];

            if (!empty($name)) {
                $this->productAttributeModel->update($id, $name);
                $this->productAttributeModel->syncTerms($id, $terms);
                flash('attribute_message', 'Cập nhật thuộc tính thành công.');
            } else {
                flash('attribute_message', 'Tên thuộc tính không được để trống.', 'bg-red-100 text-red-700');
            }
        }
        // Cập nhật đường dẫn
        header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/productAttribute/edit/' . $id);
        exit;
    }

    public function destroy($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->productAttributeModel->delete($id)) {
                flash('attribute_message', 'Xóa thuộc tính thành công.');
            } else {
                flash('attribute_message', 'Xóa thất bại.', 'bg-red-100 text-red-700');
            }
        }
        // Cập nhật đường dẫn
       header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/productAttribute');
        exit;
    }
}
