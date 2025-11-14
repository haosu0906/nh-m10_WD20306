<?php
// D:\laragon\www\base\models\ScheduleModel.php

class ScheduleModel extends BaseModel {
    protected $table_name = "tour_schedules";

    public function __construct() {
        parent::__construct();
    }

    public function all() {
        $query = "SELECT ts.*, t.title AS tour_title, u.full_name AS guide_name 
                  FROM tour_schedules ts
                  LEFT JOIN tours t ON ts.tour_id = t.id
                  LEFT JOIN users u ON ts.guide_user_id = u.id
                  ORDER BY ts.start_date ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $query = "SELECT ts.*, t.title AS tour_title, u.full_name AS guide_name 
                  FROM tour_schedules ts
                  LEFT JOIN tours t ON ts.tour_id = t.id
                  LEFT JOIN users u ON ts.guide_user_id = u.id
                  WHERE ts.id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO tour_schedules (tour_id, start_date, end_date, guide_user_id, driver_user_id, max_capacity) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $data['tour_id'] ?? null,
            $data['start_date'] ?? '',
            $data['end_date'] ?? '',
            $data['guide_user_id'] ?? null,
            $data['driver_user_id'] ?? null,
            $data['max_capacity'] ?? 20,
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE tour_schedules SET tour_id = ?, start_date = ?, end_date = ?, guide_user_id = ?, 
                  driver_user_id = ?, max_capacity = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $data['tour_id'] ?? null,
            $data['start_date'] ?? '',
            $data['end_date'] ?? '',
            $data['guide_user_id'] ?? null,
            $data['driver_user_id'] ?? null,
            $data['max_capacity'] ?? 20,
            (int)$id
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM tour_schedules WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([(int)$id]);
    }

    public function deleteByTour($tourId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE tour_id = ?");
            $stmt->execute([(int)$tourId]);
        } catch (PDOException $e) {
            // ignore if table missing
        }
    }

    // Lấy lịch trình theo tour
    public function getByTour($tour_id) {
        $query = "SELECT ts.*, t.title AS tour_title, u.full_name AS guide_name 
                  FROM tour_schedules ts
                  LEFT JOIN tours t ON ts.tour_id = t.id
                  LEFT JOIN users u ON ts.guide_user_id = u.id
                  WHERE ts.tour_id = ?
                  ORDER BY ts.start_date ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kiểm tra HDV có trùng lịch trong khoảng ngày hay không
    public function guideHasConflict($guideUserId, $startDate, $endDate) {
        $sql = "SELECT COUNT(*) FROM tour_schedules 
                WHERE guide_user_id = :gid
                  AND NOT (end_date < :start OR start_date > :end)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':gid' => (int)$guideUserId,
            ':start' => $startDate,
            ':end' => $endDate,
        ]);
        return ((int)$stmt->fetchColumn()) > 0; // true nếu có trùng
    }

    // Tìm một HDV rảnh trong khoảng ngày (ưu tiên theo ID nhỏ nhất)
    public function findAvailableGuide($startDate, $endDate) {
        $sql = "SELECT u.id
                FROM users u
                WHERE u.role = 'guide'
                  AND u.id NOT IN (
                    SELECT guide_user_id FROM tour_schedules ts
                    WHERE NOT (ts.end_date < :start OR ts.start_date > :end)
                    AND ts.guide_user_id IS NOT NULL
                  )
                ORDER BY u.id ASC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':start' => $startDate, ':end' => $endDate]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['id'] : null;
    }
}
?>