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

    /**
     * Lấy tất cả thương hiệu thuộc về một danh mục.
     * @param string $slug
     * @return array
     */
    public function getBrandsByCategorySlug($slug)
    {
        try {
            $query = "
                SELECT b.id, b.name 
                FROM brands b
                JOIN category_brand cb ON b.id = cb.brand_id
                JOIN categories c ON cb.category_id = c.id
                WHERE c.slug = :slug
                ORDER BY b.name ASC
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    public function countAll()
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM categories");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy tất cả danh mục.
     * @return array Danh sách danh mục.
     */
    public function getAll()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Tìm danh mục bằng slug.
     * @param string $slug Slug của danh mục.
     * @return object|false
     */
    public function findBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = :slug");
            $stmt->bindParam(':slug', $slug);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
