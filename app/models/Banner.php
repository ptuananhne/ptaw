<?php
class Banner
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy banner đang hoạt động.
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
