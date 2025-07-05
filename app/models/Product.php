<?php
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy các sản phẩm nổi bật (ví dụ: 4 sản phẩm mới nhất).
     * @return array Mảng các đối tượng sản phẩm.
     */
    public function getFeaturedProducts($limit = 4)
    {
        try {
            $query = "
                SELECT 
                    p.name, 
                    p.slug, 
                    p.image_url, 
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
            error_log($e->getMessage());
            return [];
        }
    }
}
