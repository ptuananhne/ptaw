<?php

class BannerController extends Controller
{
    private $bannerManagerModel;

    public function __construct()
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/admin/auth/login');
            exit;
        }
        $this->bannerManagerModel = $this->model('BannerManager');
    }

    public function index()
    {
        $data = [
            'title' => 'Quản lý Banner',
            'banners' => $this->bannerManagerModel->getBanners()
        ];
        $this->view('admin/banners/index', $data);
    }

    // --- CÁC ENDPOINT AJAX MỚI ---

    /**
     * Xử lý yêu cầu cập nhật thứ tự banner.
     */
    public function updateOrder()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order'])) {
            $bannerIds = $_POST['order'];
            if ($this->bannerManagerModel->updateBannerOrder($bannerIds)) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật thứ tự thành công.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cập nhật thứ tự thất bại.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
        }
    }

    /**
     * Xử lý yêu cầu bật/tắt trạng thái banner.
     */
    public function toggleStatus($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->bannerManagerModel->toggleBannerStatus($id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $imagePath = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['image']);
                if ($uploadResult['success']) {
                    $imagePath = $uploadResult['path'];
                } else {
                    flash('banner_message', $uploadResult['error'], 'bg-red-100 text-red-700');
                    header('Location: ' . BASE_URL . '/admin/banner');
                    exit;
                }
            } else {
                flash('banner_message', 'Vui lòng chọn ảnh cho banner.', 'bg-red-100 text-red-700');
                header('Location: ' . BASE_URL . '/admin/banner');
                exit;
            }

            $data = [
                'title' => trim($_POST['title']),
                'link_url' => trim($_POST['link_url']),
                'image_url' => $imagePath,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->bannerManagerModel->createBanner($data)) {
                flash('banner_message', 'Thêm banner thành công!');
            } else {
                flash('banner_message', 'Thêm banner thất bại.', 'bg-red-100 text-red-700');
            }
        }
        header('Location: ' . BASE_URL . '/admin/banner');
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $banner = $this->bannerManagerModel->getBannerById($id);
            $imagePath = $banner->image_url; // Giữ ảnh cũ làm mặc định

            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['image']);
                if ($uploadResult['success']) {
                    // Xóa ảnh cũ nếu tải lên ảnh mới thành công
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $imagePath = $uploadResult['path'];
                } else {
                    flash('banner_message', $uploadResult['error'], 'bg-red-100 text-red-700');
                    header('Location: ' . BASE_URL . '/admin/banner');
                    exit;
                }
            }

            $data = [
                'title' => trim($_POST['title']),
                'link_url' => trim($_POST['link_url']),
                'image_url' => !empty($_FILES['image']['name']) ? $imagePath : '', // Chỉ cập nhật nếu có ảnh mới
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->bannerManagerModel->updateBanner($id, $data)) {
                flash('banner_message', 'Cập nhật banner thành công!');
            } else {
                flash('banner_message', 'Cập nhật banner thất bại.', 'bg-red-100 text-red-700');
            }
            header('Location: ' . BASE_URL . '/admin/banner');
        } else {
            // Hiển thị dữ liệu cho modal edit (sẽ được xử lý bằng JS)
            header('Location: ' . BASE_URL . '/admin/banner');
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->bannerManagerModel->deleteBanner($id)) {
                flash('banner_message', 'Xóa banner thành công!');
            } else {
                flash('banner_message', 'Xóa banner thất bại.', 'bg-red-100 text-red-700');
            }
        }
        header('Location: ' . BASE_URL . '/admin/banner');
    }

    private function handleImageUpload($file)
    {
        $uploadDir = 'uploads/banners/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = uniqid() . '-' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'path' => $targetPath];
        }
        return ['success' => false, 'error' => 'Lỗi khi tải ảnh lên.'];
    }
}
