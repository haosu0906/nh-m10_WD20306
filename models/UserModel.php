<?php
class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getGuides() {
        $query = "SELECT u.*, gi.certificate_no, gi.languages, gi.experience_years 
                  FROM users u 
                  LEFT JOIN guides_info gi ON u.id = gi.user_id 
                  WHERE u.role = 'guide'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStaff() {
        $query = "SELECT * FROM users WHERE role IN ('admin', 'staff')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>