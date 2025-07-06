<?php
class Banner
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy TẤT CẢ các banner đang hoạt động để hiển thị trên slider.
     * @return array
     */
    public function getAllActiveBanners()
    {
        try {
            // Sửa câu lệnh SQL để lấy tất cả banner đang hoạt động
            $stmt = $this->db->query("SELECT image_url, link_url, title FROM banners WHERE is_active = 1 ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Log a real error here in a production environment
            return [];
        }
    }

    /**
     * Lấy một banner đang hoạt động (đã có sẵn, giữ lại để có thể dùng sau).
     * @return object|false
     */
    public function getActiveBanner()
    {
        try {
            $stmt = $this->db->query("SELECT image_url, link_url, title FROM banners WHERE is_active = 1 LIMIT 1");
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
}
