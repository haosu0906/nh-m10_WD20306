<?php
require_once __DIR__ . '/../../assets/configs/env.php';
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Thêm Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3;
    }

    body {
        background: #f8f9fa;
    }

    

    .sidebar h3 {
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        font-size: 16px;
    }

    
    </style>
</head>

<body>
    <?php $current_page='payments'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="sidebar-old" style="display:none">
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
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân sự</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=payments"><i class="fas fa-credit-card"></i> Thanh toán</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_login">
                <i class="fas fa-door-open"></i> Portal HDV
            </a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=admin_login">
                <i class="fas fa-user-shield"></i> Đăng nhập Admin
            </a>
        </nav>
    </div>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Thêm Thanh toán</h3>
            <a href="<?= BASE_URL ?>?r=payments" class="btn btn-outline-secondary">Quay lại</a>
        </div>

        <form method="post" action="<?= BASE_URL ?>?r=payments_store">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">Thông tin thanh toán</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Booking</label>
                            <select class="form-select form-select-lg" name="booking_id" required>
                                <option value="">-- Chọn booking --</option>
                                <?php foreach ($bookings as $b): ?>
                                <option value="<?= $b['id'] ?>" <?= (isset($payment) && (int)$payment['id'] === (int)$b['id']) ? 'selected' : '' ?>>
                                    BK<?= $b['booking_code'] ?> - <?= htmlspecialchars($b['tour_title']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số tiền (VND)</label>
                            <input type="number" class="form-control form-control-lg" name="amount" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phương thức thanh toán</label>
                            <select class="form-select form-select-lg" name="payment_method" required>
                                <option value="cash">Tiền mặt</option>
                                <option value="bank_transfer">Chuyển khoản</option>
                                <option value="credit_card">Thẻ tín dụng</option>
                                <option value="momo">MoMo</option>
                                <option value="zalopay">ZaloPay</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mã giao dịch (nếu có)</label>
                            <input type="text" class="form-control form-control-lg" name="transaction_id">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày thanh toán</label>
                            <input type="datetime-local" class="form-control form-control-lg" name="payment_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select class="form-select form-select-lg" name="status" required>
                                <option value="pending">Chờ xử lý</option>
                                <option value="completed">Hoàn tất</option>
                                <option value="failed">Thất bại</option>
                                <option value="refunded">Đã hoàn</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Ghi chú</label>
                            <textarea class="form-control form-control-lg" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary btn-lg">Lưu thanh toán</button>
                    <a href="<?= BASE_URL ?>?r=payments" class="btn btn-outline-secondary btn-lg">Hủy</a>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
