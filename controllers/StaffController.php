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
            $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Log error if no staff
            if(empty($staff)) {
                error_log("No staff found in users table");
            }
            
        } catch(Exception $e) {
            error_log("Error in StaffController::index: " . $e->getMessage());
            $staff = [];
        }
        
        // Make variables available to view
        $data = [
            'staff' => $staff,
        ];
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
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            flash_set('danger', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=staff_create');
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
            
            flash_set('success', 'Đã tạo nhân sự');
            header('Location: ' . BASE_URL . '?r=staff');
            exit;
            
        } catch(Exception $e) {
            error_log("Error creating staff: " . $e->getMessage());
            flash_set('danger', 'Tạo nhân sự thất bại: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '?r=staff_create');
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
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            flash_set('danger', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=staff_edit&id=' . $id);
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
            
            flash_set('success', 'Đã cập nhật nhân sự');
            header('Location: ' . BASE_URL . '?r=staff');
            exit;
            
        } catch(Exception $e) {
            error_log("Error updating staff: " . $e->getMessage());
            flash_set('danger', 'Cập nhật nhân sự thất bại: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '?r=staff_edit&id=' . $id);
            exit;
        }
    }

    public function delete($id){
        if (!csrf_validate($_GET['csrf_token'] ?? '')) {
            flash_set('danger', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=staff');
            exit;
        }
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'guide'");
            $stmt->execute([$id]);
            
            flash_set('warning', 'Đã xóa nhân sự');
            header('Location: ' . BASE_URL . '?r=staff');
            exit;
        } catch(Exception $e) {
            error_log("Error deleting staff: " . $e->getMessage());
            flash_set('danger', 'Xóa nhân sự thất bại: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '?r=staff');
            exit;
        }
    }

    public function fixData(){
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
            
            // Xóa data cũ trùng lặp
            $pdo->exec('DELETE FROM guides_info WHERE user_id IN (12, 13, 16)');
            
            // Thêm data cho user 12
            $stmt = $pdo->prepare("INSERT INTO guides_info (user_id, identity_no, guide_type, certificate_no, languages, experience_years, specialized_route, health_status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([12, '001234567890', 'domestic', 'HDV-2024012', 'Tiếng Việt, English', 3, 'Miền Trung Việt Nam, Đà Nẵng', 'Sức khỏe tốt', 'HDV trẻ, năng động']);
            
            // Thêm data cho user 13
            $stmt->execute([13, '001234567891', 'international', 'HDV-2024013', 'Tiếng Việt, English, 中文', 7, 'Du lịch Đông Nam Á, Châu Á', 'Sức khỏe tốt, có thể đi tour dài', 'HDV kinh nghiệm quốc tế']);
            
            // Thêm data cho user 16
            $stmt->execute([16, '001234567892', 'domestic', 'HDV-2024014', 'Tiếng Việt, English', 4, 'Miền Nam Việt Nam, TP.HCM', 'Sức khỏe tốt', 'HDV miền Nam chuyên nghiệp']);
            
            flash_set('info', 'Đã sửa dữ liệu');
            header('Location: ' . BASE_URL . '?r=staff');
            exit;
            
        } catch(Exception $e) {
            flash_set('danger', 'Sửa dữ liệu thất bại: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '?r=staff');
            exit;
        }
    }
}
