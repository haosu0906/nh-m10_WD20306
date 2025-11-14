
<?php
require_once __DIR__ . '/../assets/configs/db.php';

class StaffModel {
    protected $pdo;
    public function __construct(){ $this->pdo = DB::get(); }

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
        $stmt = $this->pdo->prepare("INSERT INTO users (full_name, email, phone, role, is_active) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            trim($data['full_name'] ?? ''),
            $data['email'] ?? '',
            $data['phone'] ?? '',
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