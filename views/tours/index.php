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

    .tour-cover {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px
    }

    /* CSS Grid Layout for Tours Table */
    .tours-grid-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .tours-grid-header {
        display: grid;
        grid-template-columns: 40px minmax(320px, 2fr) 140px 120px 160px 160px 120px 120px 140px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tours-grid-header-cell {
        padding: 16px 12px;
        border-right: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .tours-grid-header-cell:last-child {
        border-right: none;
    }

    .tours-grid-body {
        max-height: 600px;
        overflow-y: auto;
    }

    .tours-grid-row {
        display: grid;
        grid-template-columns: 40px minmax(320px, 2fr) 140px 120px 160px 160px 120px 120px 140px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 6px;
        background: #fff;
        transition: background-color 0.2s;
    }

    .tours-grid-row:hover {
        background-color: #f8fafc;
    }

    .tours-grid-cell {
        padding: 18px 14px;
        border-right: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        min-height: 82px;
    }

    .tours-grid-cell:last-child {
        border-right: none;
    }

    /* Checkbox cell */
    .tours-checkbox-cell {
        justify-content: center;
    }

    /* Tour info cell */
    .tours-tour-cell {
        gap: 12px;
    }

    .tours-cover-img {
        width: 60px;
        height: 45px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }

    .tours-cover-placeholder {
        width: 60px;
        height: 45px;
        border-radius: 6px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-size: 0.75rem;
        border: 1px solid #e5e7eb;
    }

    .tours-info {
        flex: 1;
    }

    .tours-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .tours-desc {
        color: #6b7280;
        font-size: 0.8rem;
        line-height: 1.4;
    }

    /* Badge cells */
    .tours-badge-cell {
        justify-content: flex-start;
    }

    .tours-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .badge-category {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-type {
        background: #ede9fe;
        color: #6b21a8;
    }

    .badge-supplier {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-no-supplier {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* Status cell */
    .tours-status-cell {
        justify-content: center;
    }

    .tours-status {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        text-align: center;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background: #fef3c7;
        color: #92400e;
    }

    /* Price cell */
    .tours-price-cell {
        justify-content: center;
        font-weight: 600;
        color: #059669;
    }

    /* Actions cell */
    .tours-actions-cell {
        justify-content: center;
        gap: 6px;
    }

    .tours-actions-cell .btn {
        padding: 6px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    /* Responsive */
    @media (max-width: 1400px) {
        .tours-grid-header,
        .tours-grid-row {
            grid-template-columns: 40px 250px 100px 80px 120px 120px 80px 80px 100px;
        }
    }

    @media (max-width: 1200px) {
        .tours-grid-header,
        .tours-grid-row {
            grid-template-columns: 40px 200px 80px 70px 100px 100px 70px 70px 90px;
        }
        .tours-grid-cell {
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
            <a class="nav-link active" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
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
        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['flash_success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['flash_error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h1 class="mb-2">🗺️ Quản Lý Tours</h1>
                <p class="text-muted mb-0">Thông tin cơ bản, lịch trình, giá và hình ảnh</p>
            </div>
            <div>
                <a class="btn btn-success" href="<?= BASE_URL ?>?r=tours_create">
                    <i class="fas fa-plus me-2"></i>Thêm tour
                </a>
            </div>
        </div>

        <!-- Tours Grid Table -->
        <div class="tours-grid-container fade-in">
            <!-- Grid Header -->
            <div class="tours-grid-header">
                <div class="tours-grid-header-cell">
                    <input type="checkbox" class="form-check-input">
                </div>
                <div class="tours-grid-header-cell">Tour</div>
                <div class="tours-grid-header-cell">Danh mục</div>
                <div class="tours-grid-header-cell">Loại</div>
                <div class="tours-grid-header-cell">Nhà cung cấp</div>
                <div class="tours-grid-header-cell">Chính sách hủy</div>
                <div class="tours-grid-header-cell">Trạng thái</div>
                <div class="tours-grid-header-cell">Giá NL</div>
                <div class="tours-grid-header-cell">Hành động</div>
            </div>
            
            <!-- Grid Body -->
            <div class="tours-grid-body">
                <?php if(!empty($tours)): foreach($tours as $tour): ?>
                    <div class="tours-grid-row">
                        <!-- Checkbox Cell -->
                        <div class="tours-grid-cell tours-checkbox-cell">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        
                        <!-- Tour Cell -->
                        <div class="tours-grid-cell tours-tour-cell">
                            <?php
                            $cover = $tour['cover_image'] ?? ($tour['image'] ?? '');
                            $isExternal = preg_match('/^https?:\/\//i', (string)$cover);
                            ?>
                            <?php if(!empty($cover)): ?>
                                <img src="<?= $isExternal ? $cover : (BASE_ASSETS_UPLOADS . $cover) ?>"
                                     class="tours-cover-img" alt="<?= htmlspecialchars($tour['title']) ?>">
                            <?php else: ?>
                                <div class="tours-cover-placeholder">N/A</div>
                            <?php endif; ?>
                            <div class="tours-info">
                                <div class="tours-title"><?= htmlspecialchars($tour['title']) ?></div>
                                <div class="tours-desc"><?= htmlspecialchars(mb_substr($tour['description'] ?? '', 0, 60)) ?>...</div>
                            </div>
                        </div>
                        
                        <!-- Category Cell -->
                        <div class="tours-grid-cell tours-badge-cell">
                            <span class="tours-badge badge-category">
                                <i class="fas fa-layer-group me-1"></i>
                                <?= htmlspecialchars($tour['category_name'] ?? 'Chưa phân loại') ?>
                            </span>
                        </div>
                        
                        <!-- Type Cell -->
                        <div class="tours-grid-cell tours-badge-cell">
                            <span class="tours-badge badge-type">
                                <i class="fas fa-tag me-1"></i>
                                <?= htmlspecialchars($types[$tour['tour_type']] ?? $tour['tour_type']) ?>
                            </span>
                        </div>
                        
                        <!-- Supplier Cell -->
                        <div class="tours-grid-cell tours-badge-cell">
                            <?php if (!empty($tour['supplier_name'])): ?>
                                <span class="tours-badge badge-supplier">
                                    <i class="fas fa-handshake me-1"></i>
                                    <?= htmlspecialchars($tour['supplier_name']) ?>
                                </span>
                            <?php else: ?>
                                <span class="tours-badge badge-no-supplier">
                                    <i class="fas fa-times me-1"></i>
                                    Chưa có
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Cancellation Policy Cell -->
                        <div class="tours-grid-cell tours-badge-cell">
                            <?php
                            $policy = $tour['cancellation_policy_name'] ?? null;
                            $refund = $tour['cancellation_policy_refund'] ?? null;
                            if ($policy) {
                                echo '<span class="tours-badge badge-type"><i class="fas fa-shield-alt me-1"></i>' . htmlspecialchars($policy);
                                if ($refund) echo ' (' . number_format((float)$refund, 0, ',', '.') . '%)';
                                echo '</span>';
                            } else {
                                echo '<span class="tours-badge badge-no-supplier"><i class="fas fa-times me-1"></i>Không có</span>';
                            }
                            ?>
                        </div>
                        
                        <!-- Status Cell -->
                        <div class="tours-grid-cell tours-status-cell">
                            <span class="tours-status <?= ($tour['status'] == 'active') ? 'status-active' : 'status-inactive' ?>">
                                <i class="fas fa-info-circle me-1"></i>
                                <?= htmlspecialchars($statuses[$tour['status']] ?? $tour['status']) ?>
                            </span>
                        </div>
                        
                        <!-- Price Cell -->
                        <div class="tours-grid-cell tours-price-cell">
                            <div>
                                <div>
                                    <?php $adult = $priceByTour[$tour['id']]['adult_price'] ?? ($tour['adult_price_join'] ?? ($tour['price'] ?? 0)); ?>
                                    <?= number_format((float)$adult, 0, ',', '.') ?>₫
                                </div>
                                <small class="text-muted">người lớn</small>
                            </div>
                        </div>
                        
                        <!-- Actions Cell -->
                        <div class="tours-grid-cell tours-actions-cell">
                            <div class="btn-group-vertical btn-group-sm" role="group">
                                <a href="<?= BASE_URL ?>?r=tours_itinerary&id=<?= $tour['id'] ?>" 
                                   class="btn btn-outline-info mb-1" title="Lịch trình">
                                    <i class="fas fa-map-marked-alt"></i>
                                </a>
                                <a href="<?= BASE_URL ?>?r=schedules&tour_id=<?= $tour['id'] ?>" 
                                   class="btn btn-outline-secondary mb-1" title="Lịch khởi hành">
                                    <i class="fas fa-calendar"></i>
                                </a>
                                <a href="<?= BASE_URL ?>?r=tours_edit&id=<?= $tour['id'] ?>" 
                                   class="btn btn-outline-primary mb-1" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>?r=tours_delete&id=<?= $tour['id'] ?>" 
                                   class="btn btn-outline-danger" 
                                   onclick="return confirm('Xác nhận xóa tour này?')" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="tours-grid-empty">
                        <div class="text-muted">
                            <i class="fas fa-route fa-3x mb-3 opacity-50"></i>
                            <h5>Chưa có tour nào</h5>
                            <p>Bắt đầu bằng cách tạo tour đầu tiên</p>
                            <a href="<?= BASE_URL ?>?r=tours_create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm tour
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if(!empty($tours)): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Hiển thị <?= count($tours) ?> tours</small>
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
