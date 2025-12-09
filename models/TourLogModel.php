<?php

class TourLogModel extends BaseModel
{
    protected $table_name = 'tour_logs';

    public function __construct()
    {
        parent::__construct();
    }

    public function getByGuide($guideUserId)
    {
        $sql = "SELECT tl.*, t.title AS tour_title
                FROM tour_logs tl
                JOIN tours t ON tl.tour_id = t.id
                WHERE tl.guide_user_id = ?
                ORDER BY tl.log_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([(int)$guideUserId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByGuideAndTour($guideUserId, $tourId)
    {
        $sql = "SELECT tl.*, t.title AS tour_title
                FROM tour_logs tl
                JOIN tours t ON tl.tour_id = t.id
                WHERE tl.guide_user_id = ? AND tl.tour_id = ?
                ORDER BY tl.log_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([(int)$guideUserId, (int)$tourId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($guideUserId = null, $tourId = null, $from = null, $to = null)
    {
        $sql = "SELECT tl.*, t.title AS tour_title, u.full_name AS guide_name
                FROM tour_logs tl
                JOIN tours t ON tl.tour_id = t.id
                LEFT JOIN users u ON tl.guide_user_id = u.id
                WHERE 1=1";
        $params = [];
        if (!empty($guideUserId)) {
            $sql .= " AND tl.guide_user_id = :gid";
            $params[':gid'] = (int)$guideUserId;
        }
        if (!empty($tourId)) {
            $sql .= " AND tl.tour_id = :tid";
            $params[':tid'] = (int)$tourId;
        }
        if (!empty($from)) {
            $sql .= " AND tl.log_date >= :from";
            $params[':from'] = $from;
        }
        if (!empty($to)) {
            $sql .= " AND tl.log_date <= :to";
            $params[':to'] = $to;
        }
        $sql .= " ORDER BY tl.log_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByTour($tourId)
    {
        $sql = "SELECT tl.*, t.title AS tour_title
                FROM tour_logs tl
                JOIN tours t ON tl.tour_id = t.id
                WHERE tl.tour_id = ?
                ORDER BY tl.log_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([(int)$tourId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByBooking($bookingId)
    {
        $sql = "SELECT tl.*, t.title AS tour_title
                FROM tour_logs tl
                JOIN tours t ON tl.tour_id = t.id
                WHERE tl.tour_id = (SELECT tour_id FROM bookings WHERE id = ?)
                ORDER BY tl.log_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([(int)$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT tl.*, t.title AS tour_title
                FROM tour_logs tl
                JOIN tours t ON tl.tour_id = t.id
                WHERE tl.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([(int)$id]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($log) {
            $log['images'] = $this->getImagesByLogId((int)$id);
        }

        return $log;
    }

    public function create($data)
    {
        $sql = "INSERT INTO tour_logs (tour_id, guide_user_id, log_date, incident_details, customer_feedback, weather)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            $data['tour_id'] ?? null,
            $data['guide_user_id'] ?? null,
            $data['log_date'] ?? date('Y-m-d H:i:s'),
            $data['incident_details'] ?? '',
            $data['customer_feedback'] ?? '',
            $data['weather'] ?? ''
        ]);

        if (!$ok) {
            return false;
        }

        return (int)$this->pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE tour_logs
                SET log_date = ?, weather = ?, incident_details = ?, customer_feedback = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['log_date'] ?? date('Y-m-d H:i:s'),
            $data['weather'] ?? '',
            $data['incident_details'] ?? '',
            $data['customer_feedback'] ?? '',
            (int)$id
        ]);
    }

    public function delete($id)
    {
        // Xóa ảnh trước
        $this->deleteImagesByLogId((int)$id);

        $sql = "DELETE FROM tour_logs WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([(int)$id]);
    }

    public function getImagesByLogId($logId)
    {
        $sql = "SELECT * FROM tour_log_images WHERE log_id = ? ORDER BY id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([(int)$logId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addImages($logId, array $paths)
    {
        if (empty($paths)) return;

        $sql = "INSERT INTO tour_log_images (log_id, image_path) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($paths as $path) {
            $stmt->execute([(int)$logId, $path]);
        }
    }

    public function deleteImagesByLogId($logId)
    {
        $sql = "DELETE FROM tour_log_images WHERE log_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([(int)$logId]);
    }
}
