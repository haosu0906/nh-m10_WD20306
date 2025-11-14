<?php
class ScheduleModel {
    private $conn;
    private $table_name = "tour_schedules"; // Cần tạo bảng này

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function all() {
        $query = "SELECT ts.*, t.name as tour_name, u.full_name as guide_name 
                  FROM tour_schedules ts
                  LEFT JOIN tours t ON ts.tour_id = t.id
                  LEFT JOIN users u ON ts.guide_id = u.id
                  ORDER BY ts.departure_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>