<?php

class GuideAssignmentsModel extends BaseModel
{
    protected $table = 'guide_assignments';

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

    public function getByTour($tour_id)
    {
        $sql = "SELECT ga.*, u.full_name, u.email
                FROM {$this->table} ga
                LEFT JOIN users u ON ga.guide_user_id = u.id
                WHERE ga.tour_id = :tour_id
                ORDER BY ga.assignment_date ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':tour_id' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (tour_id, guide_user_id, assignment_date, assignment_type, status, notes)
                VALUES (:tour_id, :guide_user_id, :assignment_date, :assignment_type, :status, :notes)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':tour_id' => $data['tour_id'],
            ':guide_user_id' => $data['guide_user_id'],
            ':assignment_date' => $data['assignment_date'] ?? date('Y-m-d'),
            ':assignment_type' => $data['assignment_type'] ?? 'main_guide',
            ':status' => $data['status'] ?? 'confirmed',
            ':notes' => $data['notes'] ?? ''
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
            ':status' => $data['status'] ?? 'confirmed',
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
