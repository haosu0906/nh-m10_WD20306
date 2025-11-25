<?php

class GuideModel extends BaseModel
{
    protected $table_name = 'guides';
    protected $info_table  = 'guides_info';

    public function __construct()
    {
        parent::__construct();
    }

    public function search($keyword = '', $type = '')
    {
        // Ưu tiên đọc từ guides_info JOIN users (schema mới)
        try {
            $sql = "SELECT gi.*, u.full_name, u.email, u.phone, u.created_at
                    FROM {$this->info_table} gi
                    LEFT JOIN users u ON gi.user_id = u.id
                    WHERE 1=1";
            $params = [];

            if ($keyword !== '') {
                $sql .= " AND (u.full_name LIKE :kw OR u.phone LIKE :kw OR u.email LIKE :kw)";
                $params[':kw'] = '%' . $keyword . '%';
            }

            if ($type !== '' && in_array($type, ['domestic', 'international'], true)) {
                $sql .= " AND gi.guide_type = :type";
                $params[':type'] = $type;
            }

            $sql .= " ORDER BY gi.id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Nếu bảng guides_info chưa tồn tại, fallback về bảng guides cũ
            if (strpos($e->getMessage(), $this->info_table) === false) {
                throw $e;
            }

            try {
                $sql = "SELECT * FROM {$this->table_name} WHERE 1=1";
                $params = [];

                if ($keyword !== '') {
                    $sql .= " AND (full_name LIKE :kw OR phone LIKE :kw OR email LIKE :kw)";
                    $params[':kw'] = '%' . $keyword . '%';
                }

                if ($type !== '' && in_array($type, ['domestic', 'international'], true)) {
                    $sql .= " AND guide_type = :type";
                    $params[':type'] = $type;
                }

                $sql .= " ORDER BY created_at DESC";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                if (strpos($e2->getMessage(), "doesn't exist") !== false) {
                    return [];
                }
                throw $e2;
            }
        }
    }

    public function find($id)
    {
        // Ưu tiên đọc từ guides_info JOIN users
        try {
            $stmt = $this->pdo->prepare(
                "SELECT gi.*, u.full_name, u.email, u.phone, u.created_at
                 FROM {$this->info_table} gi
                 LEFT JOIN users u ON gi.user_id = u.id
                 WHERE gi.id = ?"
            );
            $stmt->execute([(int)$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $row;
            }
        } catch (PDOException $e) {
            // Nếu lỗi do guides_info không tồn tại, fallback bên dưới
            if (strpos($e->getMessage(), $this->info_table) === false) {
                throw $e;
            }
        }

        // Fallback: bảng guides cũ
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table_name}
            (full_name, phone, email, identity_no, certificate_no, guide_type, avatar, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['full_name'] ?? '',
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['identity_no'] ?? '',
            $data['certificate_no'] ?? '',
            $data['guide_type'] ?? 'domestic',
            $data['avatar'] ?? null,
            $data['notes'] ?? '',
        ]);
    }

    public function update($id, $data)
    {
        $fields = [
            'full_name' => $data['full_name'] ?? '',
            'phone' => $data['phone'] ?? '',
            'email' => $data['email'] ?? '',
            'identity_no' => $data['identity_no'] ?? '',
            'certificate_no' => $data['certificate_no'] ?? '',
            'guide_type' => $data['guide_type'] ?? 'domestic',
            'notes' => $data['notes'] ?? '',
        ];

        $sql = "UPDATE {$this->table_name} SET 
            full_name = :full_name,
            phone = :phone,
            email = :email,
            identity_no = :identity_no,
            certificate_no = :certificate_no,
            guide_type = :guide_type,
            notes = :notes";

        if (!empty($data['avatar'])) {
            $sql .= ", avatar = :avatar";
            $fields['avatar'] = $data['avatar'];
        }

        $sql .= " WHERE id = :id";
        $fields['id'] = (int)$id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($fields);
    }

    public function delete($id)
    {
        $guide = $this->find($id);
        if ($guide && !empty($guide['avatar'])) {
            $filePath = PATH_ASSETS_UPLOADS . $guide['avatar'];
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE id = ?");
        $stmt->execute([(int)$id]);
    }
}

