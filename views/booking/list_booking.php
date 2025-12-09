<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Quản lý Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root { --accent:#667eea; --accent-dark:#5568d3 }
    body { background:#f8f9fa }
    .sidebar{ }
    .sidebar h3{ font-weight:700; margin-bottom:1rem; text-align:center; font-size:16px }
    .nav-link{ color:rgba(255,255,255,.95); display:flex; align-items:center; gap:.6rem; padding:.6rem; border-radius:.5rem; text-decoration:none }
    .nav-link:hover,.nav-link.active{ background:rgba(255,255,255,.1) }
    .main{ margin-left:200px; padding:86px 22px 22px }

    .bookings-grid-container{ background:white; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.05); overflow:hidden }
    .bookings-grid-header{ display:grid; grid-template-columns:40px minmax(220px,1fr) minmax(320px,2fr) 150px 120px 160px 120px; background:#fff; color:#0f172a; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:.875rem; text-transform:uppercase; letter-spacing:.5px }
    .bookings-grid-header-cell{ padding:16px 12px; border-right:1px solid rgba(255,255,255,.1); display:flex; align-items:center; justify-content:flex-start }
    .bookings-grid-header-cell:last-child{ border-right:none }
    .bookings-grid-body{ max-height:600px; overflow-y:auto }
    .bookings-grid-row{ display:grid; grid-template-columns:40px minmax(220px,1fr) minmax(320px,2fr) 150px 120px 160px 120px; border:1px solid #e5e7eb; border-radius:10px; margin-bottom:6px; background:#fff; transition:background-color .2s }
    .bookings-grid-row:hover{ background-color:#f8fafc }
    .bookings-grid-cell{ padding:18px 14px; border-right:1px solid #e5e7eb; display:flex; align-items:center; font-size:.9rem; min-height:78px }
    .bookings-grid-cell:last-child{ border-right:none }
    .bookings-checkbox-cell{ justify-content:center }
    .bookings-customer-cell{ gap:12px }
    .bookings-customer-icon{ width:40px; height:40px; border-radius:8px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#0f172a }
    .bookings-customer-info{ flex:1 }
    .bookings-customer-name{ font-weight:600; color:#1f2937; margin-bottom:4px }
    .bookings-customer-sub{ color:#6b7280; font-size:.8rem }
    .bookings-tour-cell{ gap:12px }
    .bookings-tour-title{ font-weight:600; color:#1f2937; margin-bottom:4px }
    .bookings-tour-sub{ color:#6b7280; font-size:.8rem }
    .bookings-date-cell{ flex-direction:column; align-items:center; gap:4px }
    .bookings-date-value{ font-weight:600; color:#1f2937 }
    .bookings-date-label{ color:#6b7280; font-size:.8rem }
    .bookings-guests-cell{ justify-content:center; gap:8px }
    .bookings-status-cell{ justify-content:center }
    .bookings-actions-cell{ justify-content:center; gap:6px }
    .bookings-actions-cell .btn{ padding:6px 8px; font-size:.75rem; border-radius:6px }
    .bookings-grid-empty{ text-align:center; padding:60px 20px; color:#6b7280 }
    @media (max-width:1200px){
        .bookings-grid-header,.bookings-grid-row{ grid-template-columns:40px 180px 200px 120px 100px 120px 100px }
        .bookings-grid-cell{ font-size:.8rem; padding:12px 8px }
    }
    </style>
</head>

<body>
    <!-- Sidebar (include standard template) -->
    <?php
        $current_page = 'booking';
        require_once __DIR__ . '/../../assets/templates/sidebar.php';
    ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>

    <!-- Main content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h1 class="mb-2">📋 Quản lý Booking</h1>
                <p class="text-muted mb-0">Theo dõi và quản lý tình trạng booking</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>?r=booking_create" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Tạo booking mới
                </a>
            </div>
        </div>

        <div class="bookings-grid-container fade-in">
            <div class="bookings-grid-header">
                <div class="bookings-grid-header-cell"><input type="checkbox" class="form-check-input"></div>
                <div class="bookings-grid-header-cell">Khách hàng</div>
                <div class="bookings-grid-header-cell">Tour</div>
                <div class="bookings-grid-header-cell">Ngày đặt</div>
                <div class="bookings-grid-header-cell">Số khách</div>
                <div class="bookings-grid-header-cell">Trạng thái</div>
                <div class="bookings-grid-header-cell">Hành động</div>
            </div>
            <div class="bookings-grid-body">
                <?php if(!empty($items)): foreach($items as $b): ?>
                <div class="bookings-grid-row">
                    <div class="bookings-grid-cell bookings-checkbox-cell"><input type="checkbox" class="form-check-input"></div>
                    <div class="bookings-grid-cell bookings-customer-cell">
                        <div class="bookings-customer-icon"><i class="fas fa-user"></i></div>
                        <div class="bookings-customer-info">
                            <div class="bookings-customer-name"><?= htmlspecialchars($b['customer_name'] ?? 'N/A') ?></div>
                            <div class="bookings-customer-sub">ID: <?= (int)$b['id'] ?></div>
                        </div>
                    </div>
                    <div class="bookings-grid-cell bookings-tour-cell">
                        <div class="bookings-tour-info">
                            <div class="bookings-tour-title"><?= htmlspecialchars($b['tour_name'] ?? 'N/A') ?></div>
                            <div class="bookings-tour-sub">Tour ID: <?= (int)($b['tour_id'] ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="bookings-grid-cell bookings-date-cell">
                        <div class="bookings-date-value"><?= !empty($b['date_booked']) ? date('d/m/Y H:i', strtotime($b['date_booked'])) : '---' ?></div>
                        <div class="bookings-date-label">Ngày đặt</div>
                    </div>
                    <div class="bookings-grid-cell bookings-guests-cell">
                        <div class="text-center">
                            <div class="fw-bold"><?= (int)($b['total_guests'] ?? 0) ?></div>
                            <div class="text-muted" style="font-size:.8rem">người</div>
                            <?php $tc = $typeCountsByBooking[(int)$b['id']] ?? null; if ($tc): ?>
                            <div class="mt-1" style="font-size:.75rem">
                                <span class="badge bg-light text-dark border">NL: <?= (int)($tc['adult'] ?? 0) ?></span>
                                <span class="badge bg-light text-dark border ms-1">TE: <?= (int)($tc['child'] ?? 0) ?></span>
                                <span class="badge bg-light text-dark border ms-1">EB: <?= (int)($tc['infant'] ?? 0) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="bookings-grid-cell bookings-status-cell">
                        <?php
                            $statusKey = $b['booking_status'] ?? 'pending';
                            $statusMap = [
                                'pending' => ['text' => 'Chờ xác nhận', 'cls' => 'badge bg-warning'],
                                'deposit' => ['text' => 'Đã cọc', 'cls' => 'badge bg-info'],
                                'completed' => ['text' => 'Hoàn tất', 'cls' => 'badge bg-success'],
                                'canceled' => ['text' => 'Hủy', 'cls' => 'badge bg-danger']
                            ];
                            $st = $statusMap[$statusKey] ?? ['text' => $statusKey, 'cls' => 'badge bg-secondary'];
                        ?>
                        <span class="<?= $st['cls'] ?>"><?= $st['text'] ?></span>
                    </div>
                    <div class="bookings-grid-cell bookings-actions-cell">
                        <div class="btn-group" role="group">
                            <a href="<?= BASE_URL ?>?r=booking_detail&id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-outline-info" title="Chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if (!empty($b['schedule_id'])): ?>
                            <a href="<?= BASE_URL ?>?r=tour_manifest&departure_id=<?= (int)$b['schedule_id'] ?>" class="btn btn-sm btn-outline-secondary" title="Danh sách đoàn (Chuyến)">
                                <i class="fas fa-clipboard-check"></i>
                            </a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>?r=booking_edit&id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-outline-primary" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= BASE_URL ?>?r=booking_delete&id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận xóa booking này?')" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; else: ?>
                <div class="bookings-grid-empty">
                    <div class="text-muted">
                        <i class="fas fa-book fa-3x mb-3 opacity-50"></i>
                        <h5>Chưa có booking nào</h5>
                        <p>Bắt đầu bằng cách tạo booking đầu tiên</p>
                        <a href="<?= BASE_URL ?>?r=booking_create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo booking mới
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if(!empty($items)): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Hiển thị <?= count($items) ?> booking</small>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download me-1"></i> Xuất Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa các booking đã chọn?')">
                            <i class="fas fa-trash me-1"></i> Xóa đã chọn
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
