<?php
require_once __DIR__ . '/../../assets/configs/env.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Chi Tiết Phân Công HDV — Hệ thống Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: Inter, Segoe UI, Arial;
        background: #f5f7fb;
        margin: 0;
        color: #222;
    }

    .sidebar {}

    .sidebar h3 {
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        font-size: 16px;
    }

    .nav-link {
        color: rgba(255, 255, 255, .95) !important;
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .6rem;
        border-radius: .5rem;
        text-decoration: none;
        font-weight: 500;
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, .1) !important;
    }

    .nav-link i {
        color: rgba(255, 255, 255, .95) !important;
    }

    .sidebar h3 {
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        font-size: 16px;
        color: #fff !important;
    }

    
    </style>
</head>
<body>
    <?php $current_page='guide_assignments'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h1 class="mb-2">👁️ Chi Tiết Phân Công HDV</h1>
                <p class="text-muted mb-0">Xem thông tin chi tiết phân công hướng dẫn viên</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>?r=guide_assignments" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
                <a href="<?= BASE_URL ?>?r=guide_assignments_edit&id=<?= $assignment['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Sửa
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Thông tin Tour -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">📍 Thông tin Tour</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Tên Tour</label>
                            <div class="fw-semibold"><?= htmlspecialchars($assignment['tour_title'] ?? 'Chưa có tên tour') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">ID Tour</label>
                            <div class="fw-semibold">#<?= htmlspecialchars($assignment['tour_id'] ?? 'N/A') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Ngày phân công</label>
                            <div class="fw-semibold"><?= date('d/m/Y', strtotime($assignment['assignment_date'] ?? 'now')) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Loại phân công</label>
                            <div class="fw-semibold">
                                <?php
                                $types = [
                                    'main_guide' => 'HDV chính',
                                    'assistant_guide' => 'HDV phụ',
                                    'tour_leader' => 'Trưởng đoàn'
                                ];
                                echo $types[$assignment['assignment_type']] ?? 'Không xác định';
                                ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Trạng thái</label>
                            <div>
                                <?php
                                $statusColors = [
                                    'pending' => ['bg-warning', 'fa-clock', 'Chờ xác nhận'],
                                    'confirmed' => ['bg-success', 'fa-check-circle', 'Đã xác nhận'],
                                    'completed' => ['bg-primary', 'fa-check', 'Đã hoàn thành'],
                                    'cancelled' => ['bg-danger', 'fa-times-circle', 'Đã hủy']
                                ];
                                $status = $assignment['status'] ?? 'pending';
                                $info = $statusColors[$status] ?? ['bg-secondary', 'fa-question', 'Chưa rõ'];
                                ?>
                                <span class="badge <?= $info[0] ?>">
                                    <i class="fas <?= $info[1] ?> me-1"></i>
                                    <?= $info[2] ?>
                                </span>
                            </div>
                        </div>
                        <?php if(!empty($assignment['notes'])): ?>
                        <div class="mb-3">
                            <label class="text-muted small">Ghi chú</label>
                            <div class="fw-semibold"><?= htmlspecialchars($assignment['notes']) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Thông tin HDV -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">👤 Thông tin HDV</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Họ và tên</label>
                            <div class="fw-semibold"><?= htmlspecialchars($assignment['guide_name'] ?? 'Chưa có tên HDV') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Email</label>
                            <div class="fw-semibold"><?= htmlspecialchars($assignment['guide_email'] ?? 'Chưa có email') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">SĐT</label>
                            <div class="fw-semibold"><?= htmlspecialchars($assignment['guide_phone'] ?? 'Chưa có SĐT') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Kinh nghiệm</label>
                            <div class="fw-semibold"><?= htmlspecialchars($assignment['experience_years'] ?? '0') ?> năm</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Tuyến chuyên</label>
                            <div class="fw-semibold"><?= htmlspecialchars($assignment['specialized_route'] ?? 'Chưa có') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Sức khỏe</label>
                            <div class="fw-semibold"><?= htmlspecialchars($assignment['health_status'] ?? 'Chưa có') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
