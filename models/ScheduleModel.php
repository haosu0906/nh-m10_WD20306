<?php
// D:\laragon\www\base\models\ScheduleModel.php

class ScheduleModel extends BaseModel {
    protected $table_name = "tour_schedules";

    public function __construct() {
        parent::__construct();
    }

    protected function hasColumn($name) {
        try {
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `{$this->table_name}` LIKE ?");
            $stmt->execute([$name]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
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
        $driverId = $data['driver_user_id'] ?? null;
        if (empty($driverId)) {
            try {
                $stmtD = $this->pdo->query("SELECT id FROM users WHERE role = 'driver' ORDER BY id ASC LIMIT 1");
                $rowD = $stmtD->fetch(PDO::FETCH_ASSOC);
                if ($rowD) { $driverId = (int)$rowD['id']; }
            } catch (PDOException $e) { /* ignore */ }
            if (empty($driverId) && !empty($data['guide_user_id'])) {
                $driverId = (int)$data['guide_user_id'];
            }
        }

        try {
            $cols = ['tour_id','start_date','end_date','guide_user_id','driver_user_id','max_capacity'];
            $vals = [
                $data['tour_id'] ?? null,
                $data['start_date'] ?? '',
                $data['end_date'] ?? '',
                $data['guide_user_id'] ?? null,
                $driverId ?? 0,
                $data['max_capacity'] ?? 20,
            ];
            if ($this->hasColumn('price_adult')) { $cols[] = 'price_adult'; $vals[] = $data['price_adult'] ?? null; }
            if ($this->hasColumn('price_child')) { $cols[] = 'price_child'; $vals[] = $data['price_child'] ?? null; }
            if ($this->hasColumn('price_infant')) { $cols[] = 'price_infant'; $vals[] = $data['price_infant'] ?? null; }
            $placeholders = implode(',', array_fill(0, count($cols), '?'));
            $query = "INSERT INTO {$this->table_name} (" . implode(',', $cols) . ") VALUES (" . $placeholders . ")";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute($vals);
        } catch (PDOException $e) {
            try {
                $stmtU = $this->pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 1");
                $rowU = $stmtU->fetch(PDO::FETCH_ASSOC);
                $fallbackDriver = $rowU ? (int)$rowU['id'] : ($data['guide_user_id'] ?? null);
                $stmt3 = $this->pdo->prepare("INSERT INTO tour_schedules (tour_id, start_date, end_date, guide_user_id, driver_user_id, max_capacity) VALUES (?, ?, ?, ?, ?, ?)");
                return $stmt3->execute([
                    $data['tour_id'] ?? null,
                    $data['start_date'] ?? '',
                    $data['end_date'] ?? '',
                    $data['guide_user_id'] ?? null,
                    $fallbackDriver ?? 0,
                    $data['max_capacity'] ?? 20,
                ]);
            } catch (PDOException $e2) {
                return false;
            }
        }
    }

    public function update($id, $data) {
        $driverId = $data['driver_user_id'] ?? null;
        if (empty($driverId)) {
            try {
                $stmtD = $this->pdo->query("SELECT id FROM users WHERE role = 'driver' ORDER BY id ASC LIMIT 1");
                $rowD = $stmtD->fetch(PDO::FETCH_ASSOC);
                if ($rowD) { $driverId = (int)$rowD['id']; }
            } catch (PDOException $e) { /* ignore */ }
            if (empty($driverId) && !empty($data['guide_user_id'])) {
                $driverId = (int)$data['guide_user_id'];
            }
        }

        try {
            $sets = ['tour_id = ?','start_date = ?','end_date = ?','guide_user_id = ?','driver_user_id = ?','max_capacity = ?'];
            $vals = [
                $data['tour_id'] ?? null,
                $data['start_date'] ?? '',
                $data['end_date'] ?? '',
                $data['guide_user_id'] ?? null,
                $driverId ?? 0,
                $data['max_capacity'] ?? 20,
            ];
            if ($this->hasColumn('price_adult')) { $sets[] = 'price_adult = ?'; $vals[] = $data['price_adult'] ?? null; }
            if ($this->hasColumn('price_child')) { $sets[] = 'price_child = ?'; $vals[] = $data['price_child'] ?? null; }
            if ($this->hasColumn('price_infant')) { $sets[] = 'price_infant = ?'; $vals[] = $data['price_infant'] ?? null; }
            $vals[] = (int)$id;
            $query = "UPDATE {$this->table_name} SET " . implode(', ', $sets) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute($vals);
        } catch (PDOException $e) {
            try {
                $stmtU = $this->pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 1");
                $rowU = $stmtU->fetch(PDO::FETCH_ASSOC);
                $fallbackDriver = $rowU ? (int)$rowU['id'] : ($data['guide_user_id'] ?? null);
                $sets = ['tour_id = ?','start_date = ?','end_date = ?','guide_user_id = ?','driver_user_id = ?','max_capacity = ?'];
                $vals = [
                    $data['tour_id'] ?? null,
                    $data['start_date'] ?? '',
                    $data['end_date'] ?? '',
                    $data['guide_user_id'] ?? null,
                    $fallbackDriver ?? 0,
                    $data['max_capacity'] ?? 20,
                ];
                if ($this->hasColumn('price_adult')) { $sets[] = 'price_adult = ?'; $vals[] = $data['price_adult'] ?? null; }
                if ($this->hasColumn('price_child')) { $sets[] = 'price_child = ?'; $vals[] = $data['price_child'] ?? null; }
                if ($this->hasColumn('price_infant')) { $sets[] = 'price_infant = ?'; $vals[] = $data['price_infant'] ?? null; }
                $vals[] = (int)$id;
                $query3 = "UPDATE {$this->table_name} SET " . implode(', ', $sets) . " WHERE id = ?";
                $stmt3 = $this->pdo->prepare($query3);
                return $stmt3->execute($vals);
            } catch (PDOException $e2) {
                return false;
            }
        }
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

    // Lấy lịch trình theo HDV
    public function getByGuide($guideUserId) {
        $query = "SELECT ts.*, t.title AS tour_title
                  FROM tour_schedules ts
                  LEFT JOIN tours t ON ts.tour_id = t.id
                  WHERE ts.guide_user_id = ?
                  ORDER BY ts.start_date ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$guideUserId]);
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

    // Hàm lấy lịch tour của HDV và load view dashboard
    public function mySchedules() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?r=guide_login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $schedules = $this->getByGuide($userId); // sửa đúng

        // load view dashboard HDV
        require __DIR__ . '/../views/guides/dashboard.php';
    }
}
?>
