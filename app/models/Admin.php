<?php

class Admin
{
    private $db; // Biến này sẽ chứa đối tượng kết nối PDO

    public function __construct()
    {
        // Lấy kết nối CSDL thông qua lớp Database gốc của bạn
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Tìm kiếm admin bằng username
     * @param string $username
     * @return mixed Trả về thông tin admin nếu tìm thấy, ngược lại trả về false
     */
    public function findByUsername($username)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM admins WHERE username = :username');
            $stmt->bindValue(':username', $username);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            // Ghi lại lỗi để gỡ rối (tùy chọn)
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Tìm kiếm admin bằng ID
     * @param int $id
     * @return mixed Trả về thông tin admin nếu tìm thấy, ngược lại trả về false
     */
    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM admins WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
