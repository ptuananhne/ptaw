<?php

class Taxonomy
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // --- CATEGORY METHODS ---

    /**
     * Lấy danh mục, sắp xếp theo thứ tự đã lưu.
     */
    public function getCategories()
    {
        $stmt = $this->db->query("SELECT c.*, COUNT(p.id) as product_count 
                                 FROM categories c
                                 LEFT JOIN products p ON c.id = p.category_id
                                 GROUP BY c.id
                                 ORDER BY c.sort_order ASC"); // Sắp xếp theo sort_order
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Cập nhật thứ tự của các danh mục.
     * @param array $categoryIds Mảng chứa các ID theo thứ tự mới.
     * @return bool
     */
    public function updateCategoryOrder($categoryIds)
    {
        if (empty($categoryIds) || !is_array($categoryIds)) {
            return false;
        }

        $this->db->beginTransaction();
        try {
            foreach ($categoryIds as $index => $id) {
                $stmt = $this->db->prepare("UPDATE categories SET sort_order = :sort_order WHERE id = :id");
                $stmt->execute([':sort_order' => $index, ':id' => (int)$id]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Tạo một danh mục mới.
     * @param string $name
     * @return bool
     */
    public function createCategory($name)
    {
        $slug = create_slug($name);
        $stmt = $this->db->prepare("INSERT INTO categories (name, slug) VALUES (:name, :slug)");
        return $stmt->execute([':name' => $name, ':slug' => $slug]);
    }

    /**
     * Xóa danh mục một cách an toàn.
     * Sẽ không xóa nếu danh mục vẫn còn sản phẩm.
     * @param int $id
     * @return bool
     */
    public function deleteCategory($id)
    {
        // B1: Kiểm tra xem có sản phẩm nào đang sử dụng danh mục này không.
        $checkStmt = $this->db->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = :id");
        $checkStmt->execute([':id' => $id]);
        $result = $checkStmt->fetch(PDO::FETCH_OBJ);

        // B2: Nếu có sản phẩm (count > 0), không cho xóa và trả về false.
        if ($result && $result->count > 0) {
            return false;
        }

        // B3: Nếu không có sản phẩm, tiến hành xóa.
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // --- BRAND METHODS ---

    /**
     * Lấy tất cả thương hiệu cùng với số lượng sản phẩm.
     * @return array
     */
    public function getBrands()
    {
        $stmt = $this->db->query("SELECT b.*, COUNT(p.id) as product_count
                                 FROM brands b
                                 LEFT JOIN products p ON b.id = p.brand_id
                                 GROUP BY b.id, b.name, b.slug, b.logo_url, b.created_at, b.updated_at
                                 ORDER BY b.name ASC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Tạo một thương hiệu mới.
     * @param string $name
     * @param string $logoPath
     * @return bool
     */
    public function createBrand($name, $logoPath)
    {
        $slug = create_slug($name);
        $stmt = $this->db->prepare("INSERT INTO brands (name, slug, logo_url) VALUES (:name, :slug, :logo_url)");
        return $stmt->execute([':name' => $name, ':slug' => $slug, ':logo_url' => $logoPath]);
    }

    /**
     * Xóa thương hiệu một cách an toàn.
     * Sẽ không xóa nếu thương hiệu vẫn còn sản phẩm.
     * @param int $id
     * @return bool
     */
    public function deleteBrand($id)
    {
        // B1: Kiểm tra xem có sản phẩm nào đang sử dụng thương hiệu này không.
        $checkStmt = $this->db->prepare("SELECT COUNT(*) as count FROM products WHERE brand_id = :id");
        $checkStmt->execute([':id' => $id]);
        $result = $checkStmt->fetch(PDO::FETCH_OBJ);

        // B2: Nếu có sản phẩm (count > 0), không cho xóa và trả về false.
        if ($result && $result->count > 0) {
            return false;
        }

        // B3: Nếu không có sản phẩm, tiến hành xóa (bao gồm cả xóa file logo).
        $stmt_get = $this->db->prepare("SELECT logo_url FROM brands WHERE id = :id");
        $stmt_get->execute([':id' => $id]);
        $brand = $stmt_get->fetch(PDO::FETCH_OBJ);
        if ($brand && !empty($brand->logo_url) && file_exists($brand->logo_url)) {
            unlink($brand->logo_url);
        }

        $stmt_delete = $this->db->prepare("DELETE FROM brands WHERE id = :id");
        return $stmt_delete->execute([':id' => $id]);
    }

    // --- RELATIONSHIP METHODS ---

    /**
     * Lấy danh sách ID thương hiệu đã được liên kết với một danh mục.
     * @param int $categoryId
     * @return array
     */
    public function getBrandIdsForCategory($categoryId)
    {
        $stmt = $this->db->prepare("SELECT brand_id FROM category_brand WHERE category_id = :category_id");
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Cập nhật các liên kết giữa một danh mục và các thương hiệu.
     * @param int $categoryId
     * @param array $brandIds
     * @return bool
     */
    public function updateCategoryBrandLinks($categoryId, $brandIds)
    {
        // Bắt đầu một transaction để đảm bảo toàn vẹn dữ liệu
        $this->db->beginTransaction();
        try {
            // 1. Xóa tất cả các liên kết cũ của danh mục này
            $stmt_delete = $this->db->prepare("DELETE FROM category_brand WHERE category_id = :category_id");
            $stmt_delete->execute([':category_id' => $categoryId]);

            // 2. Thêm các liên kết mới nếu có
            if (!empty($brandIds)) {
                $sql = "INSERT INTO category_brand (category_id, brand_id) VALUES ";
                $values = [];
                foreach ($brandIds as $brandId) {
                    // Đảm bảo brandId là một số nguyên để tránh SQL Injection
                    $values[] = "(:category_id, " . (int)$brandId . ")";
                }
                $sql .= implode(', ', $values);
                $stmt_insert = $this->db->prepare($sql);
                $stmt_insert->execute([':category_id' => $categoryId]);
            }

            // Nếu mọi thứ thành công, commit transaction
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Nếu có lỗi, rollback lại tất cả các thay đổi
            $this->db->rollBack();
            return false;
        }
    }
}
