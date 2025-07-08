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
     * Sắp xếp theo thứ tự đã được thiết lập trong trang admin.
     * @return array
     */
    public function getAllActiveBanners()
    {
        try {
            // SỬA LỖI: Thay đổi ORDER BY created_at DESC thành sort_order ASC
            $stmt = $this->db->query("SELECT image_url, link_url, title 
                                     FROM banners 
                                     WHERE is_active = 1 
                                     ORDER BY sort_order ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Ghi lại lỗi trong môi trường production
            return [];
        }
    }

    /**
     * Lấy một banner đang hoạt động (giữ lại để có thể dùng sau).
     * @return object|false
     */
    public function getActiveBanner()
    {
        try {
            $stmt = $this->db->query("SELECT image_url, link_url, title FROM banners WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 1");
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
}
