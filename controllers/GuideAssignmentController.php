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

        try {
            $stmtS = $this->pdo->query("SELECT s.id, s.start_date, s.end_date, t.title FROM schedules s 
                                        LEFT JOIN tours t ON s.tour_id = t.id 
                                        WHERE s.start_date >= CURDATE()
                                        ORDER BY s.start_date ASC");
            $schedules = $stmtS->fetchAll();
        } catch (PDOException $e) {
            $stmtS = $this->pdo->query("SELECT ts.id, ts.start_date, ts.end_date, t.title FROM tour_schedules ts 
                                        LEFT JOIN tours t ON ts.tour_id = t.id 
                                        WHERE ts.start_date >= CURDATE()
                                        ORDER BY ts.start_date ASC");
            $schedules = $stmtS->fetchAll();
        }

        // Lấy danh sách tours cho dropdown
        try {
            $stmtT = $this->pdo->query("SELECT id, title FROM tours ORDER BY title ASC");
            $tours = $stmtT->fetchAll();
        } catch (PDOException $e) {
            $tours = [];
        }

        // Lấy danh sách guides
        $guides = [];
        try {
            $stmtG = $this->pdo->query("SELECT id, full_name, phone, guide_type FROM guides 
                                        WHERE is_active = 1 
                                        ORDER BY full_name");
            $guides = $stmtG->fetchAll();
        } catch (PDOException $e) {
            try {
                $stmtG = $this->pdo->query("SELECT u.id, u.full_name, u.phone, COALESCE(gi.guide_type, 'internal') AS guide_type
                                            FROM users u
                                            LEFT JOIN guides_info gi ON gi.user_id = u.id
                                            WHERE u.role = 'guide' AND u.is_active = 1
                                            ORDER BY u.full_name");
                $guides = $stmtG->fetchAll();
            } catch (PDOException $e2) {
                $guides = [];
            }
        }

        require __DIR__ . '/../views/guide_assignments/create_assignment.php';
    }

    // Lưu phân công mới
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?r=guide_assignments');
            exit;
        }

        $tourId = (int)($_POST['tour_id'] ?? 0);
        $guideUserId = (int)($_POST['guide_id'] ?? 0);
        $assignmentType = $_POST['assignment_type'] ?? 'primary';
        $assignmentDate = $_POST['assignment_date'] ?? date('Y-m-d');
        $notes = $_POST['notes'] ?? '';

        if ($tourId <= 0 || $guideUserId <= 0) {
            $_SESSION['flash_error'] = 'Vui lòng chọn tour và hướng dẫn viên';
            header('Location: ' . BASE_URL . '?r=guide_assignments_create');
            exit;
        }

        // Kiểm tra trùng lặp (cùng tour, cùng HDV, cùng ngày)
        $stmt = $this->pdo->prepare("SELECT id FROM guide_assignments 
                                     WHERE tour_id = :tid AND guide_user_id = :gid AND assignment_date = :ad");
        $stmt->execute([':tid' => $tourId, ':gid' => $guideUserId, ':ad' => $assignmentDate]);
        if ($stmt->fetch()) {
            $_SESSION['flash_error'] = 'HDV này đã được phân công cho lịch này!';
            header('Location: ' . BASE_URL . '?r=guide_assignments_create');
            exit;
        }

        // Thêm phân công theo schema tour-based (fallback bỏ assignment_date nếu cột không tồn tại)
        try {
            $stmt = $this->pdo->prepare("INSERT INTO guide_assignments 
                                         (tour_id, guide_user_id, assignment_type, status, notes, assignment_date) 
                                         VALUES (:tid, :gid, :type, 'pending', :notes, :ad)");
            $result = $stmt->execute([
                ':tid' => $tourId,
                ':gid' => $guideUserId,
                ':type' => $assignmentType,
                ':notes' => $notes,
                ':ad' => $assignmentDate,
            ]);
        } catch (PDOException $e) {
            try {
                $stmt2 = $this->pdo->prepare("INSERT INTO guide_assignments 
                                              (tour_id, guide_user_id, assignment_type, status, notes) 
                                              VALUES (:tid, :gid, :type, 'pending', :notes)");
                $result = $stmt2->execute([
                    ':tid' => $tourId,
                    ':gid' => $guideUserId,
                    ':type' => $assignmentType,
                    ':notes' => $notes
                ]);
            } catch (PDOException $e2) {
                $_SESSION['flash_error'] = 'Lỗi lưu phân công: ' . $e2->getMessage();
                header('Location: ' . BASE_URL . '?r=guide_assignments_create');
                exit;
            }
        }

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

        try {
            $stmt = $this->pdo->prepare("SELECT ga.*, s.start_date, s.end_date, t.title as tour_title
                                         FROM guide_assignments ga
                                         LEFT JOIN schedules s ON ga.schedule_id = s.id
                                         LEFT JOIN tours t ON s.tour_id = t.id
                                         WHERE ga.id = :id");
            $stmt->execute([':id' => $id]);
            $assignment = $stmt->fetch();
        } catch (PDOException $e) {
            $stmt = $this->pdo->prepare("SELECT ga.*, ts.start_date, ts.end_date, t.title as tour_title
                                         FROM guide_assignments ga
                                         LEFT JOIN tour_schedules ts ON ga.schedule_id = ts.id
                                         LEFT JOIN tours t ON ts.tour_id = t.id
                                         WHERE ga.id = :id");
            $stmt->execute([':id' => $id]);
            $assignment = $stmt->fetch();
        }

        if (!$assignment) {
            $_SESSION['flash_error'] = 'Không tìm thấy phân công!';
            header('Location: ' . BASE_URL . '?r=guide_assignments');
            exit;
        }

        try {
            $stmtS = $this->pdo->query("SELECT s.id, s.start_date, s.end_date, t.title FROM schedules s 
                                        LEFT JOIN tours t ON s.tour_id = t.id 
                                        ORDER BY s.start_date ASC");
            $schedules = $stmtS->fetchAll();
        } catch (PDOException $e) {
            $stmtS = $this->pdo->query("SELECT ts.id, ts.start_date, ts.end_date, t.title FROM tour_schedules ts 
                                        LEFT JOIN tours t ON ts.tour_id = t.id 
                                        ORDER BY ts.start_date ASC");
            $schedules = $stmtS->fetchAll();
        }

        // Lấy danh sách guides
        $guides = [];
        try {
            $stmtG = $this->pdo->query("SELECT id, full_name, phone, guide_type FROM guides 
                                        WHERE is_active = 1 
                                        ORDER BY full_name");
            $guides = $stmtG->fetchAll();
        } catch (PDOException $e) {
            try {
                $stmtG = $this->pdo->query("SELECT u.id, u.full_name, u.phone, COALESCE(gi.guide_type, 'internal') AS guide_type
                                            FROM users u
                                            LEFT JOIN guides_info gi ON gi.user_id = u.id
                                            WHERE u.role = 'guide' AND u.is_active = 1
                                            ORDER BY u.full_name");
                $guides = $stmtG->fetchAll();
            } catch (PDOException $e2) {
                $guides = [];
            }
        }

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
        $tourId = (int)($_POST['tour_id'] ?? 0);
        $guideUserId = (int)($_POST['guide_id'] ?? 0);
        $assignmentType = $_POST['assignment_type'] ?? 'primary';
        $status = $_POST['status'] ?? 'assigned';
        $notes = $_POST['notes'] ?? '';
        $assignmentDate = $_POST['assignment_date'] ?? date('Y-m-d');

        // Kiểm tra trùng lặp (tour + hdv + ngày, trừ record hiện tại)
        $stmt = $this->pdo->prepare("SELECT id FROM guide_assignments 
                                     WHERE tour_id = :tid AND guide_user_id = :gid AND assignment_date = :ad AND id != :id");
        $stmt->execute([':tid' => $tourId, ':gid' => $guideUserId, ':ad' => $assignmentDate, ':id' => $id]);
        if ($stmt->fetch()) {
            $_SESSION['flash_error'] = 'HDV này đã được phân công cho lịch này!';
            header('Location: ' . BASE_URL . '?r=guide_assignments_edit&id=' . $id);
            exit;
        }

        // Cập nhật theo schema tour-based, fallback nếu thiếu cột assignment_date
        try {
            $sql = "UPDATE guide_assignments 
                    SET tour_id = :tid, guide_user_id = :gid, assignment_type = :type, 
                        status = :status, notes = :notes, assignment_date = :ad";
            if ($status === 'confirmed') {
                $sql .= ", confirmed_at = CURRENT_TIMESTAMP";
            }
            $sql .= " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':tid' => $tourId,
                ':gid' => $guideUserId,
                ':type' => $assignmentType,
                ':status' => $status,
                ':notes' => $notes,
                ':ad' => $assignmentDate,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            $sql = "UPDATE guide_assignments 
                    SET tour_id = :tid, guide_user_id = :gid, assignment_type = :type, 
                        status = :status, notes = :notes";
            if ($status === 'confirmed') {
                $sql .= ", confirmed_at = CURRENT_TIMESTAMP";
            }
            $sql .= " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':tid' => $tourId,
                ':gid' => $guideUserId,
                ':type' => $assignmentType,
                ':status' => $status,
                ':notes' => $notes,
                ':id' => $id
            ]);
        }

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

        try {
            $stmt = $this->pdo->prepare("SELECT ga.*, s.start_date, s.end_date, t.title as tour_title,
                                               u.full_name as guide_name
                                        FROM guide_assignments ga
                                        LEFT JOIN schedules s ON ga.schedule_id = s.id
                                        LEFT JOIN tours t ON s.tour_id = t.id
                                        LEFT JOIN users u ON ga.guide_id = u.id
                                        WHERE (YEAR(s.start_date) = :year AND MONTH(s.start_date) = :month)
                                           OR (YEAR(s.end_date) = :year AND MONTH(s.end_date) = :month)
                                        ORDER BY s.start_date ASC");
            $stmt->execute([':year' => $year, ':month' => $month]);
            $assignments = $stmt->fetchAll();
        } catch (PDOException $e) {
            $stmt = $this->pdo->prepare("SELECT ga.*, ts.start_date, ts.end_date, t.title as tour_title,
                                               u.full_name as guide_name
                                        FROM guide_assignments ga
                                        LEFT JOIN tour_schedules ts ON ga.schedule_id = ts.id
                                        LEFT JOIN tours t ON ts.tour_id = t.id
                                        LEFT JOIN users u ON ga.guide_id = u.id
                                        WHERE (YEAR(ts.start_date) = :year AND MONTH(ts.start_date) = :month)
                                           OR (YEAR(ts.end_date) = :year AND MONTH(ts.end_date) = :month)
                                        ORDER BY ts.start_date ASC");
            $stmt->execute([':year' => $year, ':month' => $month]);
            $assignments = $stmt->fetchAll();
        }

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
