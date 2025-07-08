<?php

class Statistic
{
    private $db;

    public function __construct()
    {
        // Sử dụng lớp Database hiện có của bạn
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy tổng số lượng sản phẩm.
     * @return int
     */
    public function getProductCount()
    {
        $stmt = $this->db->query('SELECT COUNT(*) as count FROM products');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] ?? 0;
    }

    /**
     * Lấy tổng số lượng danh mục.
     * @return int
     */
    public function getCategoryCount()
    {
        $stmt = $this->db->query('SELECT COUNT(*) as count FROM categories');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] ?? 0;
    }

    /**
     * Lấy tổng số lượng thương hiệu.
     * @return int
     */
    public function getBrandCount()
    {
        $stmt = $this->db->query('SELECT COUNT(*) as count FROM brands');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] ?? 0;
    }

    /**
     * Lấy tổng số lượng banner.
     * @return int
     */
    public function getBannerCount()
    {
        $stmt = $this->db->query('SELECT COUNT(*) as count FROM banners');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] ?? 0;
    }
}
