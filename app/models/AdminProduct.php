<?php

class AdminProduct
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Tạo một slug duy nhất bằng cách thêm số vào cuối nếu cần.
     */
    private function generateUniqueSlug($slug, $excludeId = null)
    {
        $originalSlug = $slug;
        $counter = 1;

        $sql = "SELECT id FROM products WHERE slug = :slug";
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }

        $stmt = $this->db->prepare($sql);

        while (true) {
            $params = [':slug' => $slug];
            if ($excludeId) {
                $params[':exclude_id'] = $excludeId;
            }
            $stmt->execute($params);
            if ($stmt->fetch() === false) {
                break; // Slug is unique
            }
            // If not unique, append a number and try again
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    public function createProduct($data)
    {
        $this->db->beginTransaction();
        try {
            $uniqueSlug = $this->generateUniqueSlug($data['slug']);

            $sql = "INSERT INTO products (name, slug, description, specifications, image_url, category_id, brand_id, product_type, price, attributes) 
                    VALUES (:name, :slug, :description, :specifications, :image_url, :category_id, :brand_id, :product_type, :price, :attributes)";
            $stmt = $this->db->prepare($sql);

            $result = $stmt->execute([
                ':name' => $data['name'],
                ':slug' => $uniqueSlug,
                ':description' => $data['description'],
                ':specifications' => $data['specifications'],
                ':image_url' => $data['image_url'],
                ':category_id' => $data['category_id'],
                ':brand_id' => $data['brand_id'],
                ':product_type' => $data['product_type'],
                ':price' => ($data['product_type'] == 'simple') ? $data['price'] : null,
                ':attributes' => ($data['product_type'] == 'variable') ? $data['attributes_json'] : null
            ]);

            if (!$result) {
                $this->db->rollBack();
                return false;
            }

            $productId = $this->db->lastInsertId();

            if ($data['product_type'] == 'variable' && !empty($data['variants'])) {
                $this->syncVariants($productId, $data['variants']);
            }

            $this->db->commit();
            return $productId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Lỗi tạo sản phẩm: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProduct($id, $data)
    {
        $this->db->beginTransaction();
        try {
            $uniqueSlug = $this->generateUniqueSlug($data['slug'], $id);

            $sql = "UPDATE products SET 
                        name = :name,
                        slug = :slug,
                        description = :description,
                        specifications = :specifications,
                        brand_id = :brand_id,
                        product_type = :product_type,
                        price = :price,
                        attributes = :attributes";

            if (isset($data['image_url'])) {
                $sql .= ", image_url = :image_url";
            }
            $sql .= " WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $params = [
                ':id' => $id,
                ':name' => $data['name'],
                ':slug' => $uniqueSlug,
                ':description' => $data['description'],
                ':specifications' => $data['specifications'],
                ':brand_id' => $data['brand_id'],
                ':product_type' => $data['product_type'],
                ':price' => ($data['product_type'] == 'simple') ? $data['price'] : null,
                ':attributes' => ($data['product_type'] == 'variable') ? $data['attributes_json'] : null
            ];

            if (isset($data['image_url'])) {
                $params[':image_url'] = $data['image_url'];
            }

            $stmt->execute($params);

            if ($data['product_type'] == 'variable') {
                $this->syncVariants($id, $data['variants']);
            } else {
                $stmt_delete = $this->db->prepare("DELETE FROM product_variants WHERE product_id = :product_id");
                $stmt_delete->execute([':product_id' => $id]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Lỗi cập nhật sản phẩm: ' . $e->getMessage());
            return false;
        }
    }

    public function syncVariants($productId, $variantsData)
    {
        $stmt = $this->db->prepare("DELETE FROM product_variants WHERE product_id = :product_id");
        $stmt->execute([':product_id' => $productId]);

        if (empty($variantsData)) return;

        $sql = "INSERT INTO product_variants (product_id, price, attributes) VALUES (:product_id, :price, :attributes)";
        $stmt = $this->db->prepare($sql);

        foreach ($variantsData as $variant) {
            $stmt->execute([
                ':product_id' => $productId,
                ':price' => $variant['price'],
                ':attributes' => $variant['attributes']
            ]);
        }
    }

    // Các hàm khác không thay đổi
    public function getProducts($filters = [], $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name, (CASE WHEN p.product_type = 'variable' THEN (SELECT MIN(pv.price) FROM product_variants pv WHERE pv.product_id = p.id) ELSE p.price END) as min_price, (CASE WHEN p.product_type = 'variable' THEN (SELECT MAX(pv.price) FROM product_variants pv WHERE pv.product_id = p.id) ELSE p.price END) as max_price FROM products p JOIN categories c ON p.category_id = c.id JOIN brands b ON p.brand_id = b.id";
        list($whereClause, $params) = $this->buildWhereClause($filters);
        $sql .= $whereClause;
        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function countProducts($filters = [])
    {
        $sql = "SELECT COUNT(p.id) as total FROM products p";
        list($whereClause, $params) = $this->buildWhereClause($filters);
        $sql .= $whereClause;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? (int)$result->total : 0;
    }
    private function buildWhereClause($filters)
    {
        $where = " WHERE 1=1";
        $params = [];
        if (!empty($filters['name'])) {
            $where .= " AND p.name LIKE :name";
            $params[':name'] = '%' . $filters['name'] . '%';
        }
        if (!empty($filters['category'])) {
            $where .= " AND p.category_id = :category_id";
            $params[':category_id'] = $filters['category'];
        }
        if (!empty($filters['brand'])) {
            $where .= " AND p.brand_id = :brand_id";
            $params[':brand_id'] = $filters['brand'];
        }
        return [$where, $params];
    }
    public function getProductById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_OBJ);
        if ($product && $product->product_type === 'variable') {
            $stmt_variants = $this->db->prepare("SELECT * FROM product_variants WHERE product_id = :product_id ORDER BY id ASC");
            $stmt_variants->execute([':product_id' => $id]);
            $product->variants = $stmt_variants->fetchAll(PDO::FETCH_OBJ);
        } else if ($product) {
            $product->variants = [];
        }
        return $product;
    }
    public function getAllCategories()
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getAllBrands()
    {
        $stmt = $this->db->query("SELECT * FROM brands ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getBrandsByCategoryId($categoryId)
    {
        $sql = "SELECT b.id, b.name FROM brands b JOIN category_brand cb ON b.id = cb.brand_id WHERE cb.category_id = :category_id ORDER BY b.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function deleteProduct($id)
    {
        $product = $this->getProductById($id);
        if ($product && !empty($product->image_url) && file_exists(PUBLIC_PATH . '/' . $product->image_url)) {
            unlink(PUBLIC_PATH . '/' . $product->image_url);
        }
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
