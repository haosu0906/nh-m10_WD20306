<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Quản lý HDV</title>
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

    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover
    }

    /* CSS Grid Layout for Guides Table */
    .guides-grid-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .guides-grid-header {
        display: grid;
        grid-template-columns: 40px 220px 160px 140px 100px 150px 120px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .guides-grid-header-cell {
        padding: 16px 12px;
        border-right: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .guides-grid-header-cell:last-child {
        border-right: none;
    }

    .guides-grid-body {
        max-height: 600px;
        overflow-y: auto;
    }

    .guides-grid-row {
        display: grid;
        grid-template-columns: 40px 220px 160px 140px 100px 150px 120px;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }

    .guides-grid-row:hover {
        background-color: #f8fafc;
    }

    .guides-grid-cell {
        padding: 16px 12px;
        border-right: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        min-height: 80px;
    }

    .guides-grid-cell:last-child {
        border-right: none;
    }

    /* Checkbox cell */
    .guides-checkbox-cell {
        justify-content: center;
    }

    /* Guide info cell */
    .guides-info-cell {
        gap: 12px;
    }

    .guides-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }

    .guides-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .guides-avatar i {
        font-size: 20px;
    }

    .guides-details {
        flex: 1;
    }

    .guides-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .guides-join-date {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Contact cell */
    .guides-contact-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .guides-contact-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: #4b5563;
    }

    .guides-contact-item i {
        color: #6b7280;
        font-size: 12px;
        width: 12px;
    }

    /* Documents cell */
    .guides-docs-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .guides-doc-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: #4b5563;
    }

    .guides-doc-item i {
        color: #6b7280;
        font-size: 12px;
        width: 12px;
    }

    /* Type cell */
    .guides-type-cell {
        justify-content: flex-start;
    }

    .guides-type-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    /* Notes cell */
    .guides-notes-cell {
        justify-content: flex-start;
    }

    .guides-notes-text {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #4b5563;
        font-size: 0.8rem;
    }

    /* Actions cell */
    .guides-actions-cell {
        justify-content: center;
        gap: 6px;
    }

    .guides-actions-cell .btn {
        padding: 6px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    /* Empty state */
    .guides-grid-empty {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    /* Responsive */
    @media (max-width: 1400px) {
        .guides-grid-header,
        .guides-grid-row {
            grid-template-columns: 40px 200px 140px 120px 90px 130px 100px;
        }
    }

    @media (max-width: 1200px) {
        .guides-grid-header,
        .guides-grid-row {
            grid-template-columns: 40px 180px 120px 100px 80px 110px 90px;
        }
        .guides-grid-cell {
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
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
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
                <h1 class="mb-2">👨‍🏫 Hướng Dẫn Viên (HDV)</h1>
                <p class="text-muted mb-0">Quản lý hồ sơ, phân loại và thông tin HDV</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=guide_dashboard">
                    <i class="fas fa-door-open me-2"></i>Portal HDV
                </a>
                <a class="btn btn-success" href="<?= BASE_URL ?>?r=guides_create">
                    <i class="fas fa-plus me-2"></i>Thêm HDV
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card border-0 shadow-sm mb-4 fade-in">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">🔍 Tìm kiếm & Lọc</h5>
            </div>
            <form method="get" class="card-body">
                <input type="hidden" name="r" value="guides">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Từ khóa</label>
                        <input type="text" class="form-control" name="q" placeholder="Tên, email hoặc SĐT" 
                               value="<?= htmlspecialchars((string)($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Loại HDV</label>
                        <select class="form-select" name="type">
                            <option value="">Tất cả</option>
                            <?php foreach ($types as $key => $label): ?>
                            <option value="<?= $key ?>" <?= (($_GET['type'] ?? '') === $key) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Tìm kiếm
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Guides Grid Table -->
        <div class="guides-grid-container fade-in">
            <!-- Grid Header -->
            <div class="guides-grid-header">
                <div class="guides-grid-header-cell">
                    <input type="checkbox" class="form-check-input">
                </div>
                <div class="guides-grid-header-cell">Hướng dẫn viên</div>
                <div class="guides-grid-header-cell">Liên hệ</div>
                <div class="guides-grid-header-cell">Giấy tờ</div>
                <div class="guides-grid-header-cell">Loại</div>
                <div class="guides-grid-header-cell">Ghi chú</div>
                <div class="guides-grid-header-cell">Hành động</div>
            </div>
            
            <!-- Grid Body -->
            <div class="guides-grid-body">
                <?php if(!empty($guides)): foreach($guides as $guide): ?>
                    <div class="guides-grid-row">
                        <!-- Checkbox Cell -->
                        <div class="guides-grid-cell guides-checkbox-cell">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        
                        <!-- Guide Info Cell -->
                        <div class="guides-grid-cell guides-info-cell">
                            <div class="guides-avatar">
                                <?php if(!empty($guide['avatar'])): ?>
                                    <img src="<?= BASE_ASSETS_UPLOADS . $guide['avatar'] ?>" 
                                         alt="<?= htmlspecialchars((string)$guide['full_name'], ENT_QUOTES, 'UTF-8') ?>">
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <div class="guides-details">
                                <div class="guides-name"><?= htmlspecialchars((string)$guide['full_name'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="guides-join-date">Tham gia: <?= !empty($guide['created_at']) ? date('d/m/Y', strtotime($guide['created_at'])) : '---' ?></div>
                            </div>
                        </div>
                        
                        <!-- Contact Cell -->
                        <div class="guides-grid-cell guides-contact-cell">
                            <div class="guides-contact-item">
                                <i class="fas fa-envelope"></i>
                                <span><?= htmlspecialchars((string)($guide['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="guides-contact-item">
                                <i class="fas fa-phone"></i>
                                <span><?= htmlspecialchars((string)($guide['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                        
                        <!-- Documents Cell -->
                        <div class="guides-grid-cell guides-docs-cell">
                            <div class="guides-doc-item">
                                <i class="fas fa-id-card"></i>
                                <span>CMND: <?= htmlspecialchars((string)($guide['identity_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="guides-doc-item">
                                <i class="fas fa-certificate"></i>
                                <span>CC: <?= htmlspecialchars((string)($guide['certificate_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                        
                        <!-- Type Cell -->
                        <div class="guides-grid-cell guides-type-cell">
                            <span class="guides-type-badge">
                                <i class="fas fa-user-tag me-1"></i>
                                <?= htmlspecialchars((string)($types[$guide['guide_type']] ?? $guide['guide_type']), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                        
                        <!-- Notes Cell -->
                        <div class="guides-grid-cell guides-notes-cell">
                            <div class="guides-notes-text" 
                                 title="<?= htmlspecialchars((string)($guide['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars((string)mb_substr((string)($guide['notes'] ?? ''), 0, 40), ENT_QUOTES, 'UTF-8') ?>
                                <?= mb_strlen((string)($guide['notes'] ?? '')) > 40 ? '...' : '' ?>
                            </div>
                        </div>
                        
                        <!-- Actions Cell -->
                        <div class="guides-grid-cell guides-actions-cell">
                            <div class="btn-group" role="group">
                                <a href="<?= BASE_URL ?>?r=guides_edit&id=<?= $guide['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>?r=guides_show&id=<?= $guide['id'] ?>" 
                                   class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>?r=guides_delete&id=<?= $guide['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Xác nhận xóa HDV này?')" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="guides-grid-empty">
                        <div class="text-muted">
                            <i class="fas fa-user-tie fa-3x mb-3 opacity-50"></i>
                            <h5>Chưa có HDV nào</h5>
                            <p>Bắt đầu bằng cách thêm HDV đầu tiên</p>
                            <a href="<?= BASE_URL ?>?r=guides_create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm HDV
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if(!empty($guides)): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Hiển thị <?= count($guides) ?> HDV</small>
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