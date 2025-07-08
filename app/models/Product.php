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
                SELECT p.name, p.slug, p.image_url, p.price, c.name as category_name
                FROM products p JOIN categories c ON p.category_id = c.id
                ORDER BY p.view_count DESC, p.created_at DESC
                LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getTopProductsGroupedByAllCategories($limit = 8)
    {
        try {
            $query = "
                WITH RankedProducts AS (
                    SELECT
                        p.id, p.name, p.slug, p.image_url, p.price,
                        c.id as category_id, c.name as category_name, c.slug as category_slug,
                        ROW_NUMBER() OVER(PARTITION BY p.category_id ORDER BY p.view_count DESC, p.created_at DESC) as rn
                    FROM products p
                    JOIN categories c ON p.category_id = c.id
                )
                SELECT * FROM RankedProducts WHERE rn <= :limit
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $allProducts = $stmt->fetchAll(PDO::FETCH_OBJ);

            $groupedResult = [];
            foreach ($allProducts as $product) {
                if (!isset($groupedResult[$product->category_name])) {
                    $groupedResult[$product->category_name] = [
                        'slug' => $product->category_slug,
                        'products' => []
                    ];
                }
                $groupedResult[$product->category_name]['products'][] = $product;
            }
            return $groupedResult;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function countAll()
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM products");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    public function getAll()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
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
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
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
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ? (int)$result->total : 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
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
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.slug = :slug";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getProductGallery($productId)
    {
        try {
            $stmt = $this->db->prepare("SELECT image_url, alt_text FROM product_gallery WHERE product_id = :product_id ORDER BY sort_order ASC");
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Tăng lượt xem cho sản phẩm.
     * @param int $productId ID của sản phẩm cần tăng lượt xem.
     */
    public function incrementViewCount($productId)
    {
        try {
            $stmt = $this->db->prepare("UPDATE products SET view_count = view_count + 1 WHERE id = :product_id");
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            // Ghi lại lỗi để debug nhưng không làm dừng chương trình
            error_log("Could not increment view count for product ID $productId: " . $e->getMessage());
        }
    }

    // --- CÁC HÀM CHO TRANG TÌM KIẾM ---
    public function searchProducts($options = [])
    {
        $query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id";
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
                $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindParam($key, $val, $type);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function countSearchedProducts($options = [])
    {
        $query = "SELECT COUNT(p.id) as total 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id";
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
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ? (int)$result->total : 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
}
