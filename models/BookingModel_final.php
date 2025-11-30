<?php
require_once 'BaseModel.php';

class BookingModel extends BaseModel {
    protected $table = 'bookings';
    
    public function all() {
        $sql = "SELECT b.*, t.title as tour_name 
                FROM {$this->table} b
                LEFT JOIN tours t ON b.tour_id = t.id
                ORDER BY b.date_booked DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (tour_id, customer_name, customer_email, customer_phone, 
                 number_of_people, status, total_price, start_date, note)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['tour_id'],
            $data['customer_name'],
            $data['customer_email'],
            $data['customer_phone'],
            $data['number_of_people'],
            $data['status'] ?? 'pending',
            $data['total_price'],
            $data['start_date'],
            $data['note'] ?? null
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    public function updateStatus($id, $status, $note = null) {
        // Lấy trạng thái cũ
        $oldStatus = $this->find($id)['status'] ?? null;
        
        // Cập nhật trạng thái mới
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$status, $id]);
        
        // Ghi log
        $this->addLog($id, $oldStatus, $status, $note);
        
        return true;
    }
    
    public function getLogs($bookingId) {
        $sql = "SELECT bl.*, u.full_name as changed_by_name 
                FROM booking_logs bl
                LEFT JOIN users u ON bl.changed_by = u.id
                WHERE bl.booking_id = ?
                ORDER BY bl.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    protected function addLog($bookingId, $oldStatus, $newStatus, $note = null) {
        $sql = "INSERT INTO booking_logs 
                (booking_id, old_status, new_status, note)
                VALUES (?, ?, ?, ?)";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $bookingId,
            $oldStatus,
            $newStatus,
            $note
        ]);
    }
}
