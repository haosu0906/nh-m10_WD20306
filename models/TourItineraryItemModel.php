<?php

class TourItineraryItemModel extends BaseModel
{
    protected $table_name = 'tour_itinerary_items';

    public function __construct()
    {
        parent::__construct();
    }

    protected function hasColumn($name)
    {
        try {
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `{$this->table_name}` LIKE ?");
            $stmt->execute([$name]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function addBulk($tourId, $items)
    {
        if (empty($items)) return;
        $hasEnd = $this->hasColumn('end_time');
        $hasSlot = $this->hasColumn('slot');
        $hasDetailsText = true; // optimistic
        try {
            // probe details type by a safe insert into temp; if fails we'll catch below
        } catch (Exception $e) {}

        $columns = ['tour_id','day_number','activity_time','title','details','meal_plan'];
        $placeholders = '(?,?,?,?,?,?)';
        $sql = "INSERT INTO {$this->table_name} (" . implode(',', $columns) . ") VALUES {$placeholders}";
        if ($hasEnd && $hasSlot) {
            $columns = ['tour_id','day_number','activity_time','end_time','slot','title','details','meal_plan'];
            $placeholders = '(?,?,?,?,?,?,?,?)';
            $sql = "INSERT INTO {$this->table_name} (" . implode(',', $columns) . ") VALUES {$placeholders}";
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($items as $it) {
            $day = (int)($it['day_number'] ?? 1);
            $start = $it['activity_time'] ?? '08:00:00';
            $title = $it['title'] ?? '';
            $details = $it['details'] ?? '';
            $meal = $it['meal_plan'] ?? '';

            if ($hasEnd && $hasSlot) {
                $end = $it['end_time'] ?? null;
                $slot = $it['slot'] ?? null;
                $stmt->execute([(int)$tourId, $day, $start, $end, $slot, $title, $details, $meal]);
            } else {
                // fallback without end_time/slot
                $stmt->execute([(int)$tourId, $day, $start, $title, (string)$details, $meal]);
            }
        }
    }

    public function getByTour($tourId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE tour_id = ? ORDER BY day_number ASC, activity_time ASC");
            $stmt->execute([(int)$tourId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return [];
            }
            throw $e;
        }
    }

    public function deleteByTour($tourId)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE tour_id = ?");
            $stmt->execute([(int)$tourId]);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return;
            }
            throw $e;
        }
    }
}
