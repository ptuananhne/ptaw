<?php
class Admin
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Tìm admin bằng username.
     * @param string $username Tên đăng nhập của admin.
     * @return object|false Trả về object chứa thông tin admin hoặc false nếu không tìm thấy.
     */
    public function findByUsername($username)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function countAll()
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM admins");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
}
