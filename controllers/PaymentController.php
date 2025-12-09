<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../assets/configs/env.php';
require_once __DIR__ . '/../services/PaymentService.php';

class PaymentController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new BaseModel())->getConnection();
        $this->paymentService = new PaymentService();
    }

    // Danh sách thanh toán (có lọc theo booking_id nếu có)
    public function index()
    {
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        $createdBy = (int)($_GET['created_by'] ?? 0);
        $sql = "SELECT p.*, b.id AS booking_code, t.title AS tour_title, u.full_name AS created_by_name
                FROM payments p
                LEFT JOIN bookings b ON p.booking_id = b.id
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u ON p.created_by = u.id";
        $conds = [];
        $params = [];
        if ($bookingId > 0) { $conds[] = 'p.booking_id = :bid'; $params['bid'] = $bookingId; }
        if ($createdBy > 0) { $conds[] = 'p.created_by = :cb'; $params['cb'] = $createdBy; }
        if (!empty($conds)) { $sql .= ' WHERE ' . implode(' AND ', $conds); }
        $sql .= " ORDER BY p.payment_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $payments = $stmt->fetchAll();

        // Lấy danh sách booking cho bộ lọc
        $stmtB = $this->pdo->query("SELECT b.id, b.id AS booking_code, t.title AS tour_title
                                     FROM bookings b
                                     LEFT JOIN tours t ON b.tour_id = t.id
                                     ORDER BY b.id DESC");
        $bookings = $stmtB->fetchAll();

        require __DIR__ . '/../views/payments/list_payments.php';
    }

    // Form tạo thanh toán mới
    public function create($bookingId = 0)
    {
        $bookingId = (int)$bookingId;
        if ($bookingId === 0) {
            $bookingId = (int)($_GET['booking_id'] ?? 0);
        }

        $payment = null;
        if ($bookingId > 0) {
            $stmt = $this->pdo->prepare("SELECT b.*, t.title AS tour_title
                                         FROM bookings b
                                         LEFT JOIN tours t ON b.tour_id = t.id
                                         WHERE b.id = :id");
            $stmt->execute(['id' => $bookingId]);
            $payment = $stmt->fetch();
        }

        // Lấy danh sách booking cho select
        $stmt = $this->pdo->query("SELECT b.id, b.id AS booking_code, t.title AS tour_title
                                   FROM bookings b
                                   LEFT JOIN tours t ON b.tour_id = t.id
                                   ORDER BY b.id DESC");
        $bookings = $stmt->fetchAll();

        require __DIR__ . '/../views/payments/create_payment.php';
    }

    // Lưu thanh toán mới
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            flash_set('danger', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=payments_create');
            exit;
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $paymentMethod = $_POST['payment_method'] ?? '';
        $transactionId = $_POST['transaction_id'] ?? '';
        $paymentDate = $_POST['payment_date'] ?? '';
        $status = $_POST['status'] ?? 'pending';
        $notes = $_POST['notes'] ?? '';
        $createdBy = $_SESSION['user_id'] ?? null;

        $stmt = $this->pdo->prepare("INSERT INTO payments (booking_id, amount, payment_method, transaction_id, payment_date, status, notes, created_by)
                                     VALUES (:bid, :amt, :pm, :txid, :pdate, :st, :notes, :cb)");
        $stmt->execute([
            'bid' => $bookingId,
            'amt' => $amount,
            'pm' => $paymentMethod,
            'txid' => $transactionId,
            'pdate' => $paymentDate,
            'st' => $status,
            'notes' => $notes,
            'cb' => $createdBy
        ]);

        // TỰ ĐỘNG cập nhật trạng thái booking sau khi thêm thanh toán
        $updateResult = $this->paymentService->updateBookingStatusAfterPayment($bookingId);
        
        // Lưu thông báo vào session để hiển thị
        $_SESSION['payment_message'] = $updateResult['message'];
        $_SESSION['payment_status'] = $updateResult['success'] ? 'success' : 'danger';
        flash_set($updateResult['success'] ? 'success' : 'danger', $updateResult['message']);

        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $bookingId);
        exit;
    }

    // Form sửa thanh toán
    public function edit($id = 0)
    {
        $id = (int)$id;
        $stmt = $this->pdo->prepare("SELECT p.*, b.id AS booking_code, t.title AS tour_title
                                     FROM payments p
                                     LEFT JOIN bookings b ON p.booking_id = b.id
                                     LEFT JOIN tours t ON b.tour_id = t.id
                                     WHERE p.id = :id");
        $stmt->execute(['id' => $id]);
        $payment = $stmt->fetch();

        if (!$payment) {
            echo '<div class="alert alert-danger">Không tìm thấy thanh toán.</div>';
            return;
        }

        // Lấy danh sách booking cho select
        $stmtB = $this->pdo->query("SELECT b.id, b.id AS booking_code, t.title AS tour_title
                                     FROM bookings b
                                     LEFT JOIN tours t ON b.tour_id = t.id
                                     ORDER BY b.id DESC");
        $bookings = $stmtB->fetchAll();

        require __DIR__ . '/../views/payments/edit_payment.php';
    }

    // Cập nhật thanh toán
    public function update($id = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            flash_set('danger', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=payments_edit&id=' . (int)$id);
            exit;
        }
        $id = (int)$id;

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $paymentMethod = $_POST['payment_method'] ?? '';
        $transactionId = $_POST['transaction_id'] ?? '';
        $paymentDate = $_POST['payment_date'] ?? '';
        $status = $_POST['status'] ?? 'pending';
        $notes = $_POST['notes'] ?? '';

        $stmt = $this->pdo->prepare("UPDATE payments
                                     SET booking_id=:bid, amount=:amt, payment_method=:pm, transaction_id=:txid,
                                         payment_date=:pdate, status=:st, notes=:notes
                                     WHERE id=:id");
        $stmt->execute([
            'bid' => $bookingId,
            'amt' => $amount,
            'pm' => $paymentMethod,
            'txid' => $transactionId,
            'pdate' => $paymentDate,
            'st' => $status,
            'notes' => $notes,
            'id' => $id
        ]);

        // TỰ ĐỘNG cập nhật trạng thái booking sau khi cập nhật thanh toán
        $updateResult = $this->paymentService->updateBookingStatusAfterPayment($bookingId);
        
        // Lưu thông báo vào session để hiển thị
        $_SESSION['payment_message'] = $updateResult['message'];
        $_SESSION['payment_status'] = $updateResult['success'] ? 'success' : 'danger';
        flash_set($updateResult['success'] ? 'success' : 'danger', $updateResult['message']);

        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $bookingId);
        exit;
    }

    // Hoàn tiền (đổi trạng thái payment thành refunded)
    public function refund($id = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            flash_set('danger', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=payments');
            exit;
        }
        $id = (int)$id;
        // Lấy payment
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $payment = $stmt->fetch();
        if (!$payment) {
            flash_set('danger', 'Không tìm thấy payment.');
            header('Location: ' . BASE_URL . '?r=payments');
            exit;
        }
        // Chỉ cho phép refund nếu đang completed
        if (($payment['status'] ?? '') !== 'completed') {
            flash_set('danger', 'Chỉ hoàn tiền cho giao dịch hoàn tất.');
            header('Location: ' . BASE_URL . '?r=payments_edit&id=' . $id);
            exit;
        }
        // Cập nhật trạng thái thành refunded, lưu ghi chú nếu có
        $notes = trim((string)($_POST['notes'] ?? ''));
        $stmtU = $this->pdo->prepare("UPDATE payments SET status = 'refunded', notes = :notes WHERE id = :id");
        $stmtU->execute(['notes' => $notes !== '' ? $notes : ($payment['notes'] ?? ''), 'id' => $id]);

        // Cập nhật trạng thái booking sau refund
        $bookingId = (int)($payment['booking_id'] ?? 0);
        if ($bookingId > 0) {
            $updateResult = $this->paymentService->updateBookingStatusAfterPayment($bookingId);
            $_SESSION['payment_message'] = 'Đã hoàn tiền giao dịch #' . $id . '. ' . ($updateResult['message'] ?? '');
            $_SESSION['payment_status'] = $updateResult['success'] ? 'success' : 'danger';
            flash_set($updateResult['success'] ? 'success' : 'danger', 'Đã hoàn tiền giao dịch #' . $id . '. ' . ($updateResult['message'] ?? ''));
        }

        header('Location: ' . BASE_URL . '?r=booking_payment_history&booking_id=' . $bookingId);
        exit;
    }

    // Xóa thanh toán
    public function delete($id = 0)
    {
        if (!csrf_validate($_GET['csrf_token'] ?? '')) {
            flash_set('danger', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=payments');
            exit;
        }
        $id = (int)$id;
        $stmt = $this->pdo->prepare("SELECT booking_id FROM payments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $payment = $stmt->fetch();

        if ($payment) {
            $del = $this->pdo->prepare("DELETE FROM payments WHERE id = :id");
            $del->execute(['id' => $id]);

            // TỰ ĐỘNG cập nhật trạng thái booking sau khi xóa thanh toán
            $updateResult = $this->paymentService->updateBookingStatusAfterPayment($payment['booking_id']);
            
            // Lưu thông báo vào session để hiển thị
            $_SESSION['payment_message'] = $updateResult['message'];
            $_SESSION['payment_status'] = $updateResult['success'] ? 'success' : 'danger';
            flash_set($updateResult['success'] ? 'success' : 'danger', $updateResult['message']);
        }

        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . (int)($payment['booking_id'] ?? 0));
        exit;
    }

    // Xem lịch sử thanh toán của booking
    public function bookingPaymentHistory()
    {
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        if ($bookingId <= 0) {
            echo '<div class="alert alert-danger">Không tìm thấy booking.</div>';
            return;
        }

        // Lấy thông tin tổng quan thanh toán
        $summary = $this->paymentService->getBookingPaymentSummary($bookingId);
        if (!$summary) {
            echo '<div class="alert alert-danger">Không tìm thấy thông tin booking.</div>';
            return;
        }

        // Lấy lịch sử thanh toán
        $payments = $this->paymentService->getPaymentHistory($bookingId);

        require __DIR__ . '/../views/payments/booking_payment_history.php';
    }
}
