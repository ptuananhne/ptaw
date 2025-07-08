<?php
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ===================================================================
    // CÁC PHƯƠNG THỨC CHO TRANG KHÁCH HÀNG (CLIENT-SIDE)
    // ===================================================================

    public function getTopViewedProducts($limit = 8)
    {
        $query = "SELECT p.name, p.slug, p.image_url, p.price, c.name as category_name
                  FROM products p JOIN categories c ON p.category_id = c.id
                  ORDER BY p.view_count DESC, p.created_at DESC
                  LIMIT :limit";
        return $this->executeQuery($query, [':limit' => $limit]);
    }

    public function getTopProductsGroupedByAllCategories($limit = 8)
    {
        $query = "WITH RankedProducts AS (
                    SELECT p.*, c.name as category_name, c.slug as category_slug,
                           ROW_NUMBER() OVER(PARTITION BY p.category_id ORDER BY p.view_count DESC, p.created_at DESC) as rn
                    FROM products p JOIN categories c ON p.category_id = c.id
                  )
                  SELECT * FROM RankedProducts WHERE rn <= :limit";
        $allProducts = $this->executeQuery($query, [':limit' => $limit]);

        $groupedResult = [];
        foreach ($allProducts as $product) {
            if (!isset($groupedResult[$product->category_name])) {
                $groupedResult[$product->category_name] = ['slug' => $product->category_slug, 'products' => []];
            }
            $groupedResult[$product->category_name]['products'][] = $product;
        }
        return $groupedResult;
    }

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

        return $this->executeQuery($query, $params);
    }

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

        $result = $this->executeQuery($query, $params, true);
        return $result ? (int)$result->total : 0;
    }

    public function getProductBySlug($slug)
    {
        $query = "SELECT p.*, c.slug as category_slug, c.name as category_name, b.name as brand_name
                  FROM products p
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN brands b ON p.brand_id = b.id
                  WHERE p.slug = :slug";
        return $this->executeQuery($query, [':slug' => $slug], true);
    }

    public function incrementViewCount($productId)
    {
        $this->executeNonQuery("UPDATE products SET view_count = view_count + 1 WHERE id = :id", [':id' => $productId]);
    }

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

        return $this->executeQuery($query, $params);
    }

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

        $result = $this->executeQuery($query, $params, true);
        return $result ? (int)$result->total : 0;
    }

    // ===================================================================
    // CÁC PHƯƠNG THỨC CHO TRANG QUẢN TRỊ (ADMIN)
    // ===================================================================

    public function findAllWithDetails($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT p.*, c.name as category_name, b.name as brand_name 
                  FROM products p
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN brands b ON p.brand_id = b.id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $products = $this->executeQuery($query, [':limit' => $perPage, ':offset' => $offset]);
        $totalCount = $this->countAll();

        return [
            'products' => $products,
            'total_pages' => ceil($totalCount / $perPage),
            'current_page' => $page
        ];
    }

    public function searchAdminProducts($filters = [])
    {
        $query = "SELECT p.*, c.name as category_name, b.name as brand_name 
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
            $query .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $query .= " ORDER BY p.created_at DESC";

        return $this->executeQuery($query, $params);
    }

    public function findById($id)
    {
        return $this->executeQuery("SELECT * FROM products WHERE id = :id", [':id' => $id], true);
    }

    public function create($data)
    {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO products (name, slug, description, specifications, price, category_id, brand_id, image_url) 
                    VALUES (:name, :slug, :description, :specifications, :price, :category_id, :brand_id, :image_url)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':slug' => $data['slug'],
                ':description' => $data['description'],
                ':specifications' => $data['specifications'],
                ':price' => $data['price'],
                ':category_id' => $data['category_id'],
                ':brand_id' => $data['brand_id'],
                ':image_url' => $data['image_url'] ?? ''
            ]);

            $productId = $this->db->lastInsertId();

            if (!empty($data['gallery_images'])) {
                $this->addGalleryImages($productId, $data['gallery_images']);
            }

            $this->db->commit();
            return $productId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Product Create Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data)
    {
        $sql = "UPDATE products SET name = :name, slug = :slug, description = :description, 
                specifications = :specifications, price = :price, 
                category_id = :category_id, brand_id = :brand_id
                WHERE id = :id";

        return $this->executeNonQuery($sql, [
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'],
            ':specifications' => $data['specifications'],
            ':price' => $data['price'],
            ':category_id' => $data['category_id'],
            ':brand_id' => $data['brand_id'],
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $product = $this->findById($id);
        if ($product && !empty($product->image_url)) {
            $filePath = UPLOADS_PATH . '/' . $product->image_url;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $galleryImages = $this->getGalleryImages($id);
        foreach ($galleryImages as $image) {
            $this->deleteGalleryImage($image->id);
        }

        return $this->executeNonQuery("DELETE FROM products WHERE id = :id", [':id' => $id]);
    }

    // --- QUẢN LÝ THƯ VIỆN ẢNH ---

    public function getGalleryImages($productId)
    {
        return $this->executeQuery("SELECT * FROM product_gallery WHERE product_id = :id ORDER BY sort_order ASC", [':id' => $productId]);
    }

    public function addGalleryImages($productId, $images)
    {
        $sql = "INSERT INTO product_gallery (product_id, image_url) VALUES (:product_id, :image_url)";
        $stmt = $this->db->prepare($sql);
        foreach ($images as $imageUrl) {
            $stmt->execute([':product_id' => $productId, ':image_url' => $imageUrl]);
        }
    }

    public function deleteGalleryImage($imageId)
    {
        $image = $this->executeQuery("SELECT image_url FROM product_gallery WHERE id = :id", [':id' => $imageId], true);
        if ($image && !empty($image->image_url)) {
            $filePath = UPLOADS_PATH . '/' . $image->image_url;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        return $this->executeNonQuery("DELETE FROM product_gallery WHERE id = :id", [':id' => $imageId]);
    }

    public function setMainImage($productId, $imageUrl)
    {
        return $this->executeNonQuery("UPDATE products SET image_url = :image_url WHERE id = :id", [':image_url' => $imageUrl, ':id' => $productId]);
    }

    // ===================================================================
    // CÁC PHƯƠNG THỨC CHUNG & TIỆN ÍCH (ĐÃ SỬA LỖI)
    // ===================================================================

    public function countAll()
    {
        $result = $this->executeQuery("SELECT COUNT(*) as total FROM products", [], true);
        return $result ? (int)$result->total : 0;
    }

    private function executeQuery($sql, $params = [], $fetchOne = false)
    {
        try {
            $stmt = $this->db->prepare($sql);
            // Gán kiểu dữ liệu cho các tham số LIMIT và OFFSET
            foreach ($params as $key => $value) {
                if ($key == ':limit' || $key == ':offset') {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value);
                }
            }
            $stmt->execute();
            return $fetchOne ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return $fetchOne ? false : [];
        }
    }

    private function executeNonQuery($sql, $params = [])
    {
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
