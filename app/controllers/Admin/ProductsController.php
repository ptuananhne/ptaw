<?php
require_once '../app/core/AdminController.php';

class ProductsController extends AdminController
{
    private $productModel;
    private $categoryModel;
    private $brandModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->brandModel = $this->model('Brand');
    }

    // Hiển thị danh sách sản phẩm (không thay đổi)
    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;
        $data = $this->productModel->findAllWithDetails($page, $perPage);
        $data['title'] = 'Quản lý Sản phẩm';
        $data['categories'] = $this->categoryModel->getAll();
        $data['brands'] = $this->brandModel->findAll();
        $this->view('admin/products/index', $data);
    }

    // Xử lý tìm kiếm AJAX (không thay đổi)
    public function search()
    {
        $filters = [
            'keyword' => isset($_GET['q']) ? trim($_GET['q']) : '',
            'category_id' => isset($_GET['category']) ? (int)$_GET['category'] : null,
            'brand_id' => isset($_GET['brand']) ? (int)$_GET['brand'] : null,
        ];
        $products = $this->productModel->searchAdminProducts($filters);
        header('Content-Type: application/json');
        echo json_encode($products);
        exit();
    }

    // API lấy thương hiệu (không thay đổi)
    public function getBrandsByCategory($categoryId)
    {
        $brands = $this->brandModel->findByCategoryId((int)$categoryId);
        header('Content-Type: application/json');
        echo json_encode($brands);
        exit();
    }

    // === LOGIC FORM NÂNG CẤP ===

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->processForm();
        } else {
            $this->loadFormView();
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->processForm($id);
        } else {
            $product = $this->productModel->findById($id);
            if ($product) {
                $data = (array)$product;
                $data['gallery'] = $this->productModel->getGalleryImages($id);
                $this->loadFormView($data, true);
            } else {
                header('Location: ' . BASE_URL . '/admin/products');
                exit();
            }
        }
    }

    private function processForm($id = null)
    {
        $data = [
            'name' => trim($_POST['name']),
            'slug' => trim($_POST['slug']),
            'price' => trim($_POST['price']),
            'description' => trim($_POST['description']),
            'specifications' => trim($_POST['specifications']),
            'category_id' => (int)$_POST['category_id'],
            'brand_id' => (int)$_POST['brand_id'],
            'errors' => []
        ];

        // 1. Lưu hoặc cập nhật thông tin sản phẩm chính
        if ($id) { // Cập nhật
            $this->productModel->update($id, $data);
            $productId = $id;
        } else { // Tạo mới
            $productId = $this->productModel->create($data);
        }

        if ($productId) {
            // 2. Xử lý xóa ảnh trong thư viện
            if (!empty($_POST['images_to_delete'])) {
                foreach ($_POST['images_to_delete'] as $imgId) {
                    $this->productModel->deleteGalleryImage((int)$imgId);
                }
            }

            // 3. Xử lý thêm ảnh mới vào thư viện
            if (isset($_FILES['gallery_images'])) {
                $galleryFiles = $this->reArrayFiles($_FILES['gallery_images']);
                $uploadedGallery = [];
                foreach ($galleryFiles as $file) {
                    if ($file['error'] == 0) {
                        $uploadResult = $this->handleImageUpload($file);
                        if ($uploadResult['success']) {
                            $uploadedGallery[] = $uploadResult['filename'];
                        }
                    }
                }
                if (!empty($uploadedGallery)) {
                    $this->productModel->addGalleryImages($productId, $uploadedGallery);
                }
            }

            // 4. Cập nhật ảnh đại diện
            $mainImageUrl = $_POST['current_main_image'] ?? '';
            // Nếu người dùng chọn 1 ảnh trong thư viện làm ảnh đại diện
            if (!empty($_POST['new_main_image_url'])) {
                $mainImageUrl = $_POST['new_main_image_url'];
            }
            // Nếu không có ảnh nào được chọn, tự động lấy ảnh đầu tiên trong thư viện
            if (empty($mainImageUrl)) {
                $gallery = $this->productModel->getGalleryImages($productId);
                if (!empty($gallery)) {
                    $mainImageUrl = $gallery[0]->image_url;
                }
            }
            $this->productModel->setMainImage($productId, $mainImageUrl);

            header('Location: ' . BASE_URL . '/admin/products');
            exit();
        } else {
            $data['errors']['db'] = 'Lỗi lưu sản phẩm.';
            $data['gallery'] = $id ? $this->productModel->getGalleryImages($id) : [];
            $this->loadFormView($data, !is_null($id));
        }
    }

    private function loadFormView($data = [], $isEdit = false)
    {
        $viewData = $data;
        $viewData['title'] = $isEdit ? 'Chỉnh sửa Sản phẩm' : 'Thêm Sản phẩm mới';
        $viewData['categories'] = $this->categoryModel->getAll();
        $viewData['brands'] = $this->brandModel->findAll();
        $viewData['isEdit'] = $isEdit;

        $this->view('admin/products/form', $viewData);
    }

    private function reArrayFiles(&$file_post)
    {
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
        return $file_ary;
    }

    private function handleImageUpload($file)
    {
        $targetDir = UPLOADS_PATH . "/products/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $filename = uniqid() . '-' . basename($file["name"]);
        $targetFile = $targetDir . $filename;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (getimagesize($file["tmp_name"]) === false) return ['success' => false, 'message' => 'File không phải ảnh.'];
        if ($file["size"] > 5000000) return ['success' => false, 'message' => 'File quá lớn.'];
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif', 'webp'])) return ['success' => false, 'message' => 'Sai định dạng ảnh.'];

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return ['success' => true, 'filename' => 'products/' . $filename];
        } else {
            return ['success' => false, 'message' => 'Lỗi tải file.'];
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $this->productModel->delete((int)$_POST['id']);
        }
        header('Location: ' . BASE_URL . '/admin/products');
        exit();
    }
}
