<?php
// D:\laragon\www\base\models\UserModel.php

class UserModel extends BaseModel {
    protected $table_name = "users";

    public function __construct() {
        parent::__construct();
    }

    public function getGuides() {
        $query = "SELECT u.*, gi.certificate_no, gi.languages, gi.experience_years 
                  FROM users u 
                  LEFT JOIN guides_info gi ON u.id = gi.user_id 
                  WHERE u.role = 'guide'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStaff() {
        $query = "SELECT * FROM users WHERE role IN ('admin', 'staff')";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function all() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([trim($email)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $data['full_name'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? '',
            password_hash($data['password'] ?? '123456', PASSWORD_DEFAULT), // Mật khẩu mặc định
            $data['role'] ?? 'staff'
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE users SET full_name = ?, email = ?, phone = ?, role = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $data['full_name'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $data['role'] ?? 'staff',
            (int)$id
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([(int)$id]);
    }
}
?>