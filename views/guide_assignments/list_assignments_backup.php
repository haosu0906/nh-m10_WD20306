<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Phân công HDV — Hệ thống Tour</title>
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
            <a class="nav-link active" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_schedules"><i class="fas fa-calendar-alt"></i> Lịch HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_ratings"><i class="fas fa-star"></i> Đánh giá HDV</a>
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
                <h1 class="mb-2">👥 Phân Công HDV</h1>
                <p class="text-muted mb-0">Quản lý phân công hướng dẫn viên cho các tour</p>
            </div>
            <div>
                <a class="btn btn-success" href="<?= BASE_URL ?>?r=guide_assignments_create">
                    <i class="fas fa-plus me-2"></i>Thêm phân công
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
                                <h6 class="card-title mb-1">Tổng phân công</h6>
                                <h3 class="mb-0"><?= count($assignments ?? []) ?></h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-user-check fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-arrow-up me-1"></i>
                                +5 phân công mới
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
                                <h6 class="card-title mb-1">Đã xác nhận</h6>
                                <h3 class="mb-0">12</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-check me-1"></i>
                                Sẵn sàng thực hiện
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
                                <h6 class="card-title mb-1">Chờ xác nhận</h6>
                                <h3 class="mb-0">8</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-hourglass-half me-1"></i>
                                Đang chờ xử lý
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="card border-0 shadow-sm fade-in">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">📋 Danh sách phân công</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">ID</th>
                                <th class="border-0">Tour</th>
                                <th class="border-0">HDV</th>
                                <th class="border-0">Ngày</th>
                                <th class="border-0">Trạng thái</th>
                                <th class="border-0 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($assignments)): foreach($assignments as $assignment): ?>
                            <tr class="hover-lift">
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($assignment['id']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($assignment['tour_name'] ?? 'N/A') ?></div>
                                    <small class="text-muted">Mã: <?= htmlspecialchars($assignment['tour_code'] ?? 'N/A') ?></small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box-primary me-2" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user-tie text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($assignment['guide_name'] ?? 'N/A') ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($assignment['guide_phone'] ?? 'N/A') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= date('d/m/Y', strtotime($assignment['assignment_date'] ?? 'now')) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($assignment['start_time'] ?? 'now')) ?></small>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'pending' => ['bg-warning', 'fa-clock', 'Chờ xác nhận'],
                                        'confirmed' => ['bg-success', 'fa-check-circle', 'Đã xác nhận'],
                                        'completed' => ['bg-primary', 'fa-check', 'Đã hoàn thành'],
                                        'cancelled' => ['bg-danger', 'fa-times-circle', 'Đã hủy']
                                    ];
                                    $status = $assignment['assignment_status'] ?? 'pending';
                                    $info = $statusColors[$status] ?? ['bg-secondary', 'fa-question', 'Unknown'];
                                    ?>
                                    <span class="badge <?= $info[0] ?>">
                                        <i class="fas <?= $info[1] ?> me-1"></i>
                                        <?= $info[2] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= BASE_URL ?>?r=guide_assignments_edit&id=<?= $assignment['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?r=guide_assignments_show&id=<?= $assignment['id'] ?>" 
                                           class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?r=guide_assignments_delete&id=<?= $assignment['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Xác nhận xóa phân công này?')" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-user-check fa-3x mb-3 opacity-50"></i>
                                        <h5>Chưa có phân công nào</h5>
                                        <p>Bắt đầu bằng cách thêm phân công đầu tiên</p>
                                        <a href="<?= BASE_URL ?>?r=guide_assignments_create" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Thêm phân công
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if(!empty($assignments)): ?>
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Hiển thị <?= count($assignments) ?> phân công</small>
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
