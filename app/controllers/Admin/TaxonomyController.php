<?php
class TaxonomyController extends Controller
{
    /**
     * Hiển thị trang quản lý chung cho cả Danh mục và Thương hiệu.
     */
    public function index()
    {
        $categoryModel = $this->model('Category');
        $brandModel = $this->model('Brand');

        $data = [
            'title' => 'Danh mục & Thương hiệu',
            'categories' => $categoryModel->getAll(),
            'brands' => $brandModel->getAll()
        ];

        $this->view('admin/manage_taxonomies', $data);
    }

    /**
     * Lưu một danh mục mới.
     */
    public function storeCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['name'])) {
            $categoryModel = $this->model('Category');
            $data = [
                'name' => trim($_POST['name']),
                'slug' => create_slug(trim($_POST['name']))
            ];
            $categoryModel->create($data);
        }
        redirect(BASE_URL . '/admin.php?url=taxonomy');
    }

    /**
     * Xóa một danh mục.
     */
    public function deleteCategory($id)
    {
        $categoryModel = $this->model('Category');
        $categoryModel->delete($id);
        redirect(BASE_URL . '/admin.php?url=taxonomy');
    }

    /**
     * Lưu một thương hiệu mới.
     */
    public function storeBrand()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['name'])) {
            $brandModel = $this->model('Brand');
            $data = [
                'name' => trim($_POST['name']),
                'slug' => create_slug(trim($_POST['name']))
            ];
            $brandModel->create($data);
        }
        redirect(BASE_URL . '/admin.php?url=taxonomy');
    }

    /**
     * Xóa một thương hiệu.
     */
    public function deleteBrand($id)
    {
        $brandModel = $this->model('Brand');
        $brandModel->delete($id);
        redirect(BASE_URL . '/admin.php?url=taxonomy');
    }
}
