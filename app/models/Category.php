<?php
class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy tất cả danh mục.
     * @return array Danh sách các đối tượng danh mục.
     */
    public function getAll()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin một danh mục bằng slug.
     * @param string $slug
     * @return object|false
     */
    public function getCategoryBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare("SELECT id, name, slug FROM categories WHERE slug = :slug");
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả thương hiệu thuộc về một danh mục.
     * @param string $slug
     * @return array
     */
    public function getBrandsByCategorySlug($slug)
    {
        try {
            $query = "
                SELECT b.id, b.name 
                FROM brands b
                JOIN category_brand cb ON b.id = cb.brand_id
                JOIN categories c ON cb.category_id = c.id
                WHERE c.slug = :slug
                ORDER BY b.name ASC
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Đếm tổng số danh mục.
     * @return int
     */
    public function countAll()
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM categories");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    /**
     * Tạo một danh mục mới.
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO categories (name, slug) VALUES (:name, :slug)");
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':slug', $data['slug']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Xóa một danh mục bằng ID.
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
