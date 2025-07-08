<?php

class ProductController extends Controller
{
    private $adminProductModel;
    private $productsPerPage = 10;

    public function __construct()
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/auth/login');
            exit;
        }
        $this->adminProductModel = $this->model('AdminProduct');
    }

    public function index()
    {
        $totalProducts = $this->adminProductModel->countProducts();
        $totalPages = ceil($totalProducts / $this->productsPerPage);
        $products = $this->adminProductModel->getProducts([], 1, $this->productsPerPage);

        $data = [
            'title' => 'Quản lý Sản phẩm',
            'products' => $products,
            'categories' => $this->adminProductModel->getAllCategories(),
            'brands' => $this->adminProductModel->getAllBrands(),
            'totalPages' => $totalPages,
            'currentPage' => 1
        ];

        $this->view('admin/products/index', $data);
    }

    public function ajax()
    {
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $filters = [
            'name' => $_GET['name'] ?? '',
            'category' => $_GET['category'] ?? '',
            'brand' => $_GET['brand'] ?? ''
        ];
        $totalProducts = $this->adminProductModel->countProducts($filters);
        $totalPages = ceil($totalProducts / $this->productsPerPage);
        $products = $this->adminProductModel->getProducts($filters, $page, $this->productsPerPage);

        header('Content-Type: application/json');
        echo json_encode([
            'products' => $products,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }

    public function getBrandsForCategory($categoryId = 0)
    {
        $categoryId = filter_var($categoryId, FILTER_VALIDATE_INT);
        $brands = $categoryId ? $this->adminProductModel->getBrandsByCategoryId($categoryId) : $this->adminProductModel->getAllBrands();
        header('Content-Type: application/json');
        echo json_encode($brands);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $this->prepareProductData($_POST, $_FILES);
            $errors = $this->validateProductData($data);

            if (empty($errors)) {
                $uploadResult = $this->handleImageUpload($data['image_file']);
                if ($uploadResult['success']) {
                    $data['image_url'] = $uploadResult['path'];
                    $data['slug'] = create_slug($data['name']);

                    if ($this->adminProductModel->createProduct($data)) {
                        flash('product_message', 'Thêm sản phẩm thành công!');
                        header('Location: ' . BASE_URL . '/admin/product');
                        exit;
                    } else {
                        $data['error'] = 'Đã có lỗi xảy ra. Vui lòng thử lại.';
                    }
                } else {
                    $errors['image'] = $uploadResult['error'];
                }
            }
            $data['errors'] = $errors;
            $data['categories'] = $this->adminProductModel->getAllCategories();
            $data['brands'] = $this->adminProductModel->getAllBrands();
            $this->view('admin/products/add', $data);
        } else {
            $data = [
                'title' => 'Thêm Sản phẩm mới',
                'categories' => $this->adminProductModel->getAllCategories(),
                'brands' => $this->adminProductModel->getAllBrands(),
                'errors' => []
            ];
            $this->view('admin/products/add', $data);
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $this->prepareProductData($_POST, $_FILES);
            $errors = $this->validateProductData($data, true);

            if (empty($errors)) {
                if (!empty($data['gallery_files']['name'][0])) {
                    $uploadResults = $this->handleMultipleImageUpload($data['gallery_files']);
                    if ($uploadResults['success']) {
                        $this->adminProductModel->addGalleryImages($id, $uploadResults['paths']);
                    } else {
                        $errors['gallery'] = $uploadResults['error'];
                    }
                }

                if (empty($errors)) {
                    $data['slug'] = create_slug($data['name']);
                    if ($this->adminProductModel->updateProduct($id, $data)) {
                        flash('product_message', 'Cập nhật sản phẩm thành công!');
                        header('Location: ' . BASE_URL . '/admin/product/edit/' . $id);
                        exit;
                    } else {
                        flash('product_message', 'Có lỗi xảy ra khi cập nhật.', 'bg-red-100 text-red-700');
                    }
                }
            }
            $data['errors'] = $errors;
        }

        $product = $this->adminProductModel->getProductById($id);
        if (!$product) {
            header('Location: ' . BASE_URL . '/admin/product');
            exit;
        }

        $data['title'] = 'Sửa Sản phẩm';
        $data['product'] = $product;
        $data['gallery'] = $this->adminProductModel->getGalleryImages($id);
        $data['categories'] = $this->adminProductModel->getAllCategories();
        $data['brands'] = $this->adminProductModel->getAllBrands();
        if (!isset($data['errors'])) $data['errors'] = [];

        $this->view('admin/products/edit', $data);
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->adminProductModel->deleteProduct($id)) {
                flash('product_message', 'Xóa sản phẩm thành công!');
            } else {
                flash('product_message', 'Xóa sản phẩm thất bại.', 'bg-red-100 text-red-700');
            }
            header('Location: ' . BASE_URL . '/admin/product');
            exit;
        }
        header('Location: ' . BASE_URL . '/admin/product');
        exit;
    }

    // --- CÁC ENDPOINT AJAX MỚI ---
    public function deleteImage()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['image_id'])) {
            $imageId = filter_var($_POST['image_id'], FILTER_VALIDATE_INT);
            if ($this->adminProductModel->deleteGalleryImage($imageId)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            exit;
        }
    }

    public function setFeatured()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['image_id']) && isset($_POST['product_id'])) {
            $imageId = filter_var($_POST['image_id'], FILTER_VALIDATE_INT);
            $productId = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
            if ($this->adminProductModel->setFeaturedImage($productId, $imageId)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            exit;
        }
    }

    // --- CÁC HÀM TRỢ GIÚP ---
    private function prepareProductData($post, $files)
    {
        $specifications = [];
        if (isset($post['spec_key']) && is_array($post['spec_key'])) {
            foreach ($post['spec_key'] as $index => $key) {
                $value = $post['spec_value'][$index] ?? '';
                if (!empty(trim($key)) && !empty(trim($value))) {
                    $specifications[trim($key)] = trim($value);
                }
            }
        }

        return [
            'name' => trim($post['name'] ?? ''),
            'description' => trim($post['description'] ?? ''),
            'specifications' => json_encode($specifications, JSON_UNESCAPED_UNICODE),
            'price' => trim($post['price'] ?? ''),
            'brand_id' => $post['brand_id'] ?? '',
            'gallery_files' => $files['gallery'] ?? null,
            'image_file' => $files['image'] ?? null // For add function
        ];
    }

    private function validateProductData($data, $isEdit = false)
    {
        $errors = [];
        if (empty($data['name'])) $errors['name'] = 'Vui lòng nhập tên sản phẩm.';
        if (empty($data['price'])) $errors['price'] = 'Vui lòng nhập giá sản phẩm.';
        if (!is_numeric($data['price'])) $errors['price'] = 'Giá phải là một con số.';
        if (empty($data['brand_id'])) $errors['brand_id'] = 'Vui lòng chọn thương hiệu.';

        // Validation for 'add' method
        if (!$isEdit) {
            if (empty($data['category_id'])) $errors['category_id'] = 'Vui lòng chọn danh mục.';
            if (empty($data['image_file']['name'])) $errors['image'] = 'Vui lòng chọn ảnh sản phẩm.';
        }

        return $errors;
    }

    private function handleImageUpload($file)
    {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = uniqid() . '-' . basename($file['name']);
            $targetPath = $uploadDir . $fileName;
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                return ['success' => false, 'error' => 'Chỉ cho phép ảnh JPG, PNG, GIF, WEBP.'];
            }
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return ['success' => true, 'path' => $targetPath];
            }
        }
        return ['success' => false, 'error' => 'Có lỗi xảy ra khi tải ảnh lên.'];
    }

    private function handleMultipleImageUpload($files)
    {
        $uploadedPaths = [];
        $uploadDir = 'uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = uniqid() . '-' . basename($files['name'][$key]);
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $uploadedPaths[] = $targetPath;
                } else {
                    return ['success' => false, 'error' => 'Lỗi khi di chuyển file.', 'paths' => $uploadedPaths];
                }
            }
        }
        return ['success' => true, 'error' => '', 'paths' => $uploadedPaths];
    }
}
