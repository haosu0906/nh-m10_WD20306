<?php
require_once __DIR__ . '/../models/BaseModel.php';

class TourLogsController {
    private $pdo;
    public function __construct() { $this->pdo = (new BaseModel())->getConnection(); }

    public function index() {
        $guideId = (int)($_GET['guide_id'] ?? 0);
        $tourId = (int)($_GET['tour_id'] ?? 0);
        $sql = "SELECT tl.*, t.title AS tour_title, u.full_name AS guide_name
                FROM tour_logs tl
                LEFT JOIN tours t ON tl.tour_id = t.id
                LEFT JOIN users u ON tl.guide_user_id = u.id
                WHERE 1=1";
        $params = [];
        if ($guideId > 0) { $sql .= " AND tl.guide_user_id = :gid"; $params[':gid'] = $guideId; }
        if ($tourId > 0) { $sql .= " AND tl.tour_id = :tid"; $params[':tid'] = $tourId; }
        $sql .= " ORDER BY tl.log_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll();
        require __DIR__ . '/../views/tour_logs/index.php';
    }

    public function create() {
        $tours = $this->pdo->query("SELECT id, title FROM tours ORDER BY title")->fetchAll();
        require __DIR__ . '/../views/tour_logs/form.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . BASE_URL . '?r=tour_logs'); exit; }
        $stmt = $this->pdo->prepare("INSERT INTO tour_logs (tour_id, guide_user_id, log_date, incident_details, customer_feedback, weather) VALUES (:tour_id, :guide_user_id, :log_date, :incidents, :feedback, :weather)");
        $ok = $stmt->execute([
            ':tour_id' => (int)($_POST['tour_id'] ?? 0),
            ':guide_user_id' => (int)($_POST['guide_user_id'] ?? ($_SESSION['user_id'] ?? 0)),
            ':log_date' => $_POST['log_date'] ?? date('Y-m-d H:i:s'),
            ':incidents' => $_POST['incident_details'] ?? '',
            ':feedback' => $_POST['customer_feedback'] ?? '',
            ':weather' => $_POST['weather'] ?? ''
        ]);
        header('Location: ' . BASE_URL . '?r=tour_logs' . (isset($_POST['tour_id'])?('&tour_id='.(int)$_POST['tour_id']):''));
        exit;
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $this->pdo->prepare("SELECT * FROM tour_logs WHERE id = ?");
        $stmt->execute([$id]);
        $log = $stmt->fetch();
        $tours = $this->pdo->query("SELECT id, title FROM tours ORDER BY title")->fetchAll();
        require __DIR__ . '/../views/tour_logs/form.php';
    }

    public function update() {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->pdo->prepare("UPDATE tour_logs SET tour_id=:tour_id, guide_user_id=:guide_user_id, log_date=:log_date, incident_details=:incidents, customer_feedback=:feedback, weather=:weather WHERE id=:id");
        $stmt->execute([
            ':tour_id' => (int)($_POST['tour_id'] ?? 0),
            ':guide_user_id' => (int)($_POST['guide_user_id'] ?? ($_SESSION['user_id'] ?? 0)),
            ':log_date' => $_POST['log_date'] ?? date('Y-m-d H:i:s'),
            ':incidents' => $_POST['incident_details'] ?? '',
            ':feedback' => $_POST['customer_feedback'] ?? '',
            ':weather' => $_POST['weather'] ?? '',
            ':id' => $id
        ]);
        header('Location: ' . BASE_URL . '?r=tour_logs' . (isset($_POST['tour_id'])?('&tour_id='.(int)$_POST['tour_id']):''));
        exit;
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $this->pdo->prepare("DELETE FROM tour_logs WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: ' . BASE_URL . '?r=tour_logs');
        exit;
    }
}
?>
