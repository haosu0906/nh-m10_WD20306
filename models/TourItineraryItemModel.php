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
        $hasStartCol = $this->hasColumn('start_time');
        $hasActivityCol = $this->hasColumn('activity_time');
        $hasMeal = $this->hasColumn('meal_plan');

        $columns = ['tour_id','day_number'];
        if ($hasActivityCol) { $columns[] = 'activity_time'; }
        elseif ($hasStartCol) { $columns[] = 'start_time'; }
        $columns[] = 'title'; $columns[] = 'details';
        if ($hasMeal) { $columns[] = 'meal_plan'; }
        $placeholders = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';
        $sql = "INSERT INTO {$this->table_name} (" . implode(',', $columns) . ") VALUES {$placeholders}";
        if ($hasEnd && $hasSlot) {
            $columns = ['tour_id','day_number'];
            if ($hasActivityCol) { $columns[] = 'activity_time'; }
            elseif ($hasStartCol) { $columns[] = 'start_time'; }
            $columns[] = 'end_time'; $columns[] = 'slot'; $columns[] = 'title'; $columns[] = 'details';
            if ($hasMeal) { $columns[] = 'meal_plan'; }
            $placeholders = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';
            $sql = "INSERT INTO {$this->table_name} (" . implode(',', $columns) . ") VALUES {$placeholders}";
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($items as $it) {
            $day = (int)($it['day_number'] ?? 1);
            $start = $it['activity_time'] ?? ($it['start_time'] ?? '08:00:00');
            $title = $it['title'] ?? '';
            $details = $it['details'] ?? '';
            $meal = $it['meal_plan'] ?? '';

            if ($hasEnd && $hasSlot) {
                $end = $it['end_time'] ?? null;
                $slot = $it['slot'] ?? null;
                $vals = [(int)$tourId, $day, $start, $end, $slot, $title, $details];
                if ($hasMeal) { $vals[] = $meal; }
                $stmt->execute($vals);
            } else {
                $vals = [(int)$tourId, $day, $start, $title, (string)$details];
                if ($hasMeal) { $vals[] = $meal; }
                $stmt->execute($vals);
            }
        }
    }

    public function getByTour($tourId)
    {
        try {
            $orderCol = $this->hasColumn('activity_time') ? 'activity_time' : ($this->hasColumn('start_time') ? 'start_time' : 'id');
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE tour_id = ? ORDER BY day_number ASC, {$orderCol} ASC");
            $stmt->execute([(int)$tourId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return [];
            }
            throw $e;
        }
    }

    public function updateItem($id, $tourId, $data)
    {
        $hasStartCol = $this->hasColumn('start_time');
        $hasActivityCol = $this->hasColumn('activity_time');
        $hasEnd = $this->hasColumn('end_time');
        $hasSlot = $this->hasColumn('slot');
        $hasMeal = $this->hasColumn('meal_plan');
        $sets = ['day_number = ?','title = ?','details = ?'];
        $vals = [
            (int)($data['day_number'] ?? 1),
            (string)($data['title'] ?? ''),
            (string)($data['details'] ?? ''),
        ];
        if ($hasMeal) { $sets[] = 'meal_plan = ?'; $vals[] = (string)($data['meal_plan'] ?? ''); }
        $start = (string)($data['activity_time'] ?? ($data['start_time'] ?? '08:00'));
        if (strlen($start) === 5) { $start .= ':00'; }
        if ($hasActivityCol) { $sets[] = 'activity_time = ?'; $vals[] = $start; }
        elseif ($hasStartCol) { $sets[] = 'start_time = ?'; $vals[] = $start; }
        if ($hasEnd) { $end = (string)($data['end_time'] ?? ''); if ($end!=='' && strlen($end)===5) { $end.=':00'; } $sets[] = 'end_time = ?'; $vals[] = ($end!=='' ? $end : null); }
        if ($hasSlot) { $sets[] = 'slot = ?'; $vals[] = (string)($data['slot'] ?? ''); }
        $vals[] = (int)$id; $vals[] = (int)$tourId;
        $sql = "UPDATE {$this->table_name} SET " . implode(', ', $sets) . " WHERE id = ? AND tour_id = ?";
        $st = $this->pdo->prepare($sql);
        return $st->execute($vals);
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
