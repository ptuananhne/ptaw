<?php

class AdminProduct
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy danh sách sản phẩm với bộ lọc và phân trang.
     * @param array $filters Mảng chứa các điều kiện lọc.
     * @param int $page Trang hiện tại.
     * @param int $perPage Số sản phẩm mỗi trang.
     * @return array
     */
    public function getProducts($filters = [], $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        // Xây dựng câu lệnh SQL cơ bản
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                JOIN brands b ON p.brand_id = b.id";

        // Xây dựng mệnh đề WHERE
        list($whereClause, $params) = $this->buildWhereClause($filters);
        $sql .= $whereClause;

        // Thêm sắp xếp và phân trang
        $sql .= " ORDER BY p.view_count DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        // Gán các giá trị cho tham số của WHERE
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }

        // Gán giá trị cho LIMIT và OFFSET
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Đếm tổng số sản phẩm với bộ lọc.
     * @param array $filters Mảng chứa các điều kiện lọc.
     * @return int
     */
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

    /**
     * Hàm trợ giúp để xây dựng mệnh đề WHERE và các tham số.
     * @param array $filters
     * @return array
     */
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

    // --- Các phương thức khác giữ nguyên ---
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
    /**
     * Lấy thông tin một sản phẩm bằng ID.
     * @param int $id
     * @return object|false
     */
    public function getProductById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Thêm một sản phẩm mới vào CSDL.
     * @param array $data Dữ liệu sản phẩm.
     * @return bool
     */
    public function createProduct($data)
    {
        $sql = "INSERT INTO products (name, slug, description, specifications, image_url, category_id, brand_id, price) 
                VALUES (:name, :slug, :description, :specifications, :image_url, :category_id, :brand_id, :price)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'],
            ':specifications' => $data['specifications'],
            ':image_url' => $data['image_url'],
            ':category_id' => $data['category_id'],
            ':brand_id' => $data['brand_id'],
            ':price' => $data['price']
        ]);
    }

    /**
     * Cập nhật thông tin một sản phẩm.
     * @param int $id ID sản phẩm.
     * @param array $data Dữ liệu mới.
     * @return bool
     */
    public function updateProduct($id, $data)
    {
        $sql = "UPDATE products SET 
                    name = :name,
                    slug = :slug,
                    description = :description,
                    specifications = :specifications,
                    brand_id = :brand_id,
                    price = :price
                    -- Bỏ category_id ra khỏi câu lệnh UPDATE
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'],
            ':specifications' => $data['specifications'],
            ':brand_id' => $data['brand_id'],
            ':price' => $data['price']
        ]);
    }


    /**
     * Xóa một sản phẩm.
     * @param int $id
     * @return bool
     */
    public function deleteProduct($id)
    {
        // (Tùy chọn) Xóa file ảnh trước khi xóa bản ghi trong CSDL
        $product = $this->getProductById($id);
        if ($product && !empty($product->image_url) && file_exists($product->image_url)) {
            unlink($product->image_url);
        }

        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    /**
     * Lấy tất cả ảnh trong thư viện của một sản phẩm.
     * @param int $productId
     * @return array
     */
    public function getGalleryImages($productId)
    {
        $stmt = $this->db->prepare("SELECT * FROM product_gallery WHERE product_id = :product_id ORDER BY sort_order ASC");
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Thêm nhiều ảnh mới vào thư viện.
     * @param int $productId
     * @param array $imagePaths Mảng chứa các đường dẫn ảnh.
     * @return bool
     */
    public function addGalleryImages($productId, $imagePaths)
    {
        if (empty($imagePaths)) {
            return true;
        }
        $sql = "INSERT INTO product_gallery (product_id, image_url) VALUES ";
        $values = [];
        foreach ($imagePaths as $path) {
            $values[] = "(:product_id, '" . $path . "')";
        }
        $sql .= implode(', ', $values);
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':product_id' => $productId]);
    }

    /**
     * Xóa một ảnh khỏi thư viện.
     * @param int $imageId
     * @return bool
     */
    public function deleteGalleryImage($imageId)
    {
        // Lấy thông tin ảnh để xóa file vật lý
        $stmt = $this->db->prepare("SELECT image_url FROM product_gallery WHERE id = :id");
        $stmt->execute([':id' => $imageId]);
        $image = $stmt->fetch(PDO::FETCH_OBJ);

        if ($image && file_exists($image->image_url)) {
            unlink($image->image_url);
        }

        // Xóa bản ghi trong CSDL
        $deleteStmt = $this->db->prepare("DELETE FROM product_gallery WHERE id = :id");
        return $deleteStmt->execute([':id' => $imageId]);
    }

    /**
     * Đặt một ảnh làm ảnh đại diện.
     * @param int $productId
     * @param int $imageId
     * @return bool
     */
    public function setFeaturedImage($productId, $imageId)
    {
        // Lấy URL của ảnh trong thư viện
        $stmt = $this->db->prepare("SELECT image_url FROM product_gallery WHERE id = :id AND product_id = :product_id");
        $stmt->execute([':id' => $imageId, ':product_id' => $productId]);
        $image = $stmt->fetch(PDO::FETCH_OBJ);

        if ($image) {
            // Cập nhật cột image_url trong bảng products
            $updateStmt = $this->db->prepare("UPDATE products SET image_url = :image_url WHERE id = :id");
            return $updateStmt->execute([':image_url' => $image->image_url, ':id' => $productId]);
        }
        return false;
    }
}
