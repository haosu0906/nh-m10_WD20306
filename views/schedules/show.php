<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Chi tiết Lịch khởi hành</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    
    .card{ border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.05) }
    .muted{ color:#6b7280 }
    .value{ font-weight:600; color:#1f2937 }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='schedules'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Chi tiết Lịch khởi hành</h3>
                <div class="d-flex gap-2">
                    <a href="<?= BASE_URL ?>?r=tour_manifest&departure_id=<?= (int)$schedule['id'] ?>" class="btn btn-secondary">
                        <i class="fas fa-clipboard-list me-1"></i> Danh sách đoàn
                    </a>
                    <a href="<?= BASE_URL ?>?r=qr_scan&departure_id=<?= (int)$schedule['id'] ?>" class="btn btn-outline-primary">
                        <i class="fas fa-qrcode me-1"></i> Quét QR
                    </a>
                    <a href="<?= BASE_URL ?>?r=schedules_edit&id=<?= (int)$schedule['id'] ?>" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-1"></i> Sửa
                    </a>
                    <a href="<?= BASE_URL ?>?r=schedules" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card border-0">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="muted">Tour</div>
                            <div class="value"><i class="fas fa-route me-1"></i> <?= htmlspecialchars($schedule['tour_title'] ?? ('#'.$schedule['tour_id'])) ?></div>
                            <div class="muted">Tour ID: <?= (int)$schedule['tour_id'] ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="muted">Ngày đi</div>
                            <div class="value"><i class="fas fa-plane-departure me-1"></i> <?= htmlspecialchars($schedule['start_date']) ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="muted">Ngày về</div>
                            <div class="value"><i class="fas fa-plane-arrival me-1"></i> <?= htmlspecialchars($schedule['end_date']) ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="muted">Hướng dẫn viên</div>
                            <?php if (!empty($schedule['guide_name'])): ?>
                                <div class="value"><i class="fas fa-user-tie me-1"></i> <?= htmlspecialchars($schedule['guide_name']) ?></div>
                            <?php else: ?>
                                <div class="muted"><i class="fas fa-times me-1"></i> Chưa gán</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <div class="muted">Tài xế</div>
                            <div class="value">ID: <?= htmlspecialchars($schedule['driver_user_id'] ?? '—') ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="muted">Sức chứa</div>
                            <div class="value"><i class="fas fa-users me-1"></i> <?= (int)($schedule['max_capacity'] ?? 0) ?> người</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
</html>
