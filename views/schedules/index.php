<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Quản lý lịch khởi hành</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3
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
        overflow: auto
    }

    .sidebar h3 {
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        font-size: 16px
    }

    .nav-link {
        color: rgba(255, 255, 255, .95);
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .6rem;
        border-radius: .5rem;
        text-decoration: none
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, .1)
    }

    .main {
        margin-left: 200px;
        padding: 22px
    }

    /* CSS Grid Layout for Schedules Table */
    .schedules-grid-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .schedules-grid-header {
        display: grid;
        grid-template-columns: 40px 200px 120px 120px 150px 120px 100px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .schedules-grid-header-cell {
        padding: 16px 12px;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .schedules-grid-header-cell:last-child {
        border-right: none;
    }

    .schedules-grid-body {
        max-height: 600px;
        overflow-y: auto;
    }

    .schedules-grid-row {
        display: grid;
        grid-template-columns: 40px 200px 120px 120px 150px 120px 100px;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }

    .schedules-grid-row:hover {
        background-color: #f8fafc;
    }

    .schedules-grid-cell {
        padding: 16px 12px;
        border-right: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        min-height: 80px;
    }

    .schedules-grid-cell:last-child {
        border-right: none;
    }

    /* Checkbox cell */
    .schedules-checkbox-cell {
        justify-content: center;
    }

    /* Tour cell */
    .schedules-tour-cell {
        gap: 12px;
    }

    .schedules-tour-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea20, #764ba220);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
    }

    .schedules-tour-info {
        flex: 1;
    }

    .schedules-tour-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .schedules-tour-id {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Date cells */
    .schedules-date-cell {
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .schedules-date-value {
        font-weight: 600;
        color: #1f2937;
    }

    .schedules-date-label {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Guide cell */
    .schedules-guide-cell {
        justify-content: flex-start;
    }

    .schedules-guide-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .schedules-guide-empty {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Capacity cell */
    .schedules-capacity-cell {
        justify-content: center;
        gap: 8px;
    }

    .schedules-capacity-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: linear-gradient(135deg, #3b82f620, #1d4ed820);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
    }

    .schedules-capacity-info {
        text-align: center;
    }

    .schedules-capacity-value {
        font-weight: 700;
        color: #1f2937;
        font-size: 1rem;
    }

    .schedules-capacity-label {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Actions cell */
    .schedules-actions-cell {
        justify-content: center;
        gap: 6px;
    }

    .schedules-actions-cell .btn {
        padding: 6px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    /* Empty state */
    .schedules-grid-empty {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    /* Responsive */
    @media (max-width: 1400px) {

        .schedules-grid-header,
        .schedules-grid-row {
            grid-template-columns: 40px 180px 100px 100px 130px 100px 90px;
        }
    }

    @media (max-width: 1200px) {

        .schedules-grid-header,
        .schedules-grid-row {
            grid-template-columns: 40px 160px 90px 90px 110px 90px 80px;
        }

        .schedules-grid-cell {
            font-size: 0.8rem;
            padding: 12px 8px;
        }
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
            <a class="nav-link active" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi
                hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công
                HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_schedules"><i class="fas fa-calendar-alt"></i> Lịch
                HDV</a>
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
                <h1 class="mb-2">📅 Lịch Khởi Hành</h1>
                <p class="text-muted mb-0">Quản lý lịch trình, gán HDV và sức chứa tour</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=schedules_calendar">
                    <i class="fas fa-calendar me-2"></i>Xem calendar
                </a>
                <a class="btn btn-success" href="<?= BASE_URL ?>?r=schedules_create">
                    <i class="fas fa-plus me-2"></i>Thêm lịch
                </a>
            </div>
        </div>

        <!-- Schedules Grid Table -->
        <div class="schedules-grid-container fade-in">
            <!-- Grid Header -->
            <div class="schedules-grid-header">
                <div class="schedules-grid-header-cell">
                    <input type="checkbox" class="form-check-input">
                </div>
                <div class="schedules-grid-header-cell">Tour</div>
                <div class="schedules-grid-header-cell">Ngày đi</div>
                <div class="schedules-grid-header-cell">Ngày về</div>
                <div class="schedules-grid-header-cell">HDV</div>
                <div class="schedules-grid-header-cell">Sức chứa</div>
                <div class="schedules-grid-header-cell">Hành động</div>
            </div>

            <!-- Grid Body -->
            <div class="schedules-grid-body">
                <?php if(!empty($schedules)): foreach($schedules as $row): ?>
                <div class="schedules-grid-row">
                    <!-- Checkbox Cell -->
                    <div class="schedules-grid-cell schedules-checkbox-cell">
                        <input type="checkbox" class="form-check-input">
                    </div>

                    <!-- Tour Cell -->
                    <div class="schedules-grid-cell schedules-tour-cell">
                        <div class="schedules-tour-icon">
                            <i class="fas fa-route"></i>
                        </div>
                        <div class="schedules-tour-info">
                            <div class="schedules-tour-name">
                                <?= htmlspecialchars($row['tour_title'] ?? ('#'.$row['tour_id'])) ?></div>
                            <div class="schedules-tour-id">Tour ID: <?= (int)$row['tour_id'] ?></div>
                        </div>
                    </div>

                    <!-- Start Date Cell -->
                    <div class="schedules-grid-cell schedules-date-cell">
                        <div class="schedules-date-value"><?= htmlspecialchars($row['start_date']) ?></div>
                        <div class="schedules-date-label">Khởi hành</div>
                    </div>

                    <!-- End Date Cell -->
                    <div class="schedules-grid-cell schedules-date-cell">
                        <div class="schedules-date-value"><?= htmlspecialchars($row['end_date']) ?></div>
                        <div class="schedules-date-label">Kết thúc</div>
                    </div>

                    <!-- Guide Cell -->
                    <div class="schedules-grid-cell schedules-guide-cell">
                        <?php if (!empty($row['guide_name'])): ?>
                        <span class="schedules-guide-badge">
                            <i class="fas fa-user-tie me-1"></i>
                            <?= htmlspecialchars($row['guide_name']) ?>
                        </span>
                        <?php else: ?>
                        <div class="schedules-guide-empty">
                            <i class="fas fa-times me-1"></i>
                            Chưa gán
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Capacity Cell -->
                    <div class="schedules-grid-cell schedules-capacity-cell">
                        <div class="schedules-capacity-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="schedules-capacity-info">
                            <div class="schedules-capacity-value"><?= (int)($row['max_capacity'] ?? 0) ?></div>
                            <div class="schedules-capacity-label">Hành khách</div>
                        </div>
                    </div>

                    <!-- Actions Cell -->
                    <div class="schedules-grid-cell schedules-actions-cell">
                        <div class="btn-group" role="group">
                            <a href="<?= BASE_URL ?>?r=schedules_edit&id=<?= (int)$row['id'] ?>"
                                class="btn btn-sm btn-outline-primary" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= BASE_URL ?>?r=schedules_show&id=<?= (int)$row['id'] ?>"
                                class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= BASE_URL ?>?r=schedules_delete&id=<?= (int)$row['id'] ?>"
                                class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận xóa lịch này?')"
                                title="Xóa">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; else: ?>
                <div class="schedules-grid-empty">
                    <div class="text-muted">
                        <i class="fas fa-calendar fa-3x mb-3 opacity-50"></i>
                        <h5>Chưa có lịch trình nào</h5>
                        <p>Bắt đầu bằng cách tạo lịch trình đầu tiên</p>
                        <a href="<?= BASE_URL ?>?r=schedules_create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm lịch
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if(!empty($schedules)): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Hiển thị <?= count($schedules) ?> lịch trình</small>
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
        </div>
        <?php endif; ?>
    </main>
</body>

</html>