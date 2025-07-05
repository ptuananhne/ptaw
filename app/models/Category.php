<?php
class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllCategories()
    {
        // ... (hàm này không thay đổi)
        try {
            $stmt = $this->db->query("SELECT name, slug FROM categories ORDER BY name ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Lấy thông tin một danh mục bằng slug.
     * @param string $slug
     * @return object|false
     */
    public function getCategoryBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare("SELECT id, name, slug FROM categories WHERE slug = :slug");
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
}
