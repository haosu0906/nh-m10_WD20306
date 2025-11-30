<?php
require_once 'BaseModel.php';

class BookingModel extends BaseModel {
    protected $table = 'bookings';
    
    public function all() {
        $sql = "SELECT b.*, t.title as tour_name 
                FROM {$this->table} b
                LEFT JOIN tours t ON b.tour_id = t.id
                ORDER BY b.created_at DESC";
        return $this->query($sql);
    }
    
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (tour_id, customer_name, customer_email, customer_phone, 
                 number_of_people, status, total_price, start_date, note)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $this->execute($sql, [
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
        
        return $this->getLastInsertId();
    }
    
    public function updateStatus($id, $status, $note = null) {
        // Lấy trạng thái cũ
        $oldStatus = $this->find($id)['status'] ?? null;
        
        // Cập nhật trạng thái mới
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $this->execute($sql, [$status, $id]);
        
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
        return $this->query($sql, [$bookingId]);
    }
    
    protected function addLog($bookingId, $oldStatus, $newStatus, $note = null) {
        $sql = "INSERT INTO booking_logs 
                (booking_id, old_status, new_status, note)
                VALUES (?, ?, ?, ?)";
                
        $this->execute($sql, [
            $bookingId,
            $oldStatus,
            $newStatus,
            $note
        ]);
    }
}
