<?php
require_once __DIR__ . '/../../assets/configs/env.php';
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Lịch sử thanh toán - BK<?= $summary['booking']['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    .progress {
        height: 8px;
    }

    .status-badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }

    .payment-summary-card {
        border-left: 4px solid var(--accent);
    }

    .payment-row {
        border-left: 3px solid transparent;
        transition: all 0.2s;
    }

    .payment-row:hover {
        border-left-color: var(--accent);
        background-color: #f8f9fa;
    }

    .payment-status-completed {
        border-left-color: #28a745;
    }

    .payment-status-pending {
        border-left-color: #ffc107;
    }

    .payment-status-failed {
        border-left-color: #dc3545;
    }
    </style>
</head>

<body>
    <?php $current_page='payments'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3><i class="fas fa-history me-2"></i>Lịch sử thanh toán</h3>
                <p class="text-muted mb-0">Booking #BK<?= $summary['booking']['id'] ?> - <?= htmlspecialchars($summary['booking']['tour_title']) ?></p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>?r=payments&booking_id=<?= $summary['booking']['id'] ?>" class="btn btn-outline-primary me-2">
                    <i class="fas fa-plus me-1"></i>Thêm thanh toán
                </a>
                <a href="<?= BASE_URL ?>?r=booking" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại Booking
                </a>
            </div>
        </div>

        <?php if (!empty($_SESSION['payment_message'])): ?>
        <div class="alert alert-<?= ($_SESSION['payment_status'] ?? '') === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['payment_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['payment_message'], $_SESSION['payment_status']); endif; ?>

        <!-- Thông tin tổng quan -->
        <div class="card payment-summary-card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-chart-pie me-2"></i>Tổng quan thanh toán
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="text-muted small">Tổng giá tour</label>
                                <div class="fw-bold text-primary"><?= number_format($summary['total_price'], 0, ',', '.') ?> VNĐ</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Tiền cọc cần (30%)</label>
                                <div class="fw-bold text-warning"><?= number_format($summary['deposit_required'], 0, ',', '.') ?> VNĐ</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Đã thanh toán</label>
                                <div class="fw-bold text-success"><?= number_format($summary['completed_total'], 0, ',', '.') ?> VNĐ</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Còn lại</label>
                                <div class="fw-bold text-danger"><?= number_format($summary['remaining_amount'], 0, ',', '.') ?> VNĐ</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Trạng thái booking</label>
                                <div>
                                    <?php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'deposit' => 'info', 
                                        'completed' => 'success',
                                        'canceled' => 'danger'
                                    ];
                                    $statusText = [
                                        'pending' => 'Chờ cọc',
                                        'deposit' => 'Đã cọc',
                                        'completed' => 'Đã thanh toán',
                                        'canceled' => 'Đã hủy'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusClass[$summary['booking']['booking_status']] ?> status-badge">
                                        <?= $statusText[$summary['booking']['booking_status']] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Tiền đang chờ xử lý</label>
                                <div class="fw-bold text-secondary"><?= number_format($summary['pending_total'], 0, ',', '.') ?> VNĐ</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-3">Tiến độ thanh toán</h6>
                        
                        <!-- Tiến độ cọc -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Tiến độ cọc</span>
                                <span><?= round($summary['deposit_progress'], 1) ?>%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: <?= $summary['deposit_progress'] ?>%"></div>
                            </div>
                            <small class="text-muted">
                                <?= $summary['is_deposit_fulfilled'] ? '✅ Đã đủ cọc' : '⏳ Chờ đủ cọc' ?>
                            </small>
                        </div>

                        <!-- Tiến độ thanh toán đầy đủ -->
                        <div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Thanh toán đầy đủ</span>
                                <span><?= round($summary['full_payment_progress'], 1) ?>%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: <?= $summary['full_payment_progress'] ?>%"></div>
                            </div>
                            <small class="text-muted">
                                <?= $summary['is_fully_paid'] ? '✅ Đã thanh toán đủ' : '⏳ Chờ thanh toán đủ' ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch sử thanh toán -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lịch sử giao dịch</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($payments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ngày thanh toán</th>
                                    <th>Số tiền</th>
                                    <th>Phương thức</th>
                                    <th>Mã giao dịch</th>
                                    <th>Trạng thái</th>
                                    <th>Người tạo</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr class="payment-row payment-status-<?= $payment['status'] ?>">
                                        <td>#<?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($payment['payment_date'])) ?></td>
                                        <td class="fw-bold"><?= number_format($payment['amount'], 0, ',', '.') ?> VNĐ</td>
                                        <td>
                                            <?php
                                            $methods = [
                                                'cash' => 'Tiền mặt',
                                                'bank_transfer' => 'Chuyển khoản',
                                                'credit_card' => 'Thẻ tín dụng',
                                                'momo' => 'MoMo',
                                                'zalopay' => 'ZaloPay'
                                            ];
                                            ?>
                                            <?= $methods[$payment['payment_method']] ?? $payment['payment_method'] ?>
                                        </td>
                                        <td><?= $payment['transaction_id'] ?: '---' ?></td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'failed' => 'danger',
                                                'refunded' => 'secondary'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Chờ xử lý',
                                                'completed' => 'Hoàn tất',
                                                'failed' => 'Thất bại',
                                                'refunded' => 'Đã hoàn'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $statusColors[$payment['status']] ?>">
                                                <?= $statusLabels[$payment['status']] ?>
                                            </span>
                                        </td>
                                        <td><?= $payment['created_by_name'] ?: '---' ?></td>
                                        <td><?= $payment['notes'] ?: '---' ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= BASE_URL ?>?r=payments_edit&id=<?= $payment['id'] ?>" class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($payment['status'] === 'completed'): ?>
                                                <button type="button" class="btn btn-outline-warning btn-refund" data-id="<?= $payment['id'] ?>" title="Hoàn tiền">
                                                    <i class="fas fa-rotate-left"></i>
                                                </button>
                                                <?php endif; ?>
                                                <a href="<?= BASE_URL ?>?r=payments_delete&id=<?= $payment['id'] ?>&csrf_token=<?= urlencode(csrf_token()) ?>" 
                                                   class="btn btn-outline-danger" 
                                                   onclick="return confirm('Xác nhận xóa giao dịch này?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có giao dịch nào</h5>
                        <p class="text-muted">Booking này chưa có lịch sử thanh toán.</p>
                        <a href="<?= BASE_URL ?>?r=payments_create&booking_id=<?= $summary['booking']['id'] ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm thanh toán đầu tiên
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <form method="post" id="refundForm">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Hoàn tiền giao dịch</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>Xác nhận hoàn tiền cho giao dịch #<span id="refundPaymentId"></span>?</p>
              <div class="mb-3">
                <label class="form-label">Ghi chú (tuỳ chọn)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Nhập ghi chú..."></textarea>
              </div>
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-warning">Hoàn tiền</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function(){
      document.querySelectorAll('.btn-refund').forEach(function(btn){
        btn.addEventListener('click', function(){
          var id = this.dataset.id;
          var pidEl = document.getElementById('refundPaymentId');
          if (pidEl) pidEl.textContent = id;
          var form = document.getElementById('refundForm');
          if (form) {
            form.action = '<?= BASE_URL ?>?r=payments_refund&id=' + encodeURIComponent(id);
          }
          var modalEl = document.getElementById('refundModal');
          if (modalEl) {
            var m = new bootstrap.Modal(modalEl);
            m.show();
          }
        });
      });
    })();
    </script>
    </body>
    </html>
