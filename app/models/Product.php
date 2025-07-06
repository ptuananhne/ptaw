<?php
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // --- HÀM TRỢ GIÚP ---
    private function buildWhereClause($options, &$whereClauses, &$params)
    {
        if (!empty($options['category_slug'])) {
            $whereClauses[] = "c.slug = :category_slug";
            $params[':category_slug'] = $options['category_slug'];
        }
        if (!empty($options['brand_id']) && $options['brand_id'] !== '') {
            $whereClauses[] = "p.brand_id = :brand_id";
            $params[':brand_id'] = $options['brand_id'];
        }
    }

    // --- CÁC HÀM CHO TRANG CHỦ ---
    public function getTopViewedProducts($limit = 8)
    {
        try {
            $query = "
                SELECT p.name, p.slug, p.image_url, c.name as category_name
                FROM products p JOIN categories c ON p.category_id = c.id
                ORDER BY p.view_count DESC, p.created_at DESC
                LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getTopViewedProductsByCategory($categoryId, $limit = 8)
    {
        try {
            $query = "
                SELECT p.name, p.slug, p.image_url, c.name as category_name
                FROM products p JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = :category_id
                ORDER BY p.view_count DESC, p.created_at DESC
                LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // --- CÁC HÀM CHO TRANG DANH MỤC ---
    public function getFilteredProducts($options = [])
    {
        $query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id";
        $whereClauses = [];
        $params = [];
        $this->buildWhereClause($options, $whereClauses, $params);

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $orderBy = "p.view_count DESC";
        if (!empty($options['sort_by'])) {
            switch ($options['sort_by']) {
                case 'price_asc':
                    $orderBy = "p.price ASC";
                    break;
                case 'price_desc':
                    $orderBy = "p.price DESC";
                    break;
                case 'newest':
                    $orderBy = "p.created_at DESC";
                    break;
            }
        }
        $query .= " ORDER BY " . $orderBy;

        if (isset($options['limit'])) {
            $query .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $options['limit'];
            $params[':offset'] = $options['offset'] ?? 0;
        }

        try {
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => &$val) {
                $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindParam($key, $val, $type);
            }
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function countFilteredProducts($options = [])
    {
        $query = "SELECT COUNT(p.id) as total FROM products p JOIN categories c ON p.category_id = c.id";
        $whereClauses = [];
        $params = [];
        $this->buildWhereClause($options, $whereClauses, $params);

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        try {
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => &$val) {
                $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindParam($key, $val, $type);
            }
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? (int)$result->total : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    // --- CÁC HÀM CHO TRANG CHI TIẾT SẢN PHẨM ---
    public function getProductBySlug($slug)
    {
        try {
            $query = "
                SELECT p.*, c.slug as category_slug, c.name as category_name, b.name as brand_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                JOIN brands b ON p.brand_id = b.id
                WHERE p.slug = :slug";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getProductGallery($productId)
    {
        try {
            $stmt = $this->db->prepare("SELECT image_url, alt_text FROM product_gallery WHERE product_id = :product_id ORDER BY sort_order ASC");
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function incrementViewCount($productId)
    {
        try {
            $stmt = $this->db->prepare("UPDATE products SET view_count = view_count + 1 WHERE id = :product_id");
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) { /* Do nothing */
        }
    }
    /**
     * Tìm kiếm sản phẩm theo từ khóa, có phân trang.
     * @param array $options
     * @return array
     */
    public function searchProducts($options = [])
    {
        $query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id";
        $whereClauses = [];
        $params = [];

        if (!empty($options['keyword'])) {
            $whereClauses[] = "(p.name LIKE :keyword OR p.description LIKE :keyword)";
            $params[':keyword'] = '%' . $options['keyword'] . '%';
        }

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $query .= " ORDER BY p.view_count DESC";

        if (isset($options['limit'])) {
            $query .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $options['limit'];
            $params[':offset'] = $options['offset'] ?? 0;
        }

        try {
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Đếm tổng số sản phẩm tìm thấy.
     * @param array $options
     * @return int
     */
    public function countSearchedProducts($options = [])
    {
        $query = "SELECT COUNT(p.id) as total FROM products p";
        $whereClauses = [];
        $params = [];

        if (!empty($options['keyword'])) {
            $whereClauses[] = "(p.name LIKE :keyword OR p.description LIKE :keyword)";
            $params[':keyword'] = '%' . $options['keyword'] . '%';
        }

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        try {
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? (int)$result->total : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
}
