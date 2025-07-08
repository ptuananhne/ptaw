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

    // Thêm các phương thức khác cho CRUD (create, read, update, delete) ở đây
}
