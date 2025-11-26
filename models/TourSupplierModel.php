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
        return $this->allWithMeta();
    }

    public function allWithMeta()
    {
        try {
            $stmt = $this->pdo->query("SELECT id, name, contact_person, service_type, phone FROM {$this->table_name} ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return [];
            }
            throw $e;
        }
    }

    public function findWithExpenses($id)
    {
        $id = (int)$id;
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE id = ?");
            $stmt->execute([$id]);
            $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$supplier) return null;

            // Lấy các chi phí/dịch vụ từ tour_expenses liên quan đến NCC này
            $sql = "SELECT te.*, t.title AS tour_title
                    FROM tour_expenses te
                    LEFT JOIN tours t ON te.tour_id = t.id
                    WHERE te.supplier_id = ?
                    ORDER BY te.date_incurred DESC";
            $q = $this->pdo->prepare($sql);
            $q->execute([$id]);
            $supplier['expenses'] = $q->fetchAll(PDO::FETCH_ASSOC);

            return $supplier;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return null;
            }
            throw $e;
        }
    }
}

