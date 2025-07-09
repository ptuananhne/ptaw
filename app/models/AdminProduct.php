<?php

class AdminProduct
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

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
                break; 
            }
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
                        attributes = :attributes,
                        image_url = :image_url
                    WHERE id = :id";

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
                ':attributes' => ($data['product_type'] == 'variable') ? $data['attributes_json'] : null,
                ':image_url' => $data['image_url'] // Cập nhật ảnh đại diện
            ];

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

    public function deleteProduct($id)
    {
        $this->db->beginTransaction();
        try {
            // Lấy tất cả ảnh trong gallery để xóa file
            $galleryImages = $this->getGalleryByProductId($id);
            foreach ($galleryImages as $image) {
                if (!empty($image->image_url) && file_exists($image->image_url)) {
                    unlink($image->image_url);
                }
            }
            // Lấy ảnh đại diện để xóa file
            $product = $this->getProductById($id);
             if ($product && !empty($product->image_url) && file_exists($product->image_url)) {
                unlink($product->image_url);
            }

            // Xóa record trong DB (sẽ tự xóa gallery qua foreign key)
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Lỗi xóa sản phẩm: ' . $e->getMessage());
            return false;
        }
    }
    
    // --- Các hàm quản lý thư viện ảnh ---

    public function addImagesToGallery($productId, $imagePaths)
    {
        if (empty($imagePaths)) return true;
        
        $sql = "INSERT INTO product_gallery (product_id, image_url) VALUES (:product_id, :image_url)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($imagePaths as $path) {
            $stmt->execute([':product_id' => $productId, ':image_url' => $path]);
        }
        return true;
    }

    public function getGalleryByProductId($productId)
    {
        $stmt = $this->db->prepare("SELECT * FROM product_gallery WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC");
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function deleteGalleryImage($imageId)
    {
        $this->db->beginTransaction();
        try {
            // Lấy thông tin ảnh để xóa file
            $stmt_get = $this->db->prepare("SELECT image_url FROM product_gallery WHERE id = :id");
            $stmt_get->execute([':id' => $imageId]);
            $image = $stmt_get->fetch(PDO::FETCH_OBJ);

            if ($image && !empty($image->image_url) && file_exists($image->image_url)) {
                unlink($image->image_url);
            }

            // Xóa record trong DB
            $stmt_delete = $this->db->prepare("DELETE FROM product_gallery WHERE id = :id");
            $stmt_delete->execute([':id' => $imageId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Lỗi xóa ảnh gallery: ' . $e->getMessage());
            return false;
        }
    }

    // --- Các hàm có sẵn ---
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
}
