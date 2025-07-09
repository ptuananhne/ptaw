<?php
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Helper function to generate the SQL for calculating display price.
     * It gets the minimum price from variants if it's a variable product.
     *
     * CẬP NHẬT:
     * - Sử dụng COALESCE để xử lý các trường hợp giá NULL.
     * - Ưu tiên lấy giá nhỏ nhất > 0 của biến thể.
     * - Nếu không có, sẽ lấy giá gốc của sản phẩm.
     * - Nếu vẫn không có, trả về 0 để hiển thị "Liên hệ".
     */
    private function getDisplayPriceSQL()
    {
        return "
            (CASE
                WHEN p.product_type = 'variable'
                THEN COALESCE((SELECT MIN(pv.price) FROM product_variants pv WHERE pv.product_id = p.id AND pv.price > 0), p.price, 0)
                ELSE COALESCE(p.price, 0)
            END) as display_price
        ";
    }

    /**
     * Helper function to build WHERE clauses for filtering.
     */
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

    /**
     * Gets top viewed products for the homepage.
     */
    public function getTopViewedProducts($limit = 8)
    {
        try {
            $displayPriceSQL = $this->getDisplayPriceSQL();
            $query = "
                SELECT p.name, p.slug, p.image_url, p.product_type, c.name as category_name, {$displayPriceSQL}
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

    /**
     * Gets top products grouped by all categories for the homepage.
     */
    public function getTopProductsGroupedByAllCategories($limit = 8)
    {
        try {
            $displayPriceSQL = $this->getDisplayPriceSQL();
            $query = "
                WITH RankedProducts AS (
                    SELECT
                        p.id, p.name, p.slug, p.image_url, p.product_type, {$displayPriceSQL},
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

    /**
     * Gets a list of products based on filters, sorting, and pagination for category pages.
     */
    public function getFilteredProducts($options = [])
    {
        $displayPriceSQL = $this->getDisplayPriceSQL();
        $query = "SELECT p.*, c.name as category_name, {$displayPriceSQL} FROM products p JOIN categories c ON p.category_id = c.id";
        
        $whereClauses = [];
        $params = [];
        $this->buildWhereClause($options, $whereClauses, $params);

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $orderBy = "p.view_count DESC"; // Default sort
        if (!empty($options['sort_by'])) {
            switch ($options['sort_by']) {
                case 'price_asc':
                    $orderBy = "display_price ASC";
                    break;
                case 'price_desc':
                    $orderBy = "display_price DESC";
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

    /**
     * Counts products based on filters for pagination.
     */
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

    /**
     * Gets a single product by its slug for the detail page.
     */
    public function getProductBySlug($slug)
    {
        try {
            $displayPriceSQL = $this->getDisplayPriceSQL();
            $query = "
                SELECT p.*, c.slug as category_slug, c.name as category_name, b.name as brand_name, {$displayPriceSQL}
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

    /**
     * Gets all variants for a specific product.
     */
    public function getProductVariants($productId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_variants WHERE product_id = :product_id ORDER BY price ASC");
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Gets the image gallery for a product.
     */
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
     * Increments the view count for a product.
     */
    public function incrementViewCount($productId)
    {
        try {
            $stmt = $this->db->prepare("UPDATE products SET view_count = view_count + 1 WHERE id = :product_id");
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Could not increment view count for product ID $productId: " . $e->getMessage());
        }
    }

    /**
     * Gets products based on a search keyword.
     */
    public function searchProducts($options = [])
    {
        $displayPriceSQL = $this->getDisplayPriceSQL();
        $query = "SELECT p.*, c.name as category_name, {$displayPriceSQL}
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

    /**
     * Counts products based on a search keyword.
     */
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
