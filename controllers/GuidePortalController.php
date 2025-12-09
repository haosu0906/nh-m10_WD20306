<?php
require_once __DIR__ . '/../models/BaseModel.php';

class GuidePortalController {
    private $pdo;
    public function __construct() { $this->pdo = (new BaseModel())->getConnection(); }

    public function index() {
        // Nếu có đăng nhập với role guide thì lọc theo user
        $guideId = (int)($_SESSION['user_id'] ?? 0);
        $assignments = [];
        if ($guideId > 0 && (($_SESSION['role'] ?? '') === 'guide')) {
            $stmt = $this->pdo->prepare("SELECT ga.*, t.title AS tour_title, ts.start_date, ts.end_date
                                          FROM guide_assignments ga
                                          LEFT JOIN tour_schedules ts ON ga.schedule_id = ts.id
                                          LEFT JOIN tours t ON ga.tour_id = t.id
                                          WHERE ga.guide_user_id = :gid
                                          ORDER BY ts.start_date DESC, ga.assignment_date DESC");
            $stmt->execute([':gid'=>$guideId]);
            $assignments = $stmt->fetchAll();
        } else {
            $stmt = $this->pdo->query("SELECT ga.*, t.title AS tour_title, ts.start_date, ts.end_date
                                       FROM guide_assignments ga
                                       LEFT JOIN tour_schedules ts ON ga.schedule_id = ts.id
                                       LEFT JOIN tours t ON ga.tour_id = t.id
                                       ORDER BY ts.start_date DESC, ga.assignment_date DESC");
            $assignments = $stmt->fetchAll();
        }
        require __DIR__ . '/../views/guide_portal/index.php';
    }

    public function customers() {
        $tourId = (int)($_GET['tour_id'] ?? 0);
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        if ($tourId <= 0) { echo 'Invalid tour'; return; }
        $sql = "SELECT bg.* , b.id AS booking_id
                FROM booking_guests bg
                LEFT JOIN bookings b ON bg.booking_id = b.id
                WHERE b.tour_id = :tid";
        $params = [':tid'=>$tourId];
        if ($start && $end) { $sql .= " AND b.date_booked BETWEEN :start AND :end"; $params[':start']=$start; $params[':end']=$end; }
        $sql .= " ORDER BY b.id DESC, bg.id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $guests = $stmt->fetchAll();
        require __DIR__ . '/../views/guide_portal/customers.php';
    }

    public function updateGuestNote() {
        if (($_SESSION['role'] ?? '') !== 'guide') {
            http_response_code(403);
            echo 'forbidden';
            return;
        }
        $guestId = (int)($_POST['guest_id'] ?? 0);
        $note = trim($_POST['notes'] ?? '');
        if ($guestId <= 0) { http_response_code(400); echo 'bad_request'; return; }
        // Xác thực: guest thuộc tour/lịch mà HDV đang được phân công
        try {
            $gid = (int)($_SESSION['user_id'] ?? 0);
            $sql = "SELECT COUNT(*) FROM booking_guests bg
                    JOIN bookings b ON bg.booking_id = b.id
                    JOIN guide_assignments ga ON ga.tour_id = b.tour_id AND ga.schedule_id = b.schedule_id
                    WHERE bg.id = :g AND ga.guide_user_id = :u";
            $st = $this->pdo->prepare($sql);
            $st->execute([':g'=>$guestId, ':u'=>$gid]);
            $ok = ((int)$st->fetchColumn()) > 0;
            if (!$ok) { http_response_code(403); echo 'forbidden'; return; }
        } catch (Throwable $e) { /* ignore and continue */ }
        try {
            $stmt = $this->pdo->prepare("UPDATE booking_guests SET notes = :n WHERE id = :id");
            $stmt->execute([':n'=>$note, ':id'=>$guestId]);
            echo 'ok';
        } catch (Throwable $e) {
            http_response_code(500); echo 'error';
        }
    }
}
?>
