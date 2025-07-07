<?php
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ===================================================================
    // CÁC HÀM DÀNH CHO TRANG QUẢN TRỊ (ADMIN)
    // ===================================================================

    /**
     * Đếm tổng số sản phẩm. Dùng cho trang Dashboard.
     * @return int
     */
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

    /**
     * Lấy sản phẩm cho trang quản trị với chức năng lọc và sắp xếp.
     */
    public function getFilteredProductsAdmin($filters = [])
    {
        try {
            $query = "SELECT p.id, p.name, p.image_url, p.price, p.view_count, c.name as category_name, b.name as brand_name
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.id
                      LEFT JOIN brands b ON p.brand_id = b.id";

            $whereClauses = [];
            $params = [];

            if (!empty($filters['keyword'])) {
                $whereClauses[] = "p.name LIKE :keyword";
                $params[':keyword'] = '%' . $filters['keyword'] . '%';
            }
            if (!empty($filters['category_id'])) {
                $whereClauses[] = "p.category_id = :category_id";
                $params[':category_id'] = $filters['category_id'];
            }
            if (!empty($filters['brand_id'])) {
                $whereClauses[] = "p.brand_id = :brand_id";
                $params[':brand_id'] = $filters['brand_id'];
            }

            if (!empty($whereClauses)) {
                $query .= " WHERE " . implode(" AND ", $whereClauses);
            }

            $orderBy = 'p.created_at DESC'; // Mặc định
            if (!empty($filters['sort_by'])) {
                switch ($filters['sort_by']) {
                    case 'views_desc':
                        $orderBy = 'p.view_count DESC';
                        break;
                    case 'price_asc':
                        $orderBy = 'p.price ASC';
                        break;
                    case 'price_desc':
                        $orderBy = 'p.price DESC';
                        break;
                    case 'newest':
                    default:
                        $orderBy = 'p.created_at DESC';
                        break;
                }
            }
            $query .= " ORDER BY " . $orderBy;

            $stmt = $this->db->prepare($query);
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin một sản phẩm bằng ID.
     */
    public function getProductById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Tạo sản phẩm mới và trả về ID của sản phẩm đó.
     */
    public function createProduct($data)
    {
        try {
            $query = "INSERT INTO products (name, slug, description, price, category_id, brand_id, image_url) 
                      VALUES (:name, :slug, :description, :price, :category_id, :brand_id, :image_url)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':slug', $data['slug']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
            $stmt->bindParam(':brand_id', $data['brand_id'], PDO::PARAM_INT);
            $stmt->bindParam(':image_url', $data['image_url']);

            if ($stmt->execute()) {
                return $this->db->lastInsertId(); // Trả về ID sản phẩm vừa tạo
            }
            return false;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật thông tin sản phẩm.
     */
    public function updateProduct($id, $data)
    {
        try {
            // Chỉ cập nhật các trường được cung cấp, tránh ghi đè toàn bộ
            $fields = [];
            $params = [':id' => $id];
            foreach ($data as $key => $value) {
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
            $fieldString = implode(', ', $fields);

            $query = "UPDATE products SET $fieldString WHERE id = :id";
            $stmt = $this->db->prepare($query);

            foreach ($params as $key => &$val) {
                $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindParam($key, $val, $type);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Xóa sản phẩm bằng ID.
     */
    public function deleteProduct($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ===================================================================
    // CÁC HÀM DÀNH CHO THƯ VIỆN ẢNH (GALLERY)
    // ===================================================================

    public function getGalleryByProductId($productId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_gallery WHERE product_id = :product_id ORDER BY sort_order ASC");
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getGalleryImageById($imageId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_gallery WHERE id = :id");
            $stmt->bindParam(':id', $imageId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function addGalleryImages($productId, $imageUrls)
    {
        try {
            $query = "INSERT INTO product_gallery (product_id, image_url) VALUES (:product_id, :image_url)";
            $stmt = $this->db->prepare($query);

            foreach ($imageUrls as $url) {
                $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $stmt->bindParam(':image_url', $url);
                $stmt->execute();
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function deleteGalleryImage($imageId)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM product_gallery WHERE id = :id");
            $stmt->bindParam(':id', $imageId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ===================================================================
    // CÁC HÀM DÀNH CHO TRANG KHÁCH (CLIENT)
    // ===================================================================

    public function getProductGallery($productId)
    {
        return $this->getGalleryByProductId($productId);
    }

    /**
     * Lấy sản phẩm xem nhiều cho trang chủ.
     */
    public function getTopViewedProducts($limit = 8)
    {
        try {
            $query = "SELECT p.name, p.slug, p.image_url, p.price, c.name as category_name
                      FROM products p JOIN categories c ON p.category_id = c.id
                      ORDER BY p.view_count DESC, p.created_at DESC
                      LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Lấy sản phẩm theo từng danh mục cho trang chủ.
     */
    public function getTopProductsGroupedByAllCategories($limit = 8)
    {
        try {
            $query = "WITH RankedProducts AS (
                        SELECT p.id, p.name, p.slug, p.image_url, p.price,
                               c.id as category_id, c.name as category_name, c.slug as category_slug,
                               ROW_NUMBER() OVER(PARTITION BY p.category_id ORDER BY p.view_count DESC, p.created_at DESC) as rn
                        FROM products p JOIN categories c ON p.category_id = c.id
                      )
                      SELECT * FROM RankedProducts WHERE rn <= :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $allProducts = $stmt->fetchAll(PDO::FETCH_OBJ);

            $groupedResult = [];
            foreach ($allProducts as $product) {
                if (!isset($groupedResult[$product->category_name])) {
                    $groupedResult[$product->category_name] = ['slug' => $product->category_slug, 'products' => []];
                }
                $groupedResult[$product->category_name]['products'][] = $product;
            }
            return $groupedResult;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Lấy sản phẩm có lọc cho trang danh mục.
     */
    public function getFilteredProducts($options = [])
    {
        $query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id";
        $whereClauses = [];
        $params = [];

        if (!empty($options['category_slug'])) {
            $whereClauses[] = "c.slug = :category_slug";
            $params[':category_slug'] = $options['category_slug'];
        }
        if (!empty($options['brand_id'])) {
            $whereClauses[] = "p.brand_id = :brand_id";
            $params[':brand_id'] = $options['brand_id'];
        }

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
            return [];
        }
    }

    /**
     * Đếm sản phẩm có lọc cho trang danh mục (phân trang).
     */
    public function countFilteredProducts($options = [])
    {
        $query = "SELECT COUNT(p.id) as total FROM products p JOIN categories c ON p.category_id = c.id";
        $whereClauses = [];
        $params = [];
        if (!empty($options['category_slug'])) {
            $whereClauses[] = "c.slug = :category_slug";
            $params[':category_slug'] = $options['category_slug'];
        }
        if (!empty($options['brand_id'])) {
            $whereClauses[] = "p.brand_id = :brand_id";
            $params[':brand_id'] = $options['brand_id'];
        }

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
            return 0;
        }
    }

    /**
     * Lấy thông tin chi tiết sản phẩm bằng slug.
     */
    public function getProductBySlug($slug)
    {
        try {
            $query = "SELECT p.*, c.slug as category_slug, c.name as category_name, b.name as brand_name
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.id
                      LEFT JOIN brands b ON p.brand_id = b.id
                      WHERE p.slug = :slug";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Tăng lượt xem cho sản phẩm.
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
     * Tìm kiếm sản phẩm.
     */
    public function searchProducts($options = [])
    {
        $query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
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
            return [];
        }
    }

    /**
     * Đếm sản phẩm tìm kiếm được (phân trang).
     */
    public function countSearchedProducts($options = [])
    {
        $query = "SELECT COUNT(p.id) as total FROM products p LEFT JOIN categories c ON p.category_id = c.id";
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
            return 0;
        }
    }
}
