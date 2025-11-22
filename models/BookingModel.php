<?php
// D:\laragon\www\base\models\BookingModel.php

class BookingModel extends BaseModel {
    protected $table_name = "bookings";

    public function __construct() {
        parent::__construct();
    }

    public function all() {
        $query = "SELECT b.*, t.name as tour_name, u.full_name as customer_name 
                  FROM bookings b
                  LEFT JOIN tours t ON b.tour_id = t.id
                  LEFT JOIN users u ON b.user_id = u.id
                  ORDER BY b.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $query = "SELECT b.*, t.name as tour_name, u.full_name as customer_name 
                  FROM bookings b
                  LEFT JOIN tours t ON b.tour_id = t.id
                  LEFT JOIN users u ON b.user_id = u.id
                  WHERE b.id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO bookings (tour_id, user_id, booking_code, number_of_guests, total_amount, status, special_requests) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        
        $booking_code = 'BK' . date('Ymd') . rand(1000, 9999);
        
        return $stmt->execute([
            $data['tour_id'] ?? null,
            $data['user_id'] ?? null,
            $booking_code,
            $data['number_of_guests'] ?? 1,
            $data['total_amount'] ?? 0,
            $data['status'] ?? 'pending',
            $data['special_requests'] ?? ''
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE bookings SET tour_id = ?, user_id = ?, number_of_guests = ?, 
                  total_amount = ?, status = ?, special_requests = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $data['tour_id'] ?? null,
            $data['user_id'] ?? null,
            $data['number_of_guests'] ?? 1,
            $data['total_amount'] ?? 0,
            $data['status'] ?? 'pending',
            $data['special_requests'] ?? '',
            (int)$id
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM bookings WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([(int)$id]);
    }

    // Cập nhật trạng thái booking
    public function updateStatus($id, $status) {
        $query = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$status, (int)$id]);
    }

    // Lấy booking theo user
    public function getByUser($user_id) {
        $query = "SELECT b.*, t.name as tour_name 
                  FROM bookings b
                  LEFT JOIN tours t ON b.tour_id = t.id
                  WHERE b.user_id = ?
                  ORDER BY b.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>