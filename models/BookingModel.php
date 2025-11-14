<?php
class BookingModel {
    private $conn;
    private $table_name = "bookings"; // Cần tạo bảng này

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function all() {
        $query = "SELECT b.*, t.name as tour_name, u.full_name as customer_name 
                  FROM bookings b
                  LEFT JOIN tours t ON b.tour_id = t.id
                  LEFT JOIN users u ON b.user_id = u.id
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>