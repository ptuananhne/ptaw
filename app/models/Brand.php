<?php
// Model này tương tác với bảng `brands`
class Brand
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy tất cả các thương hiệu (dùng cho bộ lọc trang admin).
     * @return array Mảng các đối tượng thương hiệu.
     */
    public function findAll()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM brands ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tất cả các thương hiệu thuộc về một danh mục cụ thể (dùng cho bộ lọc trang admin).
     * @param int $categoryId ID của danh mục.
     * @return array Mảng các đối tượng thương hiệu.
     */
    public function findByCategoryId($categoryId)
    {
        $sql = "SELECT b.* FROM brands b
                INNER JOIN category_brand cb ON b.id = cb.brand_id
                WHERE cb.category_id = :category_id
                ORDER BY b.name ASC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Brand Model Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm tổng số thương hiệu.
     * @return int Số lượng thương hiệu.
     */
    public function countAll()
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM brands");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // Thêm các phương thức khác cho CRUD (create, read, update, delete) ở đây nếu cần
}
