<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../assets/configs/env.php';

class CancellationPolicyController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new BaseModel())->getConnection();
    }

    // Danh sách chính sách (có lọc theo tour_id nếu có)
    public function index()
    {
        $tourId = (int)($_GET['tour_id'] ?? 0);
        $sql = "SELECT cp.*, t.title AS tour_title
                FROM cancellation_policies cp
                LEFT JOIN tours t ON cp.tour_id = t.id";
        if ($tourId > 0) {
            $sql .= " WHERE cp.tour_id = :tid";
        }
        $sql .= " ORDER BY cp.tour_id, cp.days_before DESC";
        
        // Debug
        error_log("CancellationPolicy SQL: " . $sql);
        
        $stmt = $this->pdo->prepare($sql);
        if ($tourId > 0) {
            $stmt->execute(['tid' => $tourId]);
        } else {
            $stmt->execute();
        }
        $policies = $stmt->fetchAll();
        
        // Debug
        error_log("Policies found: " . count($policies));

        // Lấy danh sách tour cho bộ lọc
        $stmtT = $this->pdo->query("SELECT id, title FROM tours ORDER BY title");
        $tours = $stmtT->fetchAll();

        require __DIR__ . '/../views/cancellation_policies/list_policies.php';
    }

    // Form tạo chính sách mới
    public function create()
    {
        $tourId = (int)($_GET['tour_id'] ?? 0);
        $policy = null;
        if ($tourId > 0) {
            $stmt = $this->pdo->prepare("SELECT id, title FROM tours WHERE id = :id");
            $stmt->execute(['id' => $tourId]);
            $policy = $stmt->fetch();
        }

        // Lấy danh sách tour cho select
        $stmt = $this->pdo->query("SELECT id, title FROM tours ORDER BY title");
        $tours = $stmt->fetchAll();

        require __DIR__ . '/../views/cancellation_policies/create_policy.php';
    }

    // Lưu chính sách mới
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $tourId = (int)($_POST['tour_id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $daysBefore = (int)($_POST['days_before'] ?? 0);
        $refundPercentage = (float)($_POST['refund_percentage'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $this->pdo->prepare("INSERT INTO cancellation_policies (tour_id, name, description, days_before, refund_percentage, is_active)
                                     VALUES (:tid, :name, :desc, :days, :percent, :active)");
        $stmt->execute([
            'tid' => $tourId,
            'name' => $name,
            'desc' => $description,
            'days' => $daysBefore,
            'percent' => $refundPercentage,
            'active' => $isActive
        ]);

        header('Location: ' . BASE_URL . '?r=cancellation_policies' . ($tourId ? '?tour_id=' . $tourId : ''));
        exit;
    }

    // Form sửa chính sách
    public function edit($id = 0)
    {
        $id = (int)$id;
        $stmt = $this->pdo->prepare("SELECT cp.*, t.title AS tour_title
                                     FROM cancellation_policies cp
                                     LEFT JOIN tours t ON cp.tour_id = t.id
                                     WHERE cp.id = :id");
        $stmt->execute(['id' => $id]);
        $policy = $stmt->fetch();

        if (!$policy) {
            echo '<div class="alert alert-danger">Không tìm thấy chính sách hủy.</div>';
            return;
        }

        // Lấy danh sách tour cho select
        $stmtT = $this->pdo->query("SELECT id, title FROM tours ORDER BY title");
        $tours = $stmtT->fetchAll();

        require __DIR__ . '/../views/cancellation_policies/edit_policy.php';
    }

    // Cập nhật chính sách
    public function update($id = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $id = (int)$id;

        $tourId = (int)($_POST['tour_id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $daysBefore = (int)($_POST['days_before'] ?? 0);
        $refundPercentage = (float)($_POST['refund_percentage'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $this->pdo->prepare("UPDATE cancellation_policies
                                     SET tour_id=:tid, name=:name, description=:desc, days_before=:days,
                                         refund_percentage=:percent, is_active=:active
                                     WHERE id=:id");
        $stmt->execute([
            'tid' => $tourId,
            'name' => $name,
            'desc' => $description,
            'days' => $daysBefore,
            'percent' => $refundPercentage,
            'active' => $isActive,
            'id' => $id
        ]);

        header('Location: ' . BASE_URL . '?r=cancellation_policies' . ($tourId ? '?tour_id=' . $tourId : ''));
        exit;
    }

    // Xóa chính sách
    public function delete($id = 0)
    {
        $id = (int)$id;
        $stmt = $this->pdo->prepare("SELECT tour_id FROM cancellation_policies WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $policy = $stmt->fetch();

        if ($policy) {
            $del = $this->pdo->prepare("DELETE FROM cancellation_policies WHERE id = :id");
            $del->execute(['id' => $id]);
        }

        header('Location: ' . BASE_URL . '?r=cancellation_policies' . ($policy ? '?tour_id=' . $policy['tour_id'] : ''));
        exit;
    }
}
