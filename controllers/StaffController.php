<?php

require_once __DIR__ . '/../models/StaffModel.php';
require_once __DIR__ . '/../assets/configs/env.php';

class StaffController {
    protected $model;
    public function __construct(){ $this->model = new StaffModel(); }

    public function index(){
        try {
            // Get staff data from users table (excluding guides)
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $stmt = $pdo->prepare("SELECT * FROM users WHERE role != 'guide' ORDER BY created_at DESC");
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Log error if no staff
            if(empty($items)) {
                error_log("No staff found in users table");
            }
            
        } catch(Exception $e) {
            error_log("Error in StaffController::index: " . $e->getMessage());
            $items = [];
        }
        
        require __DIR__ . '/../views/staff/index.php';
    }

    public function create(){
        $staff = null;
        require __DIR__ . '/../views/staff/form.php';
    }

    public function store(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?r=staff');
            exit;
        }

        try {
            // Handle avatar upload
            $avatarPath = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'assets/images/staff/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = time() . '_' . basename($_FILES['avatar']['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                    $avatarPath = $targetPath;
                }
            }
            
            // Create staff in users table
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, role, avatar, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $_POST['username'],
                $_POST['email'], 
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                $_POST['full_name'],
                $_POST['phone'] ?? null,
                $_POST['role'] ?? 'staff',
                $avatarPath,
                $_POST['status'] ?? 1
            ]);
            
            header('Location: ' . BASE_URL . '?r=staff&created=1');
            exit;
            
        } catch(Exception $e) {
            error_log("Error creating staff: " . $e->getMessage());
            header('Location: ' . BASE_URL . '?r=staff_create&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function edit($id){
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role != 'guide'");
            $stmt->execute([$id]);
            $staff = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$staff) {
                header('Location: ' . BASE_URL . '?r=staff');
                exit;
            }
            require __DIR__ . '/../views/staff/form.php';
        } catch(Exception $e) {
            error_log("Error loading staff for edit: " . $e->getMessage());
            header('Location: ' . BASE_URL . '?r=staff');
            exit;
        }
    }

    public function update($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?r=staff');
            exit;
        }

        try {
            // Handle avatar upload
            $avatarPath = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'assets/images/staff/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = time() . '_' . basename($_FILES['avatar']['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                    $avatarPath = $targetPath;
                }
            }
            
            // Update staff in users table
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
            
            // Build update query
            $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, phone = ?, role = ?, status = ?";
            $params = [
                $_POST['username'],
                $_POST['email'],
                $_POST['full_name'],
                $_POST['phone'] ?? null,
                $_POST['role'] ?? 'staff',
                $_POST['status'] ?? 1
            ];
            
            // Add password if provided
            if (!empty($_POST['password'])) {
                $sql .= ", password = ?";
                $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            // Add avatar if uploaded
            if ($avatarPath) {
                $sql .= ", avatar = ?";
                $params[] = $avatarPath;
            }
            
            $sql .= " WHERE id = ? AND role != 'guide'";
            $params[] = $id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            header('Location: ' . BASE_URL . '?r=staff&updated=1');
            exit;
            
        } catch(Exception $e) {
            error_log("Error updating staff: " . $e->getMessage());
            header('Location: ' . BASE_URL . '?r=staff_edit&id=' . $id . '&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function delete($id){
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'guide'");
            $stmt->execute([$id]);
            
            header('Location: ' . BASE_URL . '?r=staff&deleted=1');
            exit;
        } catch(Exception $e) {
            error_log("Error deleting staff: " . $e->getMessage());
            header('Location: ' . BASE_URL . '?r=staff&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function fixData(){
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
            
            // Xóa data cũ trùng lặp
            $pdo->exec('DELETE FROM guides_info WHERE user_id IN (12, 13, 16)');
            
            // Thêm data cho user 12
            $stmt = $pdo->prepare("INSERT INTO guides_info (user_id, identity_no, guide_type, certificate_no, languages, experience_years, specialized_route, health_status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)" );
            $stmt->execute([12, '001234567890', 'domestic', 'HDV-2024012', 'Tiếng Việt, English', 3, 'Miền Trung Việt Nam, Đà Nẵng', 'Sức khỏe tốt', 'HDV trẻ, năng động']);
            
            // Thêm data cho user 13
            $stmt->execute([13, '001234567891', 'international', 'HDV-2024013', 'Tiếng Việt, English, 中文', 7, 'Du lịch Đông Nam Á, Châu Á', 'Sức khỏe tốt, có thể đi tour dài', 'HDV kinh nghiệm quốc tế']);
            
            // Thêm data cho user 16
            $stmt->execute([16, '001234567892', 'domestic', 'HDV-2024014', 'Tiếng Việt, English', 4, 'Miền Nam Việt Nam, TP.HCM', 'Sức khỏe tốt', 'HDV miền Nam chuyên nghiệp']);
            
            // Redirect back with success message
            header('Location: ' . BASE_URL . '?r=staff&fixed=1');
            exit;
            
        } catch(Exception $e) {
            // Redirect back with error message
            header('Location: ' . BASE_URL . '?r=staff&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function allUsers(){
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            error_log("Error in StaffController::allUsers: " . $e->getMessage());
            $items = [];
        }
        require __DIR__ . '/../views/staff/index_basic.php';
    }
}
