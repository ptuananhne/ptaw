<?php
// Model này tương tác với bảng `admins` trong database
class Admin
{
    private $db;

    public function __construct()
    {
        // Khởi tạo kết nối database
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Tìm một quản trị viên bằng username.
     * @param string $username Tên đăng nhập.
     * @return object|false Trả về đối tượng admin nếu tìm thấy, ngược lại trả về false.
     */
    public function findByUsername($username)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            // Có thể ghi log lỗi ở đây
            error_log($e->getMessage());
            return false;
        }
    }
}
