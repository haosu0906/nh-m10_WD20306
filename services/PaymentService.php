<?php
require_once __DIR__ . '/../assets/configs/env.php';

class PaymentService
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Tự động cập nhật trạng thái booking sau khi thêm/cập nhật thanh toán
     * Logic: Kiểm tra tổng tiền đã thanh toán và cập nhật trạng thái booking tương ứng
     */
    public function updateBookingStatusAfterPayment($bookingId)
    {
        try {
            // Lấy thông tin booking
            $stmt = $this->pdo->prepare("SELECT total_price, booking_status FROM bookings WHERE id = :id");
            $stmt->execute(['id' => $bookingId]);
            $booking = $stmt->fetch();

            if (!$booking) {
                return ['success' => false, 'message' => 'Không tìm thấy booking'];
            }

            // Tính tổng tiền đã thanh toán (chỉ tính các payment có status = completed)
            $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total_paid 
                                        FROM payments 
                                        WHERE booking_id = :bid AND status = 'completed'");
            $stmt->execute(['bid' => $bookingId]);
            $result = $stmt->fetch();
            $totalPaid = (float)$result['total_paid'];

            // Tính tiền cọc cần (30% của tổng giá)
            $depositAmount = $booking['total_price'] * 0.3;
            $totalPrice = (float)$booking['total_price'];

            $oldStatus = $booking['booking_status'];
            $newStatus = $oldStatus;

            // Logic cập nhật trạng thái
            if ($totalPaid >= $totalPrice) {
                // Đã thanh toán đủ -> Completed
                $newStatus = 'completed';
            } elseif ($totalPaid >= $depositAmount && $oldStatus === 'pending') {
                // Đã cọc đủ và đang ở trạng thái pending -> Deposited
                $newStatus = 'deposit';
            }

            // Chỉ cập nhật nếu trạng thái thay đổi
            if ($newStatus !== $oldStatus) {
                // Bắt đầu transaction
                $this->pdo->beginTransaction();

                try {
                    // Cập nhật trạng thái booking
                    $stmt = $this->pdo->prepare("UPDATE bookings SET booking_status = :status WHERE id = :id");
                    $stmt->execute(['status' => $newStatus, 'id' => $bookingId]);

                    // Ghi log thay đổi trạng thái
                    $stmt = $this->pdo->prepare("INSERT INTO booking_status_logs 
                                                (booking_id, old_status, new_status, changed_by_user_id, changed_at) 
                                                VALUES (:bid, :old, :new, :user, NOW())");
                    $stmt->execute([
                        'bid' => $bookingId,
                        'old' => $oldStatus,
                        'new' => $newStatus,
                        'user' => $_SESSION['user_id'] ?? 0
                    ]);

                    $this->pdo->commit();

                    return [
                        'success' => true,
                        'message' => "Đã cập nhật trạng thái booking từ '{$oldStatus}' thành '{$newStatus}'",
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'total_paid' => $totalPaid,
                        'deposit_required' => $depositAmount,
                        'total_required' => $totalPrice
                    ];
                } catch (Exception $e) {
                    $this->pdo->rollback();
                    throw $e;
                }
            }

            return [
                'success' => true,
                'message' => 'Trạng thái booking không thay đổi',
                'status' => $oldStatus,
                'total_paid' => $totalPaid,
                'deposit_required' => $depositAmount,
                'total_required' => $totalPrice
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    /**
     * Lấy lịch sử hình thanh toán của một booking
     */
    public function getPaymentHistory($bookingId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, 
                                               CONCAT('BK', b.id) as booking_code,
                                               t.title as tour_title,
                                               u.full_name as created_by_name
                                        FROM payments p
                                        LEFT JOIN bookings b ON p.booking_id = b.id
                                        LEFT JOIN tours t ON b.tour_id = t.id
                                        LEFT JOIN users u ON p.created_by = u.id
                                        WHERE p.booking_id = :bid
                                        ORDER BY p.payment_date DESC, p.created_at DESC");
            $stmt->execute(['bid' => $bookingId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Lấy thông tin tổng quan thanh toán của booking
     */
    public function getBookingPaymentSummary($bookingId)
    {
        try {
            // Lấy thông tin booking
            $stmt = $this->pdo->prepare("SELECT b.*, t.title as tour_title
                                        FROM bookings b
                                        LEFT JOIN tours t ON b.tour_id = t.id
                                        WHERE b.id = :id");
            $stmt->execute(['id' => $bookingId]);
            $booking = $stmt->fetch();

            if (!$booking) {
                return null;
            }

            // Lấy tổng tiền đã thanh toán theo từng trạng thái
            $stmt = $this->pdo->prepare("SELECT status, COALESCE(SUM(amount), 0) as total
                                        FROM payments 
                                        WHERE booking_id = :bid
                                        GROUP BY status");
            $stmt->execute(['bid' => $bookingId]);
            $paymentTotals = $stmt->fetchAll();

            // Tính toán các thông số
            $totalPrice = (float)$booking['total_price'];
            $depositRequired = $totalPrice * 0.3;
            $completedTotal = 0;
            $pendingTotal = 0;

            foreach ($paymentTotals as $pt) {
                if ($pt['status'] === 'completed') {
                    $completedTotal = (float)$pt['total'];
                } elseif ($pt['status'] === 'pending') {
                    $pendingTotal = (float)$pt['total'];
                }
            }

            $remainingAmount = $totalPrice - $completedTotal;
            $depositProgress = min(100, ($completedTotal / $depositRequired) * 100);
            $fullPaymentProgress = min(100, ($completedTotal / $totalPrice) * 100);

            return [
                'booking' => $booking,
                'total_price' => $totalPrice,
                'deposit_required' => $depositRequired,
                'completed_total' => $completedTotal,
                'pending_total' => $pendingTotal,
                'remaining_amount' => $remainingAmount,
                'deposit_progress' => $depositProgress,
                'full_payment_progress' => $fullPaymentProgress,
                'is_deposit_fulfilled' => $completedTotal >= $depositRequired,
                'is_fully_paid' => $completedTotal >= $totalPrice,
                'payment_totals' => $paymentTotals
            ];
        } catch (Exception $e) {
            return null;
        }
    }
}
