<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Đánh giá HDV — Hệ thống Tour</title>
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
            <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-layer-group"></i> Danh mục tour</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=payments"><i class="fas fa-credit-card"></i> Thanh toán</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=cancellation_policies"><i class="fas fa-ban"></i> Chính sách hủy</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_schedules"><i class="fas fa-calendar-alt"></i> Lịch HDV</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=guide_ratings"><i class="fas fa-star"></i> Đánh giá HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_login">
                <i class="fas fa-door-open"></i> Portal HDV
            </a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=admin_login">
                <i class="fas fa-user-shield"></i> Đăng nhập Admin
            </a>
        </nav>
    </div>

    <main class="main">
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h1 class="mb-2">⭐ Đánh Giá HDV</h1>
                <p class="text-muted mb-0">Quản lý đánh giá và phản hồi từ khách hàng</p>
            </div>
            <div>
                <a class="btn btn-success" href="<?= BASE_URL ?>?r=guide_ratings_create">
                    <i class="fas fa-plus me-2"></i>Thêm đánh giá
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 bg-gradient-primary text-white slide-in-left">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Tổng đánh giá</h6>
                                <h3 class="mb-0"><?= count($ratings ?? []) ?></h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-star fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-arrow-up me-1"></i>
                                +12 đánh giá mới
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-gradient-success text-white slide-in-left" style="animation-delay: 0.1s;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Điểm trung bình</h6>
                                <h3 class="mb-0">4.8</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-trophy me-1"></i>
                                Rất tốt
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-gradient-warning text-white slide-in-left" style="animation-delay: 0.2s;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">HDV xuất sắc</h6>
                                <h3 class="mb-0">8</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-award fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-medal me-1"></i>
                                5.0 sao
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Guides -->
        <?php if (!empty($topGuides)): ?>
        <div class="card border-0 shadow-sm mb-4 fade-in" style="animation-delay: 0.3s;">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">🏆 HDV Xuất Sắc</h5>
            </div>
            <div class="card-body p-0">
                <div class="row g-0">
                    <?php foreach (array_slice($topGuides, 0, 3) as $index => $guide): ?>
                    <div class="col-md-4 border-end">
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box-primary me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user-tie text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($guide['full_name']) ?></div>
                                        <small class="text-muted"><?= $guide['rating_count'] ?> đánh giá</small>
                                    </div>
                                </div>
                                <div class="badge bg-warning text-dark">
                                    <i class="fas fa-star me-1"></i>
                                    <?= number_format($guide['avg_rating'], 1) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ratings Table -->
        <div class="card border-0 shadow-sm fade-in">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">📋 Danh sách đánh giá</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">HDV</th>
                                <th class="border-0">Khách hàng</th>
                                <th class="border-0">Điểm</th>
                                <th class="border-0">Nhận xét</th>
                                <th class="border-0">Ngày</th>
                                <th class="border-0 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($ratings)): foreach($ratings as $r): ?>
                            <tr class="hover-lift">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box-primary me-2" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user-tie text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($r['guide_name'] ?? 'N/A') ?></div>
                                            <small class="text-muted">ID: <?= htmlspecialchars($r['guide_user_id'] ?? 'N/A') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($r['rater_name'] ?? 'Khách hàng') ?></div>
                                        <small class="text-muted"><?= $r['rater_type'] ?? 'customer' ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= $r['rating'] ? ' text-warning' : ' text-muted' ?>" style="font-size: 12px;"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="badge bg-warning text-dark"><?= $r['rating'] ?>/5</span>
                                    </div>
                                    <?php if (!empty($r['comment'])): ?>
                                    <small class="text-muted d-block mt-1"><?= htmlspecialchars(mb_substr($r['comment'], 0, 50)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($r['tour_name'] ?? 'N/A') ?></div>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($r['rating_date'] ?? 'now')) ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= BASE_URL ?>?r=guide_ratings_show&id=<?= $r['id'] ?>" 
                                           class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?r=guide_ratings_edit&id=<?= $r['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?r=guide_ratings_delete&id=<?= $r['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Xác nhận xóa đánh giá này?')" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-star fa-3x mb-3 opacity-50"></i>
                                        <h5>Chưa có đánh giá nào</h5>
                                        <p>Bắt đầu bằng cách thêm đánh giá đầu tiên</p>
                                        <a href="<?= BASE_URL ?>?r=guide_ratings_create" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Thêm đánh giá
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if(!empty($ratings)): ?>
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Hiển thị <?= count($ratings) ?> đánh giá</small>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download me-1"></i> Xuất Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash me-1"></i> Xóa đã chọn
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>
