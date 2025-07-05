<?php
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getFeaturedProducts($limit = 4)
    {
        // ... (hàm này không thay đổi)
        try {
            $query = "
                SELECT 
                    p.name, p.slug, p.image_url, 
                    c.name as category_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                ORDER BY p.created_at DESC
                LIMIT :limit
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Lấy tất cả sản phẩm thuộc một danh mục dựa vào slug của danh mục đó.
     * @param string $categorySlug
     * @return array
     */
    public function getProductsByCategorySlug($categorySlug)
    {
        try {
            $query = "
                SELECT 
                    p.name, p.slug, p.image_url,
                    c.name as category_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE c.slug = :category_slug
                ORDER BY p.created_at DESC
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':category_slug', $categorySlug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
