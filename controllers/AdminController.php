<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class AdminController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new BaseModel())->getConnection();
    }

    public function dashboard()
    {
        $metrics = [
            'mtdRevenue' => 0.0,
            'newBookingsToday' => 0,
            'activeTours' => 0,
            'pendingIssues' => 0,
        ];

        try {
            $start = $_GET['start'] ?? null;
            $end = $_GET['end'] ?? null;
            if (!$start || !$end) {
                $start = date('Y-m-01');
                $end = date('Y-m-t');
            }
            $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(p.amount),0) AS total FROM payments p WHERE p.status='completed' AND p.payment_date BETWEEN :start AND :end");
            $stmt->execute([':start' => $start, ':end' => $end]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $metrics['mtdRevenue'] = (float)($row['total'] ?? 0);
        } catch (PDOException $e) {}

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) AS c FROM bookings WHERE DATE(date_booked)=CURDATE()");
            $metrics['newBookingsToday'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
        } catch (PDOException $e) {
            try {
                $stmt = $this->pdo->query("SELECT COUNT(*) AS c FROM bookings WHERE DATE(created_at)=CURDATE()");
                $metrics['newBookingsToday'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
            } catch (PDOException $e2) {}
        }

        try {
            $stmt = $this->pdo->query("SELECT COUNT(DISTINCT tour_id) AS c FROM tour_schedules WHERE start_date >= CURDATE()");
            $metrics['activeTours'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
        } catch (PDOException $e) {
            try {
                $stmt = $this->pdo->query("SELECT COUNT(*) AS c FROM tours WHERE is_active = 1");
                $metrics['activeTours'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
            } catch (PDOException $e2) {
                try {
                    $stmt = $this->pdo->query("SELECT COUNT(*) AS c FROM tours");
                    $metrics['activeTours'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
                } catch (PDOException $e3) {}
            }
        }

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_status = 'pending'");
            $metrics['pendingIssues'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
        } catch (PDOException $e) {
            try {
                $stmt = $this->pdo->query("SELECT COUNT(*) AS c FROM bookings WHERE status = 'pending'");
                $metrics['pendingIssues'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
            } catch (PDOException $e2) {}
        }

        $recentBookings = [];
        try {
            $sql = "SELECT b.id, b.booking_status, b.status, b.total_price AS amount, b.date_booked, t.title AS tour_title, u.full_name AS customer_name, u.phone AS customer_phone
                    FROM bookings b
                    LEFT JOIN tours t ON b.tour_id = t.id
                    LEFT JOIN users u ON b.customer_user_id = u.id
                    ORDER BY b.date_booked DESC, b.id DESC
                    LIMIT 10";
            $stmt = $this->pdo->query($sql);
            $recentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            try {
                $sql2 = "SELECT b.id, b.booking_status, b.status, b.total_amount AS amount, b.date_booked, t.title AS tour_title, u.full_name AS customer_name, u.phone AS customer_phone
                         FROM bookings b
                         LEFT JOIN tours t ON b.tour_id = t.id
                         LEFT JOIN users u ON b.customer_user_id = u.id
                         ORDER BY b.date_booked DESC, b.id DESC
                         LIMIT 10";
                $stmt2 = $this->pdo->query($sql2);
                $recentBookings = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {}
        }

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function profile()
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'admin')) {
            header('Location: ' . BASE_URL . '?r=admin_login');
            exit;
        }
        $userModel = new UserModel();
        $user = $userModel->find((int)$_SESSION['user_id']);
        require __DIR__ . '/../views/admin/profile.php';
    }
}
?>
