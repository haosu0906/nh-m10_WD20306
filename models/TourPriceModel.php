<?php

class TourPriceModel extends BaseModel
{
    protected $table_name = 'tour_prices';

    public function __construct()
    {
        parent::__construct();
    }

    public function getByTour($tourId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE tour_id = ? LIMIT 1");
            $stmt->execute([(int)$tourId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Nếu bảng chưa tồn tại, trả về null
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return null;
            }
            throw $e;
        }
    }

    protected function tableExists()
    {
        try {
            $stmt = $this->pdo->query("SHOW TABLES LIKE '" . $this->table_name . "'");
            return $stmt && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function upsert($tourId, $prices)
    {
        if (!$this->tableExists()) {
            // Bảng chưa có => bỏ qua để không làm hỏng quy trình tạo tour
            return;
        }

        $existing = $this->getByTour($tourId);
        if ($existing) {
            $stmt = $this->pdo->prepare("UPDATE {$this->table_name} 
                SET adult_price = ?, child_price = ?, infant_price = ? WHERE tour_id = ?");
            $stmt->execute([
                $prices['adult_price'],
                $prices['child_price'],
                $prices['infant_price'],
                (int)$tourId
            ]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO {$this->table_name} 
                (tour_id, adult_price, child_price, infant_price) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                (int)$tourId,
                $prices['adult_price'],
                $prices['child_price'],
                $prices['infant_price'],
            ]);
        }
    }

    public function deleteByTour($tourId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE tour_id = ?");
        $stmt->execute([(int)$tourId]);
    }
}

