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
        try {
            $st = $this->pdo->prepare("SHOW TABLES LIKE ?");
            $st->execute([$this->info_table]);
            if ($st->rowCount() > 0) {
                $u = $this->pdo->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'guide')");
                $u->execute([
                    $data['full_name'] ?? '',
                    $data['email'] ?? '',
                    $data['phone'] ?? '',
                    password_hash($data['password'] ?? '123456', PASSWORD_DEFAULT)
                ]);
                $uid = (int)$this->pdo->lastInsertId();
                $gi = $this->pdo->prepare("INSERT INTO {$this->info_table} (user_id, identity_no, guide_type, certificate_no, languages, experience_years, specialized_route, health_status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $gi->execute([
                    $uid,
                    $data['identity_no'] ?? null,
                    $data['guide_type'] ?? 'domestic',
                    $data['certificate_no'] ?? '',
                    $data['languages'] ?? '',
                    (int)($data['experience_years'] ?? 0),
                    $data['specialized_route'] ?? '',
                    $data['health_status'] ?? '',
                    $data['notes'] ?? ''
                ]);
                return;
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") === false) { throw $e; }
        }

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
        try {
            $st = $this->pdo->prepare("SHOW TABLES LIKE ?");
            $st->execute([$this->info_table]);
            if ($st->rowCount() > 0) {
                $row = $this->find($id);
                $uid = (int)($row['user_id'] ?? 0);
                if ($uid > 0) {
                    $up = $this->pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
                    $up->execute([
                        $data['full_name'] ?? ($row['full_name'] ?? ''),
                        $data['email'] ?? ($row['email'] ?? ''),
                        $data['phone'] ?? ($row['phone'] ?? ''),
                        $uid
                    ]);
                }
                $gi = $this->pdo->prepare("UPDATE {$this->info_table} SET identity_no = :identity_no, guide_type = :guide_type, certificate_no = :certificate_no, notes = :notes WHERE id = :id");
                $gi->execute([
                    ':identity_no' => $data['identity_no'] ?? ($row['identity_no'] ?? ''),
                    ':guide_type' => $data['guide_type'] ?? ($row['guide_type'] ?? 'domestic'),
                    ':certificate_no' => $data['certificate_no'] ?? ($row['certificate_no'] ?? ''),
                    ':notes' => $data['notes'] ?? ($row['notes'] ?? ''),
                    ':id' => (int)$id
                ]);
                return;
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") === false) { throw $e; }
        }

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
        $id = (int)$id;

        // Ưu tiên xóa trên schema mới guides_info nếu tồn tại
        try {
            $stmt = $this->pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$this->info_table]);
            if ($stmt->rowCount() > 0) {
                // guides_info không có avatar nên chỉ cần xóa bản ghi theo id
                $del = $this->pdo->prepare("DELETE FROM {$this->info_table} WHERE id = ?");
                $del->execute([$id]);
                return;
            }
        } catch (PDOException $e) {
            // Nếu lỗi khác thiếu bảng thì ném ra để dễ debug
            if (strpos($e->getMessage(), "doesn't exist") === false) {
                throw $e;
            }
        }

        // Fallback: schema cũ guides có cột avatar
        $guide = $this->find($id);
        if ($guide && !empty($guide['avatar'])) {
            $filePath = PATH_ASSETS_UPLOADS . $guide['avatar'];
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE id = ?");
        $stmt->execute([$id]);
    }
}

