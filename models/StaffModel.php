<?php

class StaffModel extends BaseModel {
    public function __construct(){ parent::__construct(); }

    public function getAllGuides($search = '', $guide_type = '', $status = ''){
        $sql = "
            SELECT u.*, gi.identity_no, gi.guide_type, gi.certificate_no, 
                   gi.languages, gi.experience_years, gi.specialized_route, 
                   gi.health_status, gi.notes
            FROM users u
            LEFT JOIN guides_info gi ON u.id = gi.user_id
            WHERE u.role = 'guide'
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE ? OR u.phone LIKE ? OR u.email LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        if (!empty($guide_type)) {
            $sql .= " AND gi.guide_type = ?";
            $params[] = $guide_type;
        }
        
        if ($status !== '') {
            $sql .= " AND u.is_active = ?";
            $params[] = (int)$status;
        }
        
        $sql .= " ORDER BY u.id DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGuideById($id){
        $stmt = $this->pdo->prepare("
            SELECT u.*, gi.identity_no, gi.guide_type, gi.certificate_no, 
                   gi.languages, gi.experience_years, gi.specialized_route, 
                   gi.health_status, gi.notes
            FROM users u
            LEFT JOIN guides_info gi ON u.id = gi.user_id
            WHERE u.id = ? AND u.role = 'guide'
        ");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createGuide($data, $avatarPath = null){
        $this->pdo->beginTransaction();
        
        try {
            // Create user
            $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO users (full_name, email, phone, avatar, identity_no, password, role, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                trim($data['full_name'] ?? ''),
                $data['email'] ?? '',
                $data['phone'] ?? '',
                $avatarPath,
                $data['identity_no'] ?? null,
                $defaultPassword,
                'guide',
                isset($data['is_active']) ? (int)$data['is_active'] : 1
            ]);
            
            $userId = $this->pdo->lastInsertId();
            
            // Create guides_info
            $stmt = $this->pdo->prepare("
                INSERT INTO guides_info (user_id, identity_no, guide_type, certificate_no, languages, experience_years, specialized_route, health_status, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $data['identity_no'] ?? null,
                $data['guide_type'] ?? 'domestic',
                $data['certificate_no'] ?? '',
                $data['languages'] ?? '',
                $data['experience_years'] ?? 0,
                $data['specialized_route'] ?? '',
                $data['health_status'] ?? '',
                $data['notes'] ?? null
            ]);
            
            $this->pdo->commit();
            return $userId;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function updateGuide($id, $data, $avatarPath = null){
        $this->pdo->beginTransaction();
        
        try {
            // Update user
            $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, is_active = ?";
            $params = [
                trim($data['full_name'] ?? ''),
                $data['email'] ?? '',
                $data['phone'] ?? '',
                isset($data['is_active']) ? (int)$data['is_active'] : 1
            ];
            
            if ($avatarPath !== null) {
                $sql .= ", avatar = ?";
                $params[] = $avatarPath;
            }
            
            if (isset($data['identity_no'])) {
                $sql .= ", identity_no = ?";
                $params[] = $data['identity_no'];
            }
            
            $sql .= " WHERE id = ?";
            $params[] = (int)$id;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            // Update or insert guides_info
            $stmt = $this->pdo->prepare("
                SELECT id FROM guides_info WHERE user_id = ?
            ");
            $stmt->execute([(int)$id]);
            $existingInfo = $stmt->fetch();
            
            if ($existingInfo) {
                $stmt = $this->pdo->prepare("
                    UPDATE guides_info SET 
                        identity_no = ?, guide_type = ?, certificate_no = ?, 
                        languages = ?, experience_years = ?, specialized_route = ?, 
                        health_status = ?, notes = ?
                    WHERE user_id = ?
                ");
                $stmt->execute([
                    $data['identity_no'] ?? null,
                    $data['guide_type'] ?? 'domestic',
                    $data['certificate_no'] ?? '',
                    $data['languages'] ?? '',
                    $data['experience_years'] ?? 0,
                    $data['specialized_route'] ?? '',
                    $data['health_status'] ?? '',
                    $data['notes'] ?? null,
                    (int)$id
                ]);
            } else {
                $stmt = $this->pdo->prepare("
                    INSERT INTO guides_info (user_id, identity_no, guide_type, certificate_no, languages, experience_years, specialized_route, health_status, notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    (int)$id,
                    $data['identity_no'] ?? null,
                    $data['guide_type'] ?? 'domestic',
                    $data['certificate_no'] ?? '',
                    $data['languages'] ?? '',
                    $data['experience_years'] ?? 0,
                    $data['specialized_route'] ?? '',
                    $data['health_status'] ?? '',
                    $data['notes'] ?? null
                ]);
            }
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    public function all(){
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id){
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data){
        // Mặc định tạo mật khẩu "123456" đã được hash để phù hợp schema users
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("INSERT INTO users (full_name, email, phone, password, role, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            trim($data['full_name'] ?? ''),
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $defaultPassword,
            $data['role'] ?? '',
            isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
    }

    public function update($id, $data){
        $stmt = $this->pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, role = ?, is_active = ? WHERE id = ?");
        return $stmt->execute([
            trim($data['full_name'] ?? ''),
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $data['role'] ?? '',
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
            (int)$id
        ]);
    }

    public function delete($id){
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}