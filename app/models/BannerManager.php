<?php

class BannerManager
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy tất cả banner.
     * @return array
     */
    public function getBanners()
    {
        $stmt = $this->db->query("SELECT * FROM banners ORDER BY sort_order ASC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function updateBannerOrder($bannerIds)
    {
        if (empty($bannerIds) || !is_array($bannerIds)) {
            return false;
        }

        $this->db->beginTransaction();
        try {
            foreach ($bannerIds as $index => $id) {
                $stmt = $this->db->prepare("UPDATE banners SET sort_order = :sort_order WHERE id = :id");
                $stmt->execute([':sort_order' => $index, ':id' => (int)$id]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    public function toggleBannerStatus($id)
    {
        $stmt = $this->db->prepare("UPDATE banners SET is_active = !is_active WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Lấy thông tin một banner bằng ID.
     * @param int $id
     * @return object|false
     */
    public function getBannerById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM banners WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Tạo một banner mới.
     * @param array $data
     * @return bool
     */
    public function createBanner($data)
    {
        $sql = "INSERT INTO banners (title, link_url, image_url, is_active) VALUES (:title, :link_url, :image_url, :is_active)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':link_url' => $data['link_url'],
            ':image_url' => $data['image_url'],
            ':is_active' => $data['is_active']
        ]);
    }

    /**
     * Cập nhật một banner.
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateBanner($id, $data)
    {
        $sql = "UPDATE banners SET title = :title, link_url = :link_url, is_active = :is_active";
        // Chỉ cập nhật ảnh nếu có ảnh mới được tải lên
        if (!empty($data['image_url'])) {
            $sql .= ", image_url = :image_url";
        }
        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $params = [
            ':id' => $id,
            ':title' => $data['title'],
            ':link_url' => $data['link_url'],
            ':is_active' => $data['is_active']
        ];

        if (!empty($data['image_url'])) {
            $params[':image_url'] = $data['image_url'];
        }

        return $stmt->execute($params);
    }

    /**
     * Xóa một banner.
     * @param int $id
     * @return bool
     */
    public function deleteBanner($id)
    {
        // Lấy thông tin để xóa file ảnh
        $banner = $this->getBannerById($id);
        if ($banner && !empty($banner->image_url) && file_exists($banner->image_url)) {
            unlink($banner->image_url);
        }

        $stmt = $this->db->prepare("DELETE FROM banners WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
