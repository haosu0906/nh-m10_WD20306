<?php
// D:\laragon\www\base\models\TourModel.php
require_once __DIR__ . '/../configs/database.php';

class TourModel {
    private $conn;
    private $table_name = "tours";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy tất cả tour
    public function all() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tour theo ID
    public function find($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo tour mới
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET category_id=:category_id, name=:name, description=:description, 
                  tour_type=:tour_type, price=:price, status=:status, duration=:duration";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":category_id", $data['category_id']);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":tour_type", $data['tour_type']);
        $stmt->bindParam(":price", $data['price']);
        $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":duration", $data['duration']);
        
        return $stmt->execute();
    }

    // Cập nhật tour
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET category_id=:category_id, name=:name, description=:description, 
                  tour_type=:tour_type, price=:price, status=:status, duration=:duration 
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":category_id", $data['category_id']);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":tour_type", $data['tour_type']);
        $stmt->bindParam(":price", $data['price']);
        $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":duration", $data['duration']);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    // Xóa tour
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Lấy tour theo danh mục
    public function getByCategory($category_id) {
        $query = "SELECT t.*, tc.name as category_name 
                  FROM " . $this->table_name . " t 
                  LEFT JOIN tour_categories tc ON t.category_id = tc.id 
                  WHERE t.category_id = ? 
                  ORDER BY t.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>