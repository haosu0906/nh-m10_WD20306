<?php

class SupplierModel extends BaseModel
{
    protected $table = 'suppliers';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name ASC";
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

    public function search($keyword = '', $service_type = '')
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (name LIKE :keyword OR contact_person LIKE :keyword OR phone LIKE :keyword OR email LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($service_type)) {
            $sql .= " AND type = :service_type";
            $params[':service_type'] = $service_type;
        }

        $sql .= " ORDER BY name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (name, type, contact_person, phone, email, address, description, is_active)
                VALUES (:name, :type, :contact_person, :phone, :email, :address, :description, :is_active)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'] ?? '',
            ':type' => $data['type'] ?? 'hotel',
            ':contact_person' => $data['contact_person'] ?? '',
            ':phone' => $data['phone'] ?? '',
            ':email' => $data['email'] ?? '',
            ':address' => $data['address'] ?? '',
            ':description' => $data['description'] ?? '',
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                name = :name,
                type = :type,
                contact_person = :contact_person,
                phone = :phone,
                email = :email,
                address = :address,
                description = :description,
                is_active = :is_active
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'] ?? '',
            ':type' => $data['type'] ?? 'hotel',
            ':contact_person' => $data['contact_person'] ?? '',
            ':phone' => $data['phone'] ?? '',
            ':email' => $data['email'] ?? '',
            ':address' => $data['address'] ?? '',
            ':description' => $data['description'] ?? '',
            ':is_active' => $data['is_active'] ?? 1,
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getSupplierStats()
    {
        $sql = "SELECT type, COUNT(*) as count FROM {$this->table} WHERE is_active = 1 GROUP BY type";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSupplierTours($supplier_id)
    {
        $sql = "SELECT DISTINCT t.* FROM tours t
                LEFT JOIN tour_expenses te ON t.id = te.tour_id
                WHERE te.supplier_id = :supplier_id
                ORDER BY t.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':supplier_id' => $supplier_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allWithMeta()
    {
        return $this->getAll();
    }

    public function findWithExpenses($id)
    {
        $supplier = $this->find($id);
        if (!$supplier) return null;

        $sql = "SELECT te.*, t.title AS tour_title
                FROM tour_expenses te
                LEFT JOIN tours t ON te.tour_id = t.id
                WHERE te.supplier_id = :supplier_id
                ORDER BY te.date_incurred DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':supplier_id' => $id]);
        $supplier['expenses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $supplier;
    }
}
