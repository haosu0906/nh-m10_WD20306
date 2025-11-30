<?php

class GuideRatingsModel extends BaseModel
{
    protected $table = 'guide_ratings';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT gr.*, u.full_name as guide_name, b.id as booking_id
                FROM {$this->table} gr
                LEFT JOIN users u ON gr.guide_user_id = u.id
                LEFT JOIN bookings b ON gr.booking_id = b.id
                ORDER BY gr.id DESC";
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

    public function getByGuide($guide_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE guide_user_id = :guide_user_id 
                ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':guide_user_id' => $guide_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRating($guide_id)
    {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings
                FROM {$this->table} 
                WHERE guide_user_id = :guide_user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':guide_user_id' => $guide_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (guide_user_id, booking_id, rating, comment, created_by)
                VALUES (:guide_user_id, :booking_id, :rating, :comment, :created_by)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':guide_user_id' => $data['guide_user_id'],
            ':booking_id' => $data['booking_id'] ?? null,
            ':rating' => $data['rating'] ?? 5,
            ':comment' => $data['comment'] ?? '',
            ':created_by' => $data['created_by'] ?? $_SESSION['user_id'] ?? 1
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                rating = :rating,
                comment = :comment
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':rating' => $data['rating'] ?? 5,
            ':comment' => $data['comment'] ?? '',
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
