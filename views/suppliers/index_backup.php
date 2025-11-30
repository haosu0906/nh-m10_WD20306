<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Nhà cung cấp — Hệ thống Tour</title>
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
            <a class="nav-link active" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=payments"><i class="fas fa-credit-card"></i> Thanh toán</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=cancellation_policies"><i class="fas fa-ban"></i> Chính sách hủy</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
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
                <h1 class="mb-2">🤝 Nhà Cung Cấp Dịch Vụ</h1>
                <p class="text-muted mb-0">Quản lý đối tác (khách sạn, nhà hàng, vận chuyển...)</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>?r=suppliers_create" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp
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
                                <h6 class="card-title mb-1">Tổng NCC</h6>
                                <h3 class="mb-0"><?= count($suppliers ?? []) ?></h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-handshake fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-arrow-up me-1"></i>
                                +5 đối tác mới
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-gradient-info text-white slide-in-left" style="animation-delay: 0.1s;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Khách sạn</h6>
                                <h3 class="mb-0">12</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-hotel fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-star me-1"></i>
                                4.5★ trung bình
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
                                <h6 class="card-title mb-1">Vận chuyển</h6>
                                <h3 class="mb-0">8</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-bus fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-check me-1"></i>
                                Đã xác thực
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suppliers Table -->
        <div class="card border-0 shadow-sm fade-in">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">📋 Danh sách nhà cung cấp</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">
                                    <input type="checkbox" class="form-check-input">
                                </th>
                                <th class="border-0">Nhà cung cấp</th>
                                <th class="border-0">Người liên hệ</th>
                                <th class="border-0">Loại dịch vụ</th>
                                <th class="border-0">Liên hệ</th>
                                <th class="border-0 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($suppliers)): foreach ($suppliers as $s): ?>
                            <tr class="hover-lift">
                                <td>
                                    <input type="checkbox" class="form-check-input">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box-primary me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-building text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars((string)$s['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                            <small class="text-muted">ID: <?= (int)$s['id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box-primary me-2" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars((string)$s['contact_person'], ENT_QUOTES, 'UTF-8') ?></div>
                                            <small class="text-muted"><?= htmlspecialchars((string)($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $map = [
                                        'hotel' => ['Khách sạn', 'fa-hotel', 'bg-primary'],
                                        'restaurant' => ['Nhà hàng', 'fa-utensils', 'bg-success'],
                                        'transport' => ['Vận chuyển', 'fa-bus', 'bg-warning'],
                                        'ticket' => ['Vé tham quan', 'fa-ticket-alt', 'bg-info'],
                                        'insurance' => ['Bảo hiểm', 'fa-shield-alt', 'bg-secondary'],
                                        'guide' => ['HDV', 'fa-user-tie', 'bg-dark'],
                                        'meal' => ['Ăn uống', 'fa-coffee', 'bg-danger'],
                                        'entertain' => ['Giải trí', 'fa-music', 'bg-purple'],
                                        'other' => ['Dịch vụ khác', 'fa-cogs', 'bg-light']
                                    ];
                                    $st = $s['service_type'] ?? 'other';
                                    $serviceInfo = $map[$st] ?? $map['other'];
                                    ?>
                                    <span class="badge <?= $serviceInfo[2] ?>">
                                        <i class="fas <?= $serviceInfo[1] ?> me-1"></i>
                                        <?= htmlspecialchars((string)$serviceInfo[0], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="fw-semibold">
                                            <i class="fas fa-phone text-success me-1"></i>
                                            <?= htmlspecialchars((string)$s['phone'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <?php if (!empty($s['address'])): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?= htmlspecialchars(substr((string)$s['address'], 0, 30)) ?>...
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= BASE_URL ?>?r=suppliers_show&id=<?= (int)$s['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?r=suppliers_edit&id=<?= (int)$s['id'] ?>" 
                                           class="btn btn-sm btn-outline-info" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?r=suppliers_delete&id=<?= (int)$s['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Xác nhận xóa nhà cung cấp này?')" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-handshake fa-3x mb-3 opacity-50"></i>
                                        <h5>Chưa có nhà cung cấp nào</h5>
                                        <p>Bắt đầu bằng cách thêm nhà cung cấp đầu tiên</p>
                                        <a href="<?= BASE_URL ?>?r=suppliers_create" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if(!empty($suppliers)): ?>
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Hiển thị <?= count($suppliers) ?> nhà cung cấp</small>
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
