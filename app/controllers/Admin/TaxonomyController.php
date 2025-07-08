<?php

class TaxonomyController extends Controller
{
    private $taxonomyModel;

    public function __construct()
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/auth/login');
            exit;
        }
        $this->taxonomyModel = $this->model('Taxonomy');
    }

    public function index()
    {
        $data = [
            'title' => 'Quản lý Phân loại',
            'categories' => $this->taxonomyModel->getCategories(),
            'brands' => $this->taxonomyModel->getBrands()
        ];
        $this->view('admin/taxonomy/index', $data);
    }

    // --- CATEGORY ACTIONS ---
    public function addCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['category_name'])) {
            if ($this->taxonomyModel->createCategory(trim($_POST['category_name']))) {
                flash('taxonomy_message', 'Thêm danh mục thành công!');
            } else {
                flash('taxonomy_message', 'Thêm danh mục thất bại.', 'bg-red-100 text-red-700');
            }
        }
        header('Location: ' . BASE_URL . '/admin/taxonomy');
    }

    public function deleteCategory($id)
    {
        if ($this->taxonomyModel->deleteCategory($id)) {
            flash('taxonomy_message', 'Xóa danh mục thành công!');
        } else {
            flash('taxonomy_message', 'Xóa danh mục thất bại. Có thể do danh mục này vẫn còn sản phẩm.', 'bg-red-100 text-red-700');
        }
        header('Location: ' . BASE_URL . '/admin/taxonomy');
    }

    // --- BRAND ACTIONS ---
    public function addBrand()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['brand_name'])) {
            $logoPath = '';
            if (isset($_FILES['brand_logo']) && $_FILES['brand_logo']['error'] == UPLOAD_ERR_OK) {
                $uploadResult = $this->handleLogoUpload($_FILES['brand_logo']);
                if ($uploadResult['success']) {
                    $logoPath = $uploadResult['path'];
                } else {
                    flash('taxonomy_message', $uploadResult['error'], 'bg-red-100 text-red-700');
                    header('Location: ' . BASE_URL . '/admin/taxonomy');
                    exit;
                }
            }
            if ($this->taxonomyModel->createBrand(trim($_POST['brand_name']), $logoPath)) {
                flash('taxonomy_message', 'Thêm thương hiệu thành công!');
            } else {
                flash('taxonomy_message', 'Thêm thương hiệu thất bại.', 'bg-red-100 text-red-700');
            }
        }
        header('Location: ' . BASE_URL . '/admin/taxonomy');
    }

    public function deleteBrand($id)
    {
        if ($this->taxonomyModel->deleteBrand($id)) {
            flash('taxonomy_message', 'Xóa thương hiệu thành công!');
        } else {
            flash('taxonomy_message', 'Xóa thương hiệu thất bại. Có thể do thương hiệu này vẫn còn sản phẩm.', 'bg-red-100 text-red-700');
        }
        header('Location: ' . BASE_URL . '/admin/taxonomy');
    }

    // --- RELATIONSHIP ACTIONS (AJAX) ---
    public function getBrandLinks($categoryId)
    {
        header('Content-Type: application/json');
        $linkedBrandIds = $this->taxonomyModel->getBrandIdsForCategory($categoryId);
        echo json_encode($linkedBrandIds);
    }

    public function updateLinks()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $categoryId = $_POST['category_id'] ?? 0;
            $brandIds = $_POST['brand_ids'] ?? [];
            if ($this->taxonomyModel->updateCategoryBrandLinks($categoryId, $brandIds)) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật liên kết thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cập nhật liên kết thất bại.']);
            }
        }
    }

    // --- HELPER ---
    private function handleLogoUpload($file)
    {
        $uploadDir = 'uploads/brands/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = uniqid() . '-' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'path' => $targetPath];
        }
        return ['success' => false, 'error' => 'Lỗi khi tải logo lên.'];
    }
}
