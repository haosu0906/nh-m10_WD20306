<?php

class PaymentsModel extends BaseModel
{
    protected $table = 'payments';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY payment_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByBooking($booking_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE booking_id = :booking_id 
                ORDER BY payment_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':booking_id' => $booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (booking_id, amount, payment_method, transaction_id, payment_date, status, notes, created_by)
                VALUES (:booking_id, :amount, :payment_method, :transaction_id, :payment_date, :status, :notes, :created_by)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':booking_id' => $data['booking_id'],
            ':amount' => $data['amount'],
            ':payment_method' => $data['payment_method'] ?? 'bank_transfer',
            ':transaction_id' => $data['transaction_id'] ?? '',
            ':payment_date' => $data['payment_date'] ?? date('Y-m-d H:i:s'),
            ':status' => $data['status'] ?? 'completed',
            ':notes' => $data['notes'] ?? '',
            ':created_by' => $data['created_by'] ?? $_SESSION['user_id'] ?? 1
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                status = :status,
                notes = :notes
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $data['status'] ?? 'completed',
            ':notes' => $data['notes'] ?? '',
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
