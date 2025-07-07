<?php
class ApiController extends Controller
{
    /**
     * Lấy danh sách các thương hiệu thuộc về một danh mục
     * và trả về dưới dạng JSON.
     * @param int $categoryId ID của danh mục.
     */
    public function getBrandsByCategory($categoryId = 0)
    {
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getBrandsByCategoryId((int)$categoryId);

        // Thiết lập header để trình duyệt hiểu đây là dữ liệu JSON
        header('Content-Type: application/json');

        // In ra dữ liệu dưới dạng JSON và kết thúc script
        echo json_encode($brands);
        exit();
    }
}
