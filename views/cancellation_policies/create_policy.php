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
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3;
    }

    body {
        background: #f8f9fa;
    }

    .sidebar {}

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

    </style>
</head>

<body>
    <?php $current_page='cancellation_policies'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
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
    </div>
</body>

</html>
