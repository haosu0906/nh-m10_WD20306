<?php
require_once 'BaseModel.php';

class BookingModel extends BaseModel {
    
    // Lấy tất cả bookings với filter
    public function all($statusFilter = null) {
        $sql = "SELECT b.*, t.title as tour_name 
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id";
        
        if ($statusFilter) {
            $sql .= " WHERE b.booking_status = ?";
        }
        
        $sql .= " ORDER BY b.date_booked DESC";
        
        $stmt = $this->pdo->prepare($sql);
        if ($statusFilter) {
            $stmt->execute([$statusFilter]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Tạo booking mới với auto code
    public function create($data) {
        // Generate booking code
        $bookingCode = 'BK' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $sql = "INSERT INTO bookings 
                (booking_code, tour_id, customer_user_id, sales_user_id, 
                 total_guests, booking_status, total_price, start_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $bookingCode,
            $data['tour_id'],
            $data['customer_user_id'] ?? 1,
            $data['sales_user_id'] ?? 1,
            $data['total_guests'],
            $data['booking_status'] ?? 'pending',
            $data['total_price'],
            $data['start_date']
        ]);
        
        $bookingId = $this->pdo->lastInsertId();
        
        // Thêm khách hàng vào bảng booking_guests (đã có sẵn)
        if (!empty($data['customers'])) {
            $this->addGuests($bookingId, $data['customers']);
        }
        
        return $bookingId;
    }
    
    // Thêm khách hàng vào booking_guests (dùng bảng có sẵn)
    public function addGuests($bookingId, $customers) {
        $sql = "INSERT INTO booking_guests 
                (booking_id, full_name, email, phone, id_card, birth_date, gender)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($customers as $customer) {
            $stmt->execute([
                $bookingId,
                $customer['full_name'],
                $customer['email'] ?? null,
                $customer['phone'],
                $customer['id_card'] ?? null,
                $customer['birth_date'] ?? null,
                $customer['gender'] ?? 'other'
            ]);
        }
    }
    
    // Kiểm tra số chỗ trống của tour
    public function checkAvailableSeats($tourId, $requestedSeats) {
        $sql = "SELECT 
                    (t.max_participants - COALESCE(SUM(b.total_guests), 0)) as available_seats
                FROM tours t
                LEFT JOIN bookings b ON t.id = b.tour_id 
                    AND b.booking_status != 'canceled'
                    AND b.start_date = CURDATE()
                WHERE t.id = ?
                GROUP BY t.id, t.max_participants";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tourId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['available_seats'] >= $requestedSeats;
    }
    
    // Cập nhật trạng thái với lịch sử
    public function updateStatus($id, $status, $changedBy = null, $note = null) {
        // Lấy trạng thái cũ
        $oldStatus = $this->find($id)['booking_status'] ?? null;
        
        // Cập nhật trạng thái mới
        $sql = "UPDATE bookings SET booking_status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$status, $id]);
        
        // Ghi lịch sử vào booking_status_logs (dùng bảng có sẵn)
        $this->addStatusHistory($id, $oldStatus, $status, $changedBy, $note);
        
        return true;
    }
    
    // Lấy lịch sử thay đổi trạng thái
    public function getStatusHistory($bookingId) {
        $sql = "SELECT h.*, u.full_name as changed_by_name 
                FROM booking_status_logs h
                LEFT JOIN users u ON h.user_id = u.id
                WHERE h.booking_id = ?
                ORDER BY h.changed_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy danh sách khách hàng của booking
    public function getBookingGuests($bookingId) {
        $sql = "SELECT * FROM booking_guests WHERE booking_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy chi tiết booking
    public function find($id) {
        $sql = "SELECT b.*, t.title as tour_name, t.max_participants
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id
                WHERE b.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Thêm lịch sử trạng thái
    private function addStatusHistory($bookingId, $oldStatus, $newStatus, $changedBy, $note) {
        $sql = "INSERT INTO booking_status_logs 
                (booking_id, old_status, new_status, user_id, note)
                VALUES (?, ?, ?, ?, ?)";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $bookingId,
            $oldStatus,
            $newStatus,
            $changedBy,
            $note
        ]);
    }
}
