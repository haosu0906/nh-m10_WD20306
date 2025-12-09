<?php

require_once __DIR__ . '/../models/BaseModel.php';

class GuideScheduleController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new BaseModel())->getConnection();
    }

    // Calendar view - Lịch làm việc của HDV
    public function index()
    {
        $guideId = (int)($_GET['guide_id'] ?? 0);
        $month = (int)($_GET['month'] ?? date('n'));
        $year = (int)($_GET['year'] ?? date('Y'));

        // Nếu không có guide_id, lấy HDV đầu tiên
        if ($guideId === 0) {
            $stmt = $this->pdo->query("SELECT u.id FROM users u 
                                      LEFT JOIN guides_info gi ON u.id = gi.user_id 
                                      WHERE gi.user_id IS NOT NULL 
                                      LIMIT 1");
            $guide = $stmt->fetch();
            $guideId = $guide ? (int)$guide['id'] : 0;
        }

        $schedules = [];
        if ($guideId > 0) {
            // Lấy lịch làm việc trong tháng
            $stmt = $this->pdo->prepare("SELECT gs.*, ga.assignment_type, t.title as tour_title
                                         FROM guide_schedules gs
                                         LEFT JOIN guide_assignments ga ON gs.guide_user_id = ga.guide_user_id 
                                            AND gs.schedule_date = ga.assignment_date
                                         LEFT JOIN tours t ON ga.tour_id = t.id
                                         WHERE gs.guide_user_id = :gid 
                                            AND YEAR(gs.schedule_date) = :year 
                                            AND MONTH(gs.schedule_date) = :month
                                         ORDER BY gs.schedule_date ASC");
            $stmt->execute([':gid' => $guideId, ':year' => $year, ':month' => $month]);
            $schedules = $stmt->fetchAll();
        }

        // Lấy danh sách guides
        $stmtG = $this->pdo->query("SELECT u.id, u.full_name FROM users u 
                                      LEFT JOIN guides_info gi ON u.id = gi.user_id 
                                      WHERE gi.user_id IS NOT NULL 
                                      ORDER BY u.full_name ASC");
        $guides = $stmtG->fetchAll();

        // Lấy thông tin guide hiện tại
        $guideInfo = null;
        if ($guideId > 0) {
            $stmt = $this->pdo->prepare("SELECT u.*, gi.* FROM users u 
                                        LEFT JOIN guides_info gi ON u.id = gi.user_id 
                                        WHERE u.id = :id");
            $stmt->execute([':id' => $guideId]);
            $guideInfo = $stmt->fetch();
        }

        require __DIR__ . '/../views/guide_schedules/calendar.php';
    }

    // Tạo lịch làm việc mới
    public function create()
    {
        $guideId = (int)($_GET['guide_id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');

        // Lấy danh sách guides
        $stmtG = $this->pdo->query("SELECT u.id, u.full_name FROM users u 
                                      LEFT JOIN guides_info gi ON u.id = gi.user_id 
                                      WHERE gi.user_id IS NOT NULL 
                                      ORDER BY u.full_name ASC");
        $guides = $stmtG->fetchAll();

        require __DIR__ . '/../views/guide_schedules/create_schedule.php';
    }

    // Lưu lịch làm việc
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?r=guide_schedules');
            exit;
        }

        $guideId = (int)($_POST['guide_id'] ?? 0);
        $date = $_POST['date'] ?? '';
        $status = $_POST['status'] ?? 'available';
        $startTime = $_POST['start_time'] ?? null;
        $endTime = $_POST['end_time'] ?? null;
        $location = $_POST['location'] ?? '';
        $notes = $_POST['notes'] ?? '';

        // Kiểm tra trùng lặp
        $stmt = $this->pdo->prepare("SELECT id FROM guide_schedules 
                                     WHERE guide_user_id = :gid AND schedule_date = :date");
        $stmt->execute([':gid' => $guideId, ':date' => $date]);
        if ($stmt->fetch()) {
            flash_set('danger', 'HDV đã có lịch vào ngày này!');
            header('Location: ' . BASE_URL . '?r=guide_schedules_create');
            exit;
        }

        // Thêm lịch làm việc
        $stmt = $this->pdo->prepare("INSERT INTO guide_schedules 
                                     (guide_user_id, schedule_date, status, start_time, end_time, location, notes) 
                                     VALUES (:gid, :date, :status, :start_time, :end_time, :location, :notes)");
        $result = $stmt->execute([
            ':gid' => $guideId,
            ':date' => $date,
            ':status' => $status,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
            ':location' => $location,
            ':notes' => $notes
        ]);

        if ($result) {
            flash_set('success', 'Tạo lịch làm việc thành công!');
        } else {
            flash_set('danger', 'Có lỗi xảy ra!');
        }

        header('Location: ' . BASE_URL . '?r=guide_schedules&guide_id=' . $guideId);
        exit;
    }

    // Sửa lịch làm việc
    public function edit($id)
    {
        $id = (int)$id;

        // Lấy thông tin lịch
        $stmt = $this->pdo->prepare("SELECT gs.*, u.full_name as guide_name
                                     FROM guide_schedules gs
                                     LEFT JOIN users u ON gs.guide_user_id = u.id
                                     WHERE gs.id = :id");
        $stmt->execute([':id' => $id]);
        $schedule = $stmt->fetch();

        if (!$schedule) {
            flash_set('danger', 'Không tìm thấy lịch làm việc!');
            header('Location: ' . BASE_URL . '?r=guide_schedules');
            exit;
        }

        // Lấy danh sách guides
        $stmtG = $this->pdo->query("SELECT u.id, u.full_name FROM users u 
                                      LEFT JOIN guides_info gi ON u.id = gi.user_id 
                                      WHERE gi.user_id IS NOT NULL 
                                      ORDER BY u.full_name ASC");
        $guides = $stmtG->fetchAll();

        require __DIR__ . '/../views/guide_schedules/edit_schedule.php';
    }

    // Cập nhật lịch làm việc
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?r=guide_schedules');
            exit;
        }

        $id = (int)$id;
        $guideId = (int)($_POST['guide_id'] ?? 0);
        $date = $_POST['date'] ?? '';
        $status = $_POST['status'] ?? 'available';
        $startTime = $_POST['start_time'] ?? null;
        $endTime = $_POST['end_time'] ?? null;
        $location = $_POST['location'] ?? '';
        $notes = $_POST['notes'] ?? '';

        // Kiểm tra trùng lặp (trừ record hiện tại)
        $stmt = $this->pdo->prepare("SELECT id FROM guide_schedules 
                                     WHERE guide_user_id = :gid AND schedule_date = :date AND id != :id");
        $stmt->execute([':gid' => $guideId, ':date' => $date, ':id' => $id]);
        if ($stmt->fetch()) {
            flash_set('danger', 'HDV đã có lịch vào ngày này!');
            header('Location: ' . BASE_URL . '?r=guide_schedules_edit&id=' . $id);
            exit;
        }

        // Cập nhật
        $stmt = $this->pdo->prepare("UPDATE guide_schedules 
                                     SET guide_user_id = :gid, schedule_date = :date, status = :status,
                                         start_time = :start_time, end_time = :end_time,
                                         location = :location, notes = :notes
                                     WHERE id = :id");
        $result = $stmt->execute([
            ':gid' => $guideId,
            ':date' => $date,
            ':status' => $status,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
            ':location' => $location,
            ':notes' => $notes,
            ':id' => $id
        ]);

        if ($result) {
            flash_set('success', 'Cập nhật lịch làm việc thành công!');
        } else {
            flash_set('danger', 'Có lỗi xảy ra!');
        }

        header('Location: ' . BASE_URL . '?r=guide_schedules&guide_id=' . $guideId);
        exit;
    }

    // Xóa lịch làm việc
    public function delete($id)
    {
        $id = (int)$id;

        // Lấy guide_id trước khi xóa
        $stmt = $this->pdo->prepare("SELECT guide_user_id FROM guide_schedules WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $schedule = $stmt->fetch();
        $guideId = $schedule ? (int)$schedule['guide_user_id'] : 0;

        $stmt = $this->pdo->prepare("DELETE FROM guide_schedules WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);

        if ($result) {
            flash_set('warning', 'Xóa lịch làm việc thành công!');
        } else {
            flash_set('danger', 'Có lỗi xảy ra!');
        }

        $redirectUrl = BASE_URL . '?r=guide_schedules';
        if ($guideId > 0) {
            $redirectUrl .= '&guide_id=' . $guideId;
        }
        
        header('Location: ' . $redirectUrl);
        exit;
    }

    // Bulk update - Cập nhật hàng loạt (ví dụ: nghỉ phép)
    public function bulkUpdate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?r=guide_schedules');
            exit;
        }

        $guideId = (int)($_POST['guide_id'] ?? 0);
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $status = $_POST['status'] ?? 'available';
        $notes = $_POST['notes'] ?? '';

        if (empty($startDate) || empty($endDate) || $guideId === 0) {
            flash_set('danger', 'Vui lòng điền đầy đủ thông tin!');
            header('Location: ' . BASE_URL . '?r=guide_schedules&guide_id=' . $guideId);
            exit;
        }

        // Tạo khoảng ngày
        $current = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = new DateInterval('P1D');

        $successCount = 0;
        $errorCount = 0;

        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            
            // Kiểm tra ngày này đã có lịch chưa
            $stmt = $this->pdo->prepare("SELECT id FROM guide_schedules 
                                         WHERE guide_user_id = :gid AND schedule_date = :date");
            $stmt->execute([':gid' => $guideId, ':date' => $dateStr]);
            
            if (!$stmt->fetch()) {
                // Thêm lịch mới
                $stmt = $this->pdo->prepare("INSERT INTO guide_schedules 
                                             (guide_user_id, schedule_date, status, notes) 
                                             VALUES (:gid, :date, :status, :notes)");
                if ($stmt->execute([':gid' => $guideId, ':date' => $dateStr, ':status' => $status, ':notes' => $notes])) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } else {
                $errorCount++;
            }
            
            $current->add($interval);
        }

        if ($successCount > 0) {
            flash_set('success', "Đã tạo $successCount lịch làm việc thành công!");
        }
        if ($errorCount > 0) {
            flash_set('danger', "$errorCount ngày đã có lịch hoặc có lỗi!");
        }

        header('Location: ' . BASE_URL . '?r=guide_schedules&guide_id=' . $guideId);
        exit;
    }
}
