<?php
// D:\laragon\www\base\models\BookingModel.php

class BookingModel extends BaseModel {
    protected $table_name = "bookings";

    public function __construct() {
        parent::__construct();
    }

    public function all() {
        $sql = "SELECT b.*, t.title as tour_name, u.full_name as customer_name
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u ON b.customer_user_id = u.id
                ORDER BY b.date_booked DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $query = "SELECT b.*, t.title as tour_name, u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone, s.name as supplier_name, s.phone as supplier_phone, s.email as supplier_email
                  FROM bookings b
                  LEFT JOIN tours t ON b.tour_id = t.id
                  LEFT JOIN users u ON b.customer_user_id = u.id
                  LEFT JOIN suppliers s ON t.supplier_id = s.id
                  WHERE b.id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table_name} (tour_id, schedule_id, customer_user_id, sales_user_id, total_guests, booking_status, total_price)
                VALUES (:tour_id, :schedule_id, :customer_user_id, :sales_user_id, :total_guests, :booking_status, :total_price)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $params = [
            ':tour_id' => $data['tour_id'] ?? null,
            ':schedule_id' => $data['schedule_id'] ?? null,
            ':customer_user_id' => $data['customer_user_id'] ?? null,
            ':sales_user_id' => $data['sales_user_id'] ?? null,
            ':total_guests' => $data['total_guests'] ?? 1,
            ':booking_status' => $data['booking_status'] ?? 'pending',
            ':total_price' => $data['total_price'] ?? 0
        ];
        
        if ($stmt->execute($params)) {
            return $this->pdo->lastInsertId();
        } else {
            return false;
        }
    }

    public function update($id, $data) {
        $query = "UPDATE bookings SET tour_id = ?, total_guests = ?, total_price = ?, booking_status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $data['tour_id'] ?? null,
            $data['total_guests'] ?? 1,
            $data['total_price'] ?? 0,
            $data['booking_status'] ?? 'pending',
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
        $query = "UPDATE bookings SET booking_status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$status, (int)$id]);
    }

    // Lấy booking theo user
    public function getByUser($user_id) {
        $query = "SELECT b.*, t.title as tour_name 
                  FROM bookings b
                  LEFT JOIN tours t ON b.tour_id = t.id
                  WHERE b.customer_user_id = ?
                  ORDER BY b.date_booked DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách khách của booking
    public function getBookingGuests($booking_id) {
        $query = "SELECT * FROM booking_guests WHERE booking_id = ? ORDER BY id ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy lịch sử thay đổi trạng thái
    public function getStatusHistory($booking_id) {
        // Bảng booking_status_logs có thể không tồn tại, trả về array rỗng
        try {
            $query = "SELECT * FROM booking_status_logs WHERE booking_id = ? ORDER BY id DESC LIMIT 10";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([(int)$booking_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Lấy thông tin khách hàng đầy đủ
    public function getCustomerInfo($user_id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy lịch sử thanh toán
    public function getPaymentHistory($booking_id) {
        try {
            $query = "SELECT p.*, u.full_name as confirmed_by, p.payment_method as method
                      FROM payments p
                      LEFT JOIN users u ON p.created_by = u.id
                      WHERE p.booking_id = ? 
                      ORDER BY p.id DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([(int)$booking_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Log the error for debugging if needed
            // error_log($e->getMessage());
            return [];
        }
    }

    // Tính tổng đã thanh toán
    public function getTotalPaid($booking_id) {
        try {
            $query = "SELECT COALESCE(SUM(amount), 0) as total_paid FROM payments WHERE booking_id = ? AND status = 'completed'";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([(int)$booking_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_paid'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    // Lấy danh sách nhà cung cấp cùng dịch vụ cho tour
    public function getTourSuppliers($tour_id) {
        try {
            $query = "SELECT DISTINCT s.id, s.name, s.type, s.phone, s.email, s.contact_person,
                             te.expense_type as service_type, te.description as service_description
                      FROM tour_expenses te
                      JOIN suppliers s ON te.supplier_id = s.id
                      WHERE te.tour_id = ?
                      ORDER BY s.id, te.expense_type";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([(int)$tour_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
