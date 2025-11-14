<?php

class TourSupplierModel extends BaseModel
{
    protected $table_name = 'tour_suppliers';

    public function __construct()
    {
        parent::__construct();
    }

    public function all()
    {
        try {
            $stmt = $this->pdo->query("SELECT id, name FROM {$this->table_name} ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Nếu bảng chưa tồn tại, trả về mảng rỗng
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return [];
            }
            throw $e;
        }
    }
}

