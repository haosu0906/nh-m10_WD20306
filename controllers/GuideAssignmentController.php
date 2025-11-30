<?php

require_once __DIR__ . '/../models/BaseModel.php';

class GuideAssignmentController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new BaseModel())->getConnection();
    }

    // Danh sách phân công HDV
    public function index()
    {
        $tourId = (int)($_GET['tour_id'] ?? 0);
        $guideId = (int)($_GET['guide_id'] ?? 0);
        $status = $_GET['status'] ?? '';

        $sql = "SELECT ga.*, 
                       t.title as tour_title,
                       u.full_name as guide_name, 
                       u.email as guide_email,
                       u.phone as guide_phone,
                       ga.assignment_type, 
                       ga.status as assignment_status
                FROM guide_assignments ga
                LEFT JOIN tours t ON ga.tour_id = t.id
                LEFT JOIN users u ON ga.guide_user_id = u.id
                WHERE 1=1";

        $params = [];
        if ($tourId > 0) {
            $sql .= " AND ga.tour_id = :tid";
            $params[':tid'] = $tourId;
        }
        if ($guideId > 0) {
            $sql .= " AND ga.guide_user_id = :gid";
            $params[':gid'] = $guideId;
        }
        if (!empty($status)) {
            $sql .= " AND ga.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY ga.created_at DESC, ga.assignment_date ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $assignments = $stmt->fetchAll();

        // Lấy danh sách tours cho bộ lọc
        $stmtT = $this->pdo->query("SELECT id, title FROM tours ORDER BY title ASC");
        $tours = $stmtT->fetchAll();

        // Lấy danh sách guides cho bộ lọc
        $stmtG = $this->pdo->query("SELECT u.id, u.full_name FROM users u 
                                      LEFT JOIN guides_info gi ON u.id = gi.user_id 
                                      WHERE gi.user_id IS NOT NULL 
                                      ORDER BY u.full_name ASC");
        $guides = $stmtG->fetchAll();

        require __DIR__ . '/../views/guide_assignments/list_assignments.php';
    }

    // Form tạo phân công mới
    public function create()
    {
        $scheduleId = (int)($_GET['schedule_id'] ?? 0);

        // Lấy danh sách schedules
        $stmtS = $this->pdo->query("SELECT s.id, s.start_date, s.end_date, t.title FROM schedules s 
                                    LEFT JOIN tours t ON s.tour_id = t.id 
                                    WHERE s.start_date >= CURDATE()
                                    ORDER BY s.start_date ASC");
        $schedules = $stmtS->fetchAll();

        // Lấy danh sách guides
        $stmtG = $this->pdo->query("SELECT id, full_name, phone, guide_type FROM guides 
                                    WHERE is_active = 1 
                                    ORDER BY full_name");
        $guides = $stmtG->fetchAll();

        require __DIR__ . '/../views/guide_assignments/create_assignment.php';
    }

    // Lưu phân công mới
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?r=guide_assignments');
            exit;
        }

        $scheduleId = (int)($_POST['schedule_id'] ?? 0);
        $guideId = (int)($_POST['guide_id'] ?? 0);
        $assignmentType = $_POST['assignment_type'] ?? 'primary';
        $notes = $_POST['notes'] ?? '';

        // Kiểm tra trùng lặp
        $stmt = $this->pdo->prepare("SELECT id FROM guide_assignments 
                                     WHERE schedule_id = :sid AND guide_id = :gid");
        $stmt->execute([':sid' => $scheduleId, ':gid' => $guideId]);
        if ($stmt->fetch()) {
            $_SESSION['flash_error'] = 'HDV này đã được phân công cho lịch này!';
            header('Location: ' . BASE_URL . '?r=guide_assignments_create');
            exit;
        }

        // Thêm phân công
        $stmt = $this->pdo->prepare("INSERT INTO guide_assignments 
                                     (schedule_id, guide_id, assignment_type, status, notes) 
                                     VALUES (:sid, :gid, :type, 'assigned', :notes)");
        $result = $stmt->execute([
            ':sid' => $scheduleId,
            ':gid' => $guideId,
            ':type' => $assignmentType,
            ':notes' => $notes
        ]);

        if ($result) {
            $_SESSION['flash_success'] = 'Phân công HDV thành công!';
        } else {
            $_SESSION['flash_error'] = 'Có lỗi xảy ra!';
        }

        header('Location: ' . BASE_URL . '?r=guide_assignments');
        exit;
    }

    // Sửa phân công
    public function edit($id)
    {
        $id = (int)$id;

        // Lấy thông tin phân công
        $stmt = $this->pdo->prepare("SELECT ga.*, s.start_date, s.end_date, t.title as tour_title
                                     FROM guide_assignments ga
                                     LEFT JOIN schedules s ON ga.schedule_id = s.id
                                     LEFT JOIN tours t ON s.tour_id = t.id
                                     WHERE ga.id = :id");
        $stmt->execute([':id' => $id]);
        $assignment = $stmt->fetch();

        if (!$assignment) {
            $_SESSION['flash_error'] = 'Không tìm thấy phân công!';
            header('Location: ' . BASE_URL . '?r=guide_assignments');
            exit;
        }

        // Lấy danh sách schedules
        $stmtS = $this->pdo->query("SELECT s.id, s.start_date, s.end_date, t.title FROM schedules s 
                                    LEFT JOIN tours t ON s.tour_id = t.id 
                                    ORDER BY s.start_date ASC");
        $schedules = $stmtS->fetchAll();

        // Lấy danh sách guides
        $stmtG = $this->pdo->query("SELECT id, full_name, phone, guide_type FROM guides 
                                    WHERE is_active = 1 
                                    ORDER BY full_name");
        $guides = $stmtG->fetchAll();

        require __DIR__ . '/../views/guide_assignments/edit_assignment.php';
    }

    // Cập nhật phân công
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?r=guide_assignments');
            exit;
        }

        $id = (int)$id;
        $scheduleId = (int)($_POST['schedule_id'] ?? 0);
        $guideId = (int)($_POST['guide_id'] ?? 0);
        $assignmentType = $_POST['assignment_type'] ?? 'primary';
        $status = $_POST['status'] ?? 'assigned';
        $notes = $_POST['notes'] ?? '';

        // Kiểm tra trùng lặp (trừ record hiện tại)
        $stmt = $this->pdo->prepare("SELECT id FROM guide_assignments 
                                     WHERE schedule_id = :sid AND guide_id = :gid AND id != :id");
        $stmt->execute([':sid' => $scheduleId, ':gid' => $guideId, ':id' => $id]);
        if ($stmt->fetch()) {
            $_SESSION['flash_error'] = 'HDV này đã được phân công cho lịch này!';
            header('Location: ' . BASE_URL . '?r=guide_assignments_edit&id=' . $id);
            exit;
        }

        // Cập nhật
        $sql = "UPDATE guide_assignments 
                SET schedule_id = :sid, guide_id = :gid, assignment_type = :type, 
                    status = :status, notes = :notes";
        
        // Nếu status là confirmed, cập nhật confirmed_at
        if ($status === 'confirmed') {
            $sql .= ", confirmed_at = CURRENT_TIMESTAMP";
        }
        
        $sql .= " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':sid' => $scheduleId,
            ':gid' => $guideId,
            ':type' => $assignmentType,
            ':status' => $status,
            ':notes' => $notes,
            ':id' => $id
        ]);

        if ($result) {
            $_SESSION['flash_success'] = 'Cập nhật phân công thành công!';
        } else {
            $_SESSION['flash_error'] = 'Có lỗi xảy ra!';
        }

        header('Location: ' . BASE_URL . '?r=guide_assignments');
        exit;
    }

    // Xóa phân công
    public function delete($id)
    {
        $id = (int)$id;

        $stmt = $this->pdo->prepare("DELETE FROM guide_assignments WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);

        if ($result) {
            $_SESSION['flash_success'] = 'Xóa phân công thành công!';
        } else {
            $_SESSION['flash_error'] = 'Có lỗi xảy ra!';
        }

        header('Location: ' . BASE_URL . '?r=guide_assignments');
        exit;
    }

    // Calendar view
    public function calendar()
    {
        $month = (int)($_GET['month'] ?? date('n'));
        $year = (int)($_GET['year'] ?? date('Y'));

        // Lấy các phân công trong tháng
        $stmt = $this->pdo->prepare("SELECT ga.*, s.start_date, s.end_date, t.title as tour_title,
                                           g.full_name as guide_name
                                    FROM guide_assignments ga
                                    LEFT JOIN schedules s ON ga.schedule_id = s.id
                                    LEFT JOIN tours t ON s.tour_id = t.id
                                    LEFT JOIN guides g ON ga.guide_id = g.id
                                    WHERE (YEAR(s.start_date) = :year AND MONTH(s.start_date) = :month)
                                       OR (YEAR(s.end_date) = :year AND MONTH(s.end_date) = :month)
                                    ORDER BY s.start_date ASC");
        $stmt->execute([':year' => $year, ':month' => $month]);
        $assignments = $stmt->fetchAll();

        require __DIR__ . '/../views/guide_assignments/calendar.php';
    }

    // Xem chi tiết phân công
    public function show()
    {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            header('Location: ' . BASE_URL . '?r=guide_assignments');
            exit;
        }

        // Lấy chi tiết phân công
        $stmt = $this->pdo->prepare("SELECT ga.*, 
                                           t.title as tour_title,
                                           u.full_name as guide_name, 
                                           u.email as guide_email,
                                           u.phone as guide_phone,
                                           gi.experience_years,
                                           gi.specialized_route,
                                           gi.health_status
                                    FROM guide_assignments ga
                                    LEFT JOIN tours t ON ga.tour_id = t.id
                                    LEFT JOIN users u ON ga.guide_user_id = u.id
                                    LEFT JOIN guides_info gi ON u.id = gi.user_id
                                    WHERE ga.id = :id");
        $stmt->execute([':id' => $id]);
        $assignment = $stmt->fetch();

        if (!$assignment) {
            header('Location: ' . BASE_URL . '?r=guide_assignments');
            exit;
        }

        require __DIR__ . '/../views/guide_assignments/show.php';
    }
}
