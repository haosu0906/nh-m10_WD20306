<?php

class TourItineraryModel extends BaseModel
{
    protected $table_name = 'tour_itineraries';

    public function __construct()
    {
        parent::__construct();
    }

    public function getByTour($tourId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE tour_id = ? ORDER BY day_number ASC");
            $stmt->execute([(int)$tourId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return [];
            }
            throw $e;
        }
    }

    public function replace($tourId, $items)
    {
        $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE tour_id = ?")->execute([(int)$tourId]);

        if (empty($items)) {
            return;
        }

        $stmt = $this->pdo->prepare("INSERT INTO {$this->table_name} (tour_id, day_number, location, activities) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([
                (int)$tourId,
                (int)$item['day_number'],
                $item['location'],
                $item['activities']
            ]);
        }
    }
}

