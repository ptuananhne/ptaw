<?php
class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy tất cả danh mục, sắp xếp theo thứ tự đã thiết lập.
     * @return array
     */
    public function getAllCategories()
    {
        try {
            // SỬA LỖI: Thay đổi ORDER BY name ASC thành sort_order ASC
            // Cũng lấy tất cả các cột để đảm bảo tính nhất quán
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY sort_order ASC");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
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
            return $stmt->fetch(PDO::FETCH_OBJ);
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
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return [];
        }
    }
}
