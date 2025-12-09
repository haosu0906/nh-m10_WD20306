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
        $hasType = $this->hasColumn('guest_type');
        if ($hasType) {
            $sql = "INSERT INTO {$this->table} (booking_id, full_name, gender, dob, id_document_no, guest_type, is_checked_in, notes)
                    VALUES (:booking_id, :full_name, :gender, :dob, :id_document_no, :guest_type, :is_checked_in, :notes)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':booking_id' => $data['booking_id'],
                ':full_name' => $data['full_name'] ?? '',
                ':gender' => $data['gender'] ?? '',
                ':dob' => $data['dob'] ?? null,
                ':id_document_no' => $data['id_document_no'] ?? '',
                ':guest_type' => $data['guest_type'] ?? 'adult',
                ':is_checked_in' => $data['is_checked_in'] ?? 0,
                ':notes' => $data['notes'] ?? ''
            ]);
        } else {
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
    }

    public function update($id, $data)
    {
        $hasType = $this->hasColumn('guest_type');
        if ($hasType) {
            $sql = "UPDATE {$this->table} SET 
                    full_name = :full_name,
                    gender = :gender,
                    dob = :dob,
                    id_document_no = :id_document_no,
                    guest_type = :guest_type,
                    is_checked_in = :is_checked_in,
                    notes = :notes
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':full_name' => $data['full_name'] ?? '',
                ':gender' => $data['gender'] ?? '',
                ':dob' => isset($data['dob']) ? $data['dob'] : '1970-01-01',
                ':id_document_no' => $data['id_document_no'] ?? '',
                ':guest_type' => $data['guest_type'] ?? 'adult',
                ':is_checked_in' => $data['is_checked_in'] ?? 0,
                ':notes' => $data['notes'] ?? '',
                ':id' => $id
            ]);
        } else {
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
    }

    public function setCheckin($id, $checked)
    {
        $sql = "UPDATE {$this->table} SET is_checked_in = :checked, is_no_show = CASE WHEN :checked = 1 THEN 0 ELSE is_no_show END WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':checked' => (int)$checked, ':id' => (int)$id]);
    }

    public function setNoShow($id, $flag)
    {
        $sql = "UPDATE {$this->table} SET is_no_show = :flag, is_checked_in = CASE WHEN :flag = 1 THEN 0 ELSE is_checked_in END WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':flag' => (int)$flag, ':id' => (int)$id]);
    }

    public function setCheckinAllByBooking($booking_id, $checked)
    {
        $sql = "UPDATE {$this->table} SET is_checked_in = :checked, is_no_show = CASE WHEN :checked = 1 THEN 0 ELSE is_no_show END WHERE booking_id = :bid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':checked' => (int)$checked, ':bid' => (int)$booking_id]);
    }

    public function setNoShowAllByBooking($booking_id, $flag)
    {
        $sql = "UPDATE {$this->table} SET is_no_show = :flag, is_checked_in = CASE WHEN :flag = 1 THEN 0 ELSE is_checked_in END WHERE booking_id = :bid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':flag' => (int)$flag, ':bid' => (int)$booking_id]);
    }

    public function setCheckinAllByDeparture($departure_id, $checked)
    {
        $sql = "UPDATE {$this->table} bg INNER JOIN bookings b ON bg.booking_id = b.id 
                SET bg.is_checked_in = :checked, bg.is_no_show = CASE WHEN :checked = 1 THEN 0 ELSE bg.is_no_show END
                WHERE b.schedule_id = :dep";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':checked' => (int)$checked, ':dep' => (int)$departure_id]);
    }

    public function setNoShowAllByDeparture($departure_id, $flag)
    {
        $sql = "UPDATE {$this->table} bg INNER JOIN bookings b ON bg.booking_id = b.id 
                SET bg.is_no_show = :flag, bg.is_checked_in = CASE WHEN :flag = 1 THEN 0 ELSE bg.is_checked_in END
                WHERE b.schedule_id = :dep";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':flag' => (int)$flag, ':dep' => (int)$departure_id]);
    }

    public function setPendingAllByDeparture($departure_id)
    {
        $sql = "UPDATE {$this->table} bg INNER JOIN bookings b ON bg.booking_id = b.id 
                SET bg.is_checked_in = 0, bg.is_no_show = 0
                WHERE b.schedule_id = :dep";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':dep' => (int)$departure_id]);
    }

    public function addCheckinLog($booking_id, $guest_id, $stage = null, $location = null, $status = null, $reason = null, $note = null)
    {
        try {
            $hasStatus = false; $hasReason = false; $hasNote = false; $hasChangedBy = false;
            try {
                $q1 = $this->pdo->prepare("SHOW COLUMNS FROM `guest_checkins` LIKE 'status'"); $q1->execute(); $hasStatus = $q1->rowCount() > 0;
                $q2 = $this->pdo->prepare("SHOW COLUMNS FROM `guest_checkins` LIKE 'reason'"); $q2->execute(); $hasReason = $q2->rowCount() > 0;
                $q3 = $this->pdo->prepare("SHOW COLUMNS FROM `guest_checkins` LIKE 'note'"); $q3->execute(); $hasNote = $q3->rowCount() > 0;
                $q4 = $this->pdo->prepare("SHOW COLUMNS FROM `guest_checkins` LIKE 'changed_by_user_id'"); $q4->execute(); $hasChangedBy = $q4->rowCount() > 0;
            } catch (Exception $e2) {}
            $cols = ['booking_id','guest_id','stage','location'];
            $vals = [(int)$booking_id,(int)$guest_id,(string)$stage,(string)$location];
            if ($hasStatus) { $cols[] = 'status'; $vals[] = (string)$status; }
            if ($hasReason) { $cols[] = 'reason'; $vals[] = (string)$reason; }
            if ($hasNote) { $cols[] = 'note'; $vals[] = (string)$note; }
            if ($hasChangedBy) { $cols[] = 'changed_by_user_id'; $vals[] = (int)($_SESSION['user_id'] ?? 0); }
            $cols[] = 'checked_at';
            $placeholders = rtrim(str_repeat('?,', count($cols)), ',');
            $sql = "INSERT INTO guest_checkins (" . implode(',', $cols) . ") VALUES (" . $placeholders . ")";
            $stmt = $this->pdo->prepare($sql);
            $vals[] = date('Y-m-d H:i:s');
            $stmt->execute($vals);
        } catch (Exception $e) {
            // table may not exist; safely ignore
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getCheckinLogs($guest_id)
    {
        try {
            $sql = "SELECT gc.*, u.full_name AS changed_by_name
                    FROM guest_checkins gc
                    LEFT JOIN users u ON u.id = gc.changed_by_user_id
                    WHERE gc.guest_id = :gid
                    ORDER BY gc.checked_at DESC";
            $st = $this->pdo->prepare($sql);
            $st->execute([':gid' => (int)$guest_id]);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    protected function hasColumn($name)
    {
        try {
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `{$this->table}` LIKE ?");
            $stmt->execute([$name]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getTypeCounts($booking_id)
    {
        if ($this->hasColumn('guest_type')) {
            $sql = "SELECT guest_type, COUNT(*) AS c FROM {$this->table} WHERE booking_id = :bid GROUP BY guest_type";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':bid' => (int)$booking_id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $res = ['adult'=>0,'child'=>0,'infant'=>0];
            foreach ($rows as $r) { $k = $r['guest_type']; if (isset($res[$k])) { $res[$k] = (int)$r['c']; } }
            return $res;
        } else {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE booking_id = :bid";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':bid' => (int)$booking_id]);
            $adult = (int)$stmt->fetchColumn();
            return ['adult'=>$adult,'child'=>0,'infant'=>0];
        }
    }
}
