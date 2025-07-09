<?php

class ProductController extends Controller
{
    private $adminProductModel;
    private $productAttributeModel;
    private $productsPerPage = 10;

    public function __construct()
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/auth/login');
            exit;
        }
        $this->adminProductModel = $this->model('AdminProduct');
        $this->productAttributeModel = $this->model('ProductAttribute');
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
        $data = [
            'title' => 'Thêm Sản phẩm mới',
            'categories' => $this->adminProductModel->getAllCategories(),
            'brands' => $this->adminProductModel->getAllBrands(),
            'all_attributes' => $this->productAttributeModel->getAllWithTerms(),
            'errors' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = $this->prepareProductData($_POST);
            $errors = $this->validateProductData($postData);
            $data = array_merge($data, $postData, ['errors' => $errors]);

            if (empty($errors)) {
                $uploadResult = $this->handleMultipleImageUploads($_FILES['gallery'] ?? []);
                if ($uploadResult['success']) {
                    $imagePaths = $uploadResult['paths'];
                    // Lấy ảnh đầu tiên làm ảnh đại diện
                    $postData['image_url'] = $imagePaths[0] ?? null; 
                    $postData['slug'] = create_slug($postData['name']);

                    $productId = $this->adminProductModel->createProduct($postData);
                    if ($productId) {
                        // Thêm các ảnh còn lại vào gallery
                        if (!empty($imagePaths)) {
                            $this->adminProductModel->addImagesToGallery($productId, $imagePaths);
                        }
                        flash('product_message', 'Thêm sản phẩm thành công!');
                        header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/product');
                        exit;
                    } else {
                        $data['errors']['form'] = 'Đã có lỗi xảy ra khi lưu sản phẩm. Có thể tên sản phẩm đã tồn tại.';
                    }
                } else {
                    $data['errors']['gallery'] = $uploadResult['error'];
                }
            }
        }
        
        $this->view('admin/products/add', $data);
    }
    
    public function edit($id)
    {
        $product = $this->adminProductModel->getProductById($id);
        if (!$product) {
            header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/product');
            exit;
        }

        $data = [
            'title' => 'Sửa Sản phẩm',
            'categories' => $this->adminProductModel->getAllCategories(),
            'brands' => $this->adminProductModel->getBrandsByCategoryId($product->category_id),
            'all_attributes' => $this->productAttributeModel->getAllWithTerms(),
            'product' => $product,
            'gallery' => $this->adminProductModel->getGalleryByProductId($id), // Lấy thư viện ảnh
            'errors' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = $this->prepareProductData($_POST, $id);
            $errors = $this->validateProductData($postData, true);
            $data['product'] = (object)array_merge((array)$product, $postData);
            $data['errors'] = $errors;

            if (empty($errors)) {
                // Xử lý tải ảnh mới
                if (!empty($_FILES['gallery']['name'][0])) {
                    $uploadResult = $this->handleMultipleImageUploads($_FILES['gallery']);
                    if ($uploadResult['success']) {
                        $this->adminProductModel->addImagesToGallery($id, $uploadResult['paths']);
                    } else {
                        $data['errors']['gallery'] = $uploadResult['error'];
                    }
                }

                if (empty($data['errors'])) {
                    $postData['slug'] = create_slug($postData['name']);
                    // Cập nhật ảnh đại diện từ form
                    $postData['image_url'] = $_POST['featured_image_url'] ?? $product->image_url;

                    if ($this->adminProductModel->updateProduct($id, $postData)) {
                        flash('product_message', 'Cập nhật sản phẩm thành công!');
                        header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/product/edit/' . $id);
                        exit;
                    } else {
                        flash('product_message', 'Có lỗi xảy ra khi cập nhật.', 'bg-red-100 text-red-700');
                    }
                }
            }
        }

        $this->view('admin/products/edit', $data);
    }
    
    public function delete_gallery_image() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $imageId = $_POST['image_id'] ?? null;
            if ($imageId) {
                if ($this->adminProductModel->deleteGalleryImage($imageId)) {
                    echo json_encode(['success' => true, 'message' => 'Xóa ảnh thành công.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa ảnh.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Không có ID ảnh.']);
            }
            exit;
        }
    }


    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->adminProductModel->deleteProduct($id)) {
                flash('product_message', 'Xóa sản phẩm thành công!');
            } else {
                flash('product_message', 'Xóa sản phẩm thất bại.', 'bg-red-100 text-red-700');
            }
        }
       header('Location: ' . BASE_URL . '/' . ADMIN_ROUTE_PREFIX . '/product');
        exit;
    }

    private function prepareProductData($post, $productId = null)
    {
        // Xử lý thông số kỹ thuật
        $specifications_raw = [];
        $specifications_for_json = [];
        if (isset($post['spec_key']) && is_array($post['spec_key'])) {
            foreach ($post['spec_key'] as $index => $key) {
                $value = $post['spec_value'][$index] ?? '';
                $specifications_raw[] = ['key' => trim($key), 'value' => trim($value)];
                if (!empty(trim($key)) && !empty(trim($value))) {
                    $specifications_for_json[trim($key)] = trim($value);
                }
            }
        }

        // Xử lý thuộc tính và tự động thêm giá trị mới
        $attributes_for_json = [];
        if (isset($post['product_attributes']) && is_array($post['product_attributes'])) {
            foreach ($post['product_attributes'] as $attr_id => $attr_data) {
                if (!empty($attr_data['name']) && !empty($attr_data['values']) && is_array($attr_data['values'])) {
                    
                    foreach ($attr_data['values'] as $term_name) {
                        $this->productAttributeModel->addTerm($attr_id, $term_name);
                    }

                    $attributes_for_json[] = [
                        'id' => $attr_id,
                        'name' => $attr_data['name'],
                        'values' => $attr_data['values']
                    ];
                }
            }
        }

        $data = [
            'name' => trim($post['name'] ?? ''),
            'description' => trim($post['description'] ?? ''),
            'specifications_raw' => $specifications_raw,
            'specifications' => json_encode($specifications_for_json, JSON_UNESCAPED_UNICODE),
            'category_id' => $post['category_id'] ?? '',
            'brand_id' => $post['brand_id'] ?? '',
            'product_type' => $post['product_type'] ?? 'simple',
            'price' => trim($post['price'] ?? ''),
            'attributes_json' => json_encode($attributes_for_json, JSON_UNESCAPED_UNICODE),
            'variants' => $post['variants'] ?? []
        ];

        if ($data['product_type'] !== 'variable') {
            $data['attributes_json'] = null;
            $data['variants'] = [];
        }
        
        return $data;
    }

    private function validateProductData($data, $isEdit = false)
    {
        $errors = [];
        if (empty($data['name'])) $errors['name'] = 'Vui lòng nhập tên sản phẩm.';
        
        if (!$isEdit && empty($data['category_id'])) {
            $errors['category_id'] = 'Vui lòng chọn danh mục.';
        }
        if (empty($data['brand_id'])) $errors['brand_id'] = 'Vui lòng chọn thương hiệu.';
        
        if ($data['product_type'] === 'simple') {
            if (!isset($data['price']) || $data['price'] === '') {
                $errors['price'] = 'Vui lòng nhập giá cho sản phẩm đơn giản.';
            } elseif (!is_numeric($data['price'])) {
                $errors['price'] = 'Giá phải là một con số.';
            }
        } else {
            if (empty($data['variants'])) {
                $errors['variants'] = 'Vui lòng tạo ít nhất một biến thể.';
            } else {
                foreach($data['variants'] as $index => $variant) {
                    if (!isset($variant['price']) || $variant['price'] === '') {
                         $errors['variant_price_' . $index] = 'Vui lòng nhập giá cho tất cả biến thể.';
                    }
                }
            }
        }
        // Validate image upload only on add form, not edit
        if (!$isEdit && empty($_FILES['gallery']['name'][0])) {
             $errors['gallery'] = 'Vui lòng tải lên ít nhất một ảnh.';
        }

        return $errors;
    }

    private function handleMultipleImageUploads($files)
    {
        if (empty($files) || empty($files['name'][0])) {
            return ['success' => true, 'paths' => []];
        }

        $uploadedPaths = [];
        $errors = [];
        $uploadDir = 'uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileCount = count($files['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileExtension = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                $fileName = uniqid(bin2hex(random_bytes(4)).'_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;
                
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($files['type'][$i], $allowedTypes)) {
                    $errors[] = "Tệp '{$files['name'][$i]}' có định dạng không hợp lệ.";
                    continue;
                }
                if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                    $uploadedPaths[] = $targetPath;
                } else {
                    $errors[] = "Lỗi khi di chuyển tệp '{$files['name'][$i]}'.";
                }
            } elseif ($files['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                 $errors[] = "Lỗi tải lên tệp '{$files['name'][$i]}'. Mã lỗi: " . $files['error'][$i];
            }
        }

        if (!empty($errors)) {
            return ['success' => false, 'error' => implode('<br>', $errors)];
        }
        return ['success' => true, 'paths' => $uploadedPaths];
    }
}
