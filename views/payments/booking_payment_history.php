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
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3;
    }

    body {
        background: #f8f9fa;
    }

    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        width: 200px;
        padding: 20px;
        background: linear-gradient(180deg, var(--accent), #764ba2);
        color: #fff;
        overflow: auto;
    }

    .sidebar h3 {
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        font-size: 16px;
    }

    .nav-link {
        color: rgba(255, 255, 255, .95);
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .6rem;
        border-radius: .5rem;
        text-decoration: none;
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, .1);
    }

    .main {
        margin-left: 200px;
        padding: 22px;
    }

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
    <div class="sidebar">
        <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_schedules"><i class="fas fa-calendar-alt"></i> Lịch HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_ratings"><i class="fas fa-star"></i> Đánh giá HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=payments"><i class="fas fa-credit-card"></i> Thanh toán</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_login">
                <i class="fas fa-door-open"></i> Portal HDV
            </a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=admin_login">
                <i class="fas fa-user-shield"></i> Đăng nhập Admin
            </a>
        </nav>
    </div>

    <main class="main">
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
                                                <a href="<?= BASE_URL ?>?r=payments_delete&id=<?= $payment['id'] ?>" 
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
    </main>
</body>

</html>
