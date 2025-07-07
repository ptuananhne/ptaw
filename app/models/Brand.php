<?php
// File: app/models/Brand.php

class Brand
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy tất cả các thương hiệu.
     * Sắp xếp theo tên để dễ dàng hiển thị trong dropdown.
     * @return array Mảng các đối tượng thương hiệu.
     */
    public function getAll()
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
     * Lấy tất cả các thương hiệu thuộc về một danh mục cụ thể.
     * Được sử dụng cho bộ lọc động (dynamic filter).
     * @param int $categoryId ID của danh mục.
     * @return array Mảng các đối tượng thương hiệu.
     */
    public function getBrandsByCategoryId($categoryId)
    {
        // Nếu không có categoryId hoặc là 0, trả về tất cả thương hiệu
        if (empty($categoryId)) {
            return $this->getAll();
        }

        try {
            // Dùng bảng trung gian `category_brand` để lọc
            $query = "SELECT b.id, b.name 
                      FROM brands b
                      JOIN category_brand cb ON b.id = cb.brand_id
                      WHERE cb.category_id = :category_id
                      ORDER BY b.name ASC";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Đếm tổng số thương hiệu.
     * Dùng cho trang dashboard.
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

    /**
     * Tạo một thương hiệu mới.
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO brands (name, slug) VALUES (:name, :slug)");
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':slug', $data['slug']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Xóa một thương hiệu bằng ID.
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM brands WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
