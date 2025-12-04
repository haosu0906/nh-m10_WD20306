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

    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table_name} (name, service_type, contact_person, phone, email, address, description, is_active)
                    VALUES (:name, :service_type, :contact_person, :phone, :email, :address, :description, :is_active)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':name' => $data['name'] ?? '',
                ':service_type' => $data['service_type'] ?? 'other',
                ':contact_person' => $data['contact_person'] ?? '',
                ':phone' => $data['phone'] ?? '',
                ':email' => $data['email'] ?? '',
                ':address' => $data['address'] ?? '',
                ':description' => $data['description'] ?? '',
                ':is_active' => (int)($data['is_active'] ?? 1),
            ]);
        } catch (PDOException $e) {
            try {
                $sql2 = "INSERT INTO {$this->table_name} (name, service_type, contact_person, phone)
                         VALUES (:name, :service_type, :contact_person, :phone)";
                $stmt2 = $this->pdo->prepare($sql2);
                return $stmt2->execute([
                    ':name' => $data['name'] ?? '',
                    ':service_type' => $data['service_type'] ?? 'other',
                    ':contact_person' => $data['contact_person'] ?? '',
                    ':phone' => $data['phone'] ?? '',
                ]);
            } catch (PDOException $e2) {
                return false;
            }
        }
    }

    public function update($id, $data)
    {
        $id = (int)$id;
        try {
            $sql = "UPDATE {$this->table_name} SET 
                        name = :name,
                        service_type = :service_type,
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
                ':service_type' => $data['service_type'] ?? 'other',
                ':contact_person' => $data['contact_person'] ?? '',
                ':phone' => $data['phone'] ?? '',
                ':email' => $data['email'] ?? '',
                ':address' => $data['address'] ?? '',
                ':description' => $data['description'] ?? '',
                ':is_active' => (int)($data['is_active'] ?? 1),
                ':id' => $id,
            ]);
        } catch (PDOException $e) {
            try {
                $sql2 = "UPDATE {$this->table_name} SET 
                            name = :name,
                            service_type = :service_type,
                            contact_person = :contact_person,
                            phone = :phone
                        WHERE id = :id";
                $stmt2 = $this->pdo->prepare($sql2);
                return $stmt2->execute([
                    ':name' => $data['name'] ?? '',
                    ':service_type' => $data['service_type'] ?? 'other',
                    ':contact_person' => $data['contact_person'] ?? '',
                    ':phone' => $data['phone'] ?? '',
                    ':id' => $id,
                ]);
            } catch (PDOException $e2) {
                return false;
            }
        }
    }

    public function delete($id)
    {
        $id = (int)$id;
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

