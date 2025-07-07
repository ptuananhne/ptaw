<?php
class AdminProductController extends Controller
{
    /**
     * Hiển thị trang danh sách sản phẩm, có chức năng lọc và sắp xếp.
     */
    public function index()
    {
        $filters = [
            'keyword'     => $_GET['keyword'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'brand_id'    => $_GET['brand_id'] ?? '',
            'sort_by'     => $_GET['sort_by'] ?? 'newest'
        ];

        $productModel  = $this->model('Product');
        $categoryModel = $this->model('Category');
        $brandModel    = $this->model('Brand');

        $products   = $productModel->getFilteredProductsAdmin($filters);
        $categories = $categoryModel->getAll();
        $brands     = $brandModel->getBrandsByCategoryId($filters['category_id']);

        $data = [
            'title'      => 'Quản lý Sản phẩm',
            'products'   => $products,
            'categories' => $categories,
            'brands'     => $brands,
            'filters'    => $filters
        ];

        $this->view('admin/manage_products', $data);
    }

    /**
     * Hiển thị form để thêm sản phẩm mới.
     */
    public function create()
    {
        $categoryModel = $this->model('Category');
        $brandModel    = $this->model('Brand');

        $data = [
            'title'      => 'Thêm Sản phẩm mới',
            'categories' => $categoryModel->getAll(),
            'brands'     => $brandModel->getAll(),
            'product'    => (object)[
                'name' => '',
                'description' => '',
                'price' => '',
                'category_id' => '',
                'brand_id' => '',
                'image_url' => ''
            ],
            'all_images'  => [],
            'form_action' => BASE_URL . '/admin.php?url=adminProduct/store',
        ];

        $this->view('admin/product_form', $data);
    }

    /**
     * Lưu sản phẩm mới vào CSDL.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $productData = [
                'name'        => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price'       => trim($_POST['price']),
                'category_id' => $_POST['category_id'],
                'brand_id'    => $_POST['brand_id'],
                'image_url'   => '' // Tạm thời để trống
            ];
            $productData['slug'] = create_slug($productData['name']);

            $productModel = $this->model('Product');
            $newProductId = $productModel->createProduct($productData);

            if ($newProductId) {
                // Xử lý upload thư viện ảnh
                $uploadedImagePaths = [];
                if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                    $uploadedImagePaths = $this->handleGalleryUpload($_FILES['gallery'], $newProductId);
                }

                // Tự động lấy ảnh đầu tiên làm ảnh đại diện nếu có
                if (!empty($uploadedImagePaths)) {
                    $featuredImageUrl = $uploadedImagePaths[0];
                    $updateData = ['image_url' => $featuredImageUrl];
                    $productModel->updateProduct($newProductId, $updateData);
                }

                redirect(BASE_URL . '/admin.php?url=adminProduct');
            } else {
                die('Có lỗi xảy ra khi thêm sản phẩm.');
            }
        }
    }

    /**
     * Hiển thị form để sửa thông tin sản phẩm.
     */
    public function edit($id)
    {
        $productModel  = $this->model('Product');
        $product       = $productModel->getProductById($id);

        if (!$product) {
            redirect(BASE_URL . '/admin.php?url=adminProduct');
        }

        $categoryModel = $this->model('Category');
        $brandModel    = $this->model('Brand');

        // Lấy ảnh từ gallery
        $galleryImages = $productModel->getGalleryByProductId($id);

        // Tạo một mảng để chứa các URL đã có để kiểm tra trùng lặp
        $existingUrls = array_map(function ($img) {
            return $img->image_url;
        }, $galleryImages);

        // Nếu ảnh đại diện tồn tại và chưa có trong gallery, thêm nó vào đầu danh sách
        if (!empty($product->image_url) && !in_array($product->image_url, $existingUrls)) {
            $featuredImage = new stdClass();
            $featuredImage->id = 'featured_' . $product->id; // ID giả để phân biệt
            $featuredImage->image_url = $product->image_url;
            // Thêm vào đầu mảng
            array_unshift($galleryImages, $featuredImage);
        }

        $data = [
            'title'       => 'Sửa Sản phẩm',
            'categories'  => $categoryModel->getAll(),
            'brands'      => $brandModel->getAll(),
            'product'     => $product,
            'all_images'  => $galleryImages, // Gửi danh sách đã gộp
            'form_action' => BASE_URL . '/admin.php?url=adminProduct/update/' . $id
        ];

        $this->view('admin/product_form', $data);
    }


    /**
     * Cập nhật thông tin sản phẩm trong CSDL.
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $productModel = $this->model('Product');

            // Xử lý upload ảnh gallery mới (nếu có)
            if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                $this->handleGalleryUpload($_FILES['gallery'], $id);
            }

            // Lấy URL ảnh đại diện được chọn từ form
            $featuredImageUrl = $_POST['featured_image_url'] ?? '';

            $productData = [
                'name'        => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price'       => trim($_POST['price']),
                'category_id' => $_POST['category_id'],
                'brand_id'    => $_POST['brand_id'],
                'image_url'   => $featuredImageUrl // Cập nhật ảnh đại diện
            ];
            $productData['slug'] = create_slug($productData['name']);

            if ($productModel->updateProduct($id, $productData)) {
                redirect(BASE_URL . '/admin.php?url=adminProduct');
            } else {
                die('Có lỗi xảy ra khi cập nhật sản phẩm.');
            }
        }
    }

    /**
     * Xóa sản phẩm và tất cả ảnh liên quan.
     */
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productModel = $this->model('Product');

            $product = $productModel->getProductById($id);
            $gallery = $productModel->getGalleryByProductId($id);

            if ($productModel->deleteProduct($id)) {
                // Xóa ảnh gallery (ảnh đại diện cũng sẽ bị xóa nếu nó nằm trong gallery)
                foreach ($gallery as $image) {
                    if (!empty($image->image_url) && file_exists($image->image_url)) {
                        unlink($image->image_url);
                    }
                }
                redirect(BASE_URL . '/admin.php?url=adminProduct');
            } else {
                die('Có lỗi xảy ra khi xóa sản phẩm.');
            }
        } else {
            $productModel = $this->model('Product');
            $data = [
                'title' => 'Xác nhận xóa sản phẩm',
                'product' => $productModel->getProductById($id),
                'form_action' => BASE_URL . '/admin.php?url=adminProduct/delete/' . $id
            ];
            $this->view('admin/delete_confirm', $data);
        }
    }

    /**
     * API để xóa một ảnh trong gallery.
     */
    public function deleteGalleryImage($imageId)
    {
        header('Content-Type: application/json');
        $productModel = $this->model('Product');

        $image = $productModel->getGalleryImageById($imageId);

        if ($image) {
            // Kiểm tra xem ảnh này có phải là ảnh đại diện không
            $product = $productModel->getProductByImageUrl($image->image_url);
            if ($product) {
                // Nếu là ảnh đại diện, xóa nó khỏi bảng products trước
                $productModel->updateProduct($product->id, ['image_url' => '']);
            }
        }

        if ($productModel->deleteGalleryImage($imageId)) {
            if ($image && !empty($image->image_url) && file_exists($image->image_url)) {
                unlink($image->image_url);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi xóa ảnh.']);
        }
        exit();
    }

    /**
     * Hàm nội bộ để xử lý việc upload một file.
     */
    private function handleFileUpload($file)
    {
        $targetDir = "uploads/products/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = uniqid('prod_') . '_' . basename($file["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (getimagesize($file["tmp_name"]) === false) return ['success' => false, 'error' => 'File không phải là ảnh.'];
        if ($file["size"] > 5000000) return ['success' => false, 'error' => 'Dung lượng ảnh quá lớn ( > 5MB).'];

        $allowedTypes = ["jpg", "png", "jpeg", "gif", "webp"];
        if (!in_array($imageFileType, $allowedTypes)) return ['success' => false, 'error' => 'Chỉ cho phép file JPG, JPEG, PNG, GIF, WEBP.'];

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return ['success' => true, 'filepath' => $targetFile];
        } else {
            return ['success' => false, 'error' => 'Đã có lỗi xảy ra khi upload file.'];
        }
    }

    /**
     * Hàm xử lý upload nhiều file cho gallery và trả về mảng các đường dẫn.
     * @return array Mảng các đường dẫn file đã upload thành công.
     */
    private function handleGalleryUpload($files, $productId)
    {
        $productModel = $this->model('Product');
        $uploadedImageUrls = [];

        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] === 0) {
                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];
                $uploadResult = $this->handleFileUpload($file);
                if ($uploadResult['success']) {
                    $uploadedImageUrls[] = $uploadResult['filepath'];
                }
            }
        }

        if (!empty($uploadedImageUrls)) {
            $productModel->addGalleryImages($productId, $uploadedImageUrls);
        }

        return $uploadedImageUrls;
    }
}
