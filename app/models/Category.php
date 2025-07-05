<?php
class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy tất cả danh mục từ CSDL.
     * @return array Mảng các đối tượng danh mục.
     */
    public function getAllCategories()
    {
        try {
            $stmt = $this->db->query("SELECT name, slug FROM categories ORDER BY name ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Trong thực tế, bạn nên ghi lỗi này vào file log thay vì trả về mảng rỗng
            error_log($e->getMessage());
            return [];
        }
    }
}
