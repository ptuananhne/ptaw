<?php
class ProductAttribute
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Thêm một giá trị mới cho thuộc tính nếu nó chưa tồn tại.
     */
    public function addTerm($attribute_id, $term_name)
    {
        $trimmed_term_name = trim($term_name);
        if (empty($trimmed_term_name)) {
            return true; // Bỏ qua giá trị rỗng
        }

        // Kiểm tra xem giá trị đã tồn tại chưa
        $stmt_check = $this->db->prepare("SELECT id FROM attribute_terms WHERE attribute_id = :attribute_id AND name = :name");
        $stmt_check->execute([':attribute_id' => $attribute_id, ':name' => $trimmed_term_name]);

        if ($stmt_check->fetch()) {
            return true; // Đã tồn tại, không làm gì cả
        }

        // Nếu chưa tồn tại, thêm mới
        try {
            $stmt_add = $this->db->prepare("INSERT INTO attribute_terms (attribute_id, name) VALUES (:attribute_id, :name)");
            return $stmt_add->execute([':attribute_id' => $attribute_id, ':name' => $trimmed_term_name]);
        } catch (PDOException $e) {
            error_log("Không thể thêm giá trị mới: " . $e->getMessage());
            return false;
        }
    }

    // ... các hàm khác giữ nguyên ...
    public function getAllWithTerms()
    {
        $stmt_attrs = $this->db->query("SELECT * FROM attributes ORDER BY name ASC");
        $attributes = $stmt_attrs->fetchAll(PDO::FETCH_OBJ);
        $stmt_terms = $this->db->prepare("SELECT * FROM attribute_terms WHERE attribute_id = :attribute_id ORDER BY name ASC");
        foreach ($attributes as $attribute) {
            $stmt_terms->execute([':attribute_id' => $attribute->id]);
            $attribute->terms = $stmt_terms->fetchAll(PDO::FETCH_OBJ);
        }
        return $attributes;
    }
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM attributes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $attribute = $stmt->fetch(PDO::FETCH_OBJ);
        if ($attribute) {
            $stmt_terms = $this->db->prepare("SELECT * FROM attribute_terms WHERE attribute_id = :id ORDER BY name ASC");
            $stmt_terms->execute([':id' => $id]);
            $attribute->terms = $stmt_terms->fetchAll(PDO::FETCH_OBJ);
        }
        return $attribute;
    }
    public function create($name)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO attributes (name) VALUES (:name)");
            return $stmt->execute([':name' => $name]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function update($id, $name)
    {
        try {
            $stmt = $this->db->prepare("UPDATE attributes SET name = :name WHERE id = :id");
            return $stmt->execute([':name' => $name, ':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function syncTerms($attribute_id, $terms_from_form)
    {
        $stmt = $this->db->prepare("SELECT name FROM attribute_terms WHERE attribute_id = :attribute_id");
        $stmt->execute([':attribute_id' => $attribute_id]);
        $db_terms_obj = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db_terms = array_map(fn($t) => $t->name, $db_terms_obj);
        $to_add = array_diff($terms_from_form, $db_terms);
        if (!empty($to_add)) {
            $stmt_add = $this->db->prepare("INSERT INTO attribute_terms (attribute_id, name) VALUES (:attribute_id, :name)");
            foreach ($to_add as $term_name) {
                $stmt_add->execute([':attribute_id' => $attribute_id, ':name' => $term_name]);
            }
        }
        $to_delete = array_diff($db_terms, $terms_from_form);
        if (!empty($to_delete)) {
            $stmt_delete = $this->db->prepare("DELETE FROM attribute_terms WHERE attribute_id = :attribute_id AND name = :name");
            foreach ($to_delete as $term_name) {
                $stmt_delete->execute([':attribute_id' => $attribute_id, ':name' => $term_name]);
            }
        }
        return true;
    }
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM attributes WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
