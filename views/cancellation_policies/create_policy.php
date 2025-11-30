<?php
require_once __DIR__ . '/../../assets/configs/env.php';
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Thêm Chính sách hủy tour</title>
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
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân sự</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=payments"><i class="fas fa-credit-card"></i> Thanh toán</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=cancellation_policies"><i class="fas fa-ban"></i> Chính sách hủy</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_login">
                <i class="fas fa-door-open"></i> Portal HDV
            </a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=admin_login">
                <i class="fas fa-user-shield"></i> Đăng nhập Admin
            </a>
        </nav>
    </div>

    <main class="main">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Thêm Chính sách hủy tour</h3>
            <a href="<?= BASE_URL ?>?r=cancellation_policies" class="btn btn-outline-secondary">Quay lại</a>
        </div>

        <form method="post" action="<?= BASE_URL ?>?r=cancellation_policies_store">
            <div class="card">
                <div class="card-header fw-semibold">Thông tin chính sách</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tour áp dụng</label>
                            <select class="form-select" name="tour_id" required>
                                <option value="">-- Chọn tour --</option>
                                <?php foreach ($tours as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (isset($policy) && (int)$policy['id'] === (int)$t['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['title']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tên chính sách</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số ngày trước khởi hành</label>
                            <input type="number" class="form-control" name="days_before" min="0" required>
                            <small class="text-muted">Số ngày tối thiểu trước ngày đi để áp dụng</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">% Hoàn tiền</label>
                            <input type="number" class="form-control" name="refund_percentage" min="0" max="100" step="0.01" required>
                            <small class="text-muted">Phần trăm hoàn tiền (0-100)</small>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Hoạt động
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Lưu chính sách</button>
                    <a href="<?= BASE_URL ?>?r=cancellation_policies" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </div>
        </form>
    </main>
</body>

</html>
