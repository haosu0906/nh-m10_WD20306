<?php

class BookingGuestsModel extends BaseModel
{
    protected $table = 'booking_guests';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
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
                ORDER BY id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':booking_id' => $booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByBookingId($booking_id)
    {
        return $this->getByBooking($booking_id);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (booking_id, full_name, gender, dob, id_document_no, is_checked_in, notes)
                VALUES (:booking_id, :full_name, :gender, :dob, :id_document_no, :is_checked_in, :notes)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':booking_id' => $data['booking_id'],
            ':full_name' => $data['full_name'] ?? '',
            ':gender' => $data['gender'] ?? '',
            ':dob' => $data['dob'] ?? null,
            ':id_document_no' => $data['id_document_no'] ?? '',
            ':is_checked_in' => $data['is_checked_in'] ?? 0,
            ':notes' => $data['notes'] ?? ''
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                full_name = :full_name,
                gender = :gender,
                dob = :dob,
                id_document_no = :id_document_no,
                is_checked_in = :is_checked_in,
                notes = :notes
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':full_name' => $data['full_name'] ?? '',
            ':gender' => $data['gender'] ?? '',
            ':dob' => isset($data['dob']) ? $data['dob'] : '1970-01-01',
            ':id_document_no' => $data['id_document_no'] ?? '',
            ':is_checked_in' => $data['is_checked_in'] ?? 0,
            ':notes' => $data['notes'] ?? '',
            ':id' => $id
        ]);
    }

    public function setCheckin($id, $checked)
    {
        $sql = "UPDATE {$this->table} SET is_checked_in = :checked WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':checked' => (int)$checked, ':id' => (int)$id]);
    }

    public function addCheckinLog($booking_id, $guest_id, $stage = null, $location = null)
    {
        try {
            $sql = "INSERT INTO guest_checkins (booking_id, guest_id, stage, location, checked_at) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([(int)$booking_id, (int)$guest_id, (string)$stage, (string)$location]);
        } catch (Exception $e) {
            // table may not exist yet; safely ignore
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
