<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Quản lý Tours</title>
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

    /* CSS Grid Layout for Tour Categories Table */
    .categories-grid-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .categories-grid-header {
        display: grid;
        grid-template-columns: 40px 60px 250px 120px 200px 120px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .categories-grid-header-cell {
        padding: 16px 12px;
        border-right: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .categories-grid-header-cell:last-child {
        border-right: none;
    }

    .categories-grid-body {
        max-height: 600px;
        overflow-y: auto;
    }

    .categories-grid-row {
        display: grid;
        grid-template-columns: 40px 60px 250px 120px 200px 120px;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }

    .categories-grid-row:hover {
        background-color: #f8fafc;
    }

    .categories-grid-cell {
        padding: 16px 12px;
        border-right: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        min-height: 70px;
    }

    .categories-grid-cell:last-child {
        border-right: none;
    }

    /* Checkbox cell */
    .categories-checkbox-cell {
        justify-content: center;
    }

    /* ID cell */
    .categories-id-cell {
        justify-content: center;
    }

    .categories-id-badge {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Name cell */
    .categories-name-cell {
        gap: 12px;
    }

    .categories-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea20, #764ba220);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
    }

    .categories-info {
        flex: 1;
    }

    .categories-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .categories-subtitle {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Type cell */
    .categories-type-cell {
        justify-content: flex-start;
    }

    .categories-type-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .type-domestic {
        background: #d1fae5;
        color: #065f46;
    }

    .type-international {
        background: #dbeafe;
        color: #1e40af;
    }

    /* Description cell */
    .categories-desc-cell {
        color: #6b7280;
        font-size: 0.8rem;
        line-height: 1.4;
    }

    /* Actions cell */
    .categories-actions-cell {
        justify-content: center;
        gap: 6px;
    }

    .categories-actions-cell .btn {
        padding: 6px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    /* Empty state */
    .categories-grid-empty {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .categories-grid-header,
        .categories-grid-row {
            grid-template-columns: 40px 50px 200px 100px 150px 100px;
        }
    }

    @media (max-width: 992px) {
        .categories-grid-header,
        .categories-grid-row {
            grid-template-columns: 40px 40px 150px 80px 120px 80px;
        }
        .categories-grid-cell {
            font-size: 0.8rem;
            padding: 12px 8px;
        }
    }

    @media (max-width:900px) {
        .sidebar {
            position: relative;
            width: 100%
        }

        .main {
            margin-left: 0
        }
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
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
                <h1 class="mb-2">🗺️ Danh Mục Tour</h1>
                <p class="text-muted mb-0">Quản lý và phân loại các tour du lịch</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>?r=tour_categories_create" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Thêm danh mục
                </a>
            </div>
        </div>

        <!-- Categories Grid Table -->
        <div class="categories-grid-container fade-in">
            <!-- Grid Header -->
            <div class="categories-grid-header">
                <div class="categories-grid-header-cell">
                    <input type="checkbox" class="form-check-input">
                </div>
                <div class="categories-grid-header-cell">ID</div>
                <div class="categories-grid-header-cell">Tên danh mục</div>
                <div class="categories-grid-header-cell">Loại tour</div>
                <div class="categories-grid-header-cell">Mô tả</div>
                <div class="categories-grid-header-cell">Hành động</div>
            </div>
            
            <!-- Grid Body -->
            <div class="categories-grid-body">
                <?php if(!empty($items)): foreach($items as $row): ?>
                    <div class="categories-grid-row">
                        <!-- Checkbox Cell -->
                        <div class="categories-grid-cell categories-checkbox-cell">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        
                        <!-- ID Cell -->
                        <div class="categories-grid-cell categories-id-cell">
                            <span class="categories-id-badge"><?= htmlspecialchars($row['id']) ?></span>
                        </div>
                        
                        <!-- Name Cell -->
                        <div class="categories-grid-cell categories-name-cell">
                            <div class="categories-icon">
                                <i class="fas fa-map"></i>
                            </div>
                            <div class="categories-info">
                                <div class="categories-title"><?= htmlspecialchars($row['name']) ?></div>
                                <div class="categories-subtitle">ID: <?= htmlspecialchars($row['id']) ?></div>
                            </div>
                        </div>
                        
                        <!-- Type Cell -->
                        <div class="categories-grid-cell categories-type-cell">
                            <?php
                                $typeKey = $row['category_type'] ?? 'domestic';
                                $typeIcon = $typeKey === 'domestic' ? 'fa-home' : 'fa-globe';
                                $typeName = $typeKey === 'domestic' ? 'Nội địa' : 'Quốc tế';
                            ?>
                            <span class="categories-type-badge <?= $typeKey === 'domestic' ? 'type-domestic' : 'type-international' ?>">
                                <i class="fas <?= $typeIcon ?> me-1"></i>
                                <?= $typeName ?>
                            </span>
                        </div>
                        
                        <!-- Description Cell -->
                        <div class="categories-grid-cell categories-desc-cell">
                            <p class="mb-0 text-truncate" title="<?= htmlspecialchars($row['description'] ?? '') ?>">
                                <?= htmlspecialchars(substr($row['description'] ?? '', 0, 60)) ?>
                                <?= strlen($row['description'] ?? '') > 60 ? '...' : '' ?>
                            </p>
                        </div>
                        
                        <!-- Actions Cell -->
                        <div class="categories-grid-cell categories-actions-cell">
                            <div class="btn-group" role="group">
                                <a href="<?= BASE_URL ?>?r=tour_categories_edit&id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>?r=tour_categories_show&id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>?r=tour_categories_delete&id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Xác nhận xóa danh mục này?')" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="categories-grid-empty">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <h5>Chưa có dữ liệu</h5>
                            <p>Bắt đầu bằng cách thêm danh mục tour đầu tiên</p>
                            <a href="<?= BASE_URL ?>?r=tour_categories_create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm danh mục
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
                        <small class="text-muted">Hiển thị <?= count($items) ?> danh mục</small>
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