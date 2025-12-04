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

    /* Sidebar styles are provided by modern-ui.css */

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
        padding: 86px 22px 22px;
    }

    .stats-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
    }

    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--accent);
    }

    .rating-stars {
        color: #ffc107;
    }

    /* CSS Grid Layout for Guide Ratings Table */
    .ratings-grid-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .ratings-grid-header {
        display: grid;
        grid-template-columns: minmax(240px, 2fr) minmax(240px, 2fr) 140px minmax(260px, 2fr) 160px 140px 120px;
        background: #fff;
        color: #0f172a;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .ratings-grid-header-cell {
        padding: 16px 12px;
        border-right: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .ratings-grid-header-cell:last-child {
        border-right: none;
    }

    .ratings-grid-body {
        max-height: 600px;
        overflow-y: auto;
    }

    .ratings-grid-row {
        display: grid;
        grid-template-columns: minmax(240px, 2fr) minmax(240px, 2fr) 140px minmax(260px, 2fr) 160px 140px 120px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 6px;
        background: #fff;
        transition: background-color 0.2s;
    }

    .ratings-grid-row:hover {
        background-color: #f8fafc;
    }

    .ratings-grid-cell {
        padding: 18px 14px;
        border-right: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        min-height: 92px;
    }

    .ratings-grid-cell:last-child {
        border-right: none;
    }

    /* Guide cell */
    .ratings-guide-cell {
        gap: 8px;
    }

    .ratings-guide-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: linear-gradient(135deg, #667eea20, #764ba220);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
    }

    .ratings-guide-info {
        flex: 1;
    }

    .ratings-guide-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .ratings-guide-role {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Customer cell */
    .ratings-customer-cell {
        gap: 8px;
    }

    .ratings-customer-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: linear-gradient(135deg, #3b82f620, #1d4ed820);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
    }

    .ratings-customer-info {
        flex: 1;
    }

    .ratings-customer-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .ratings-customer-role {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Rating cell */
    .ratings-rating-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }

    .ratings-rating-badge {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .ratings-rating-stars {
        display: flex;
        gap: 2px;
    }

    .ratings-rating-stars i {
        font-size: 0.875rem;
        color: #f59e0b;
    }

    .ratings-rating-comment {
        color: #6b7280;
        font-size: 0.8rem;
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Tour cell */
    .ratings-tour-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .ratings-tour-name {
        font-weight: 600;
        color: #1f2937;
    }

    .ratings-tour-date {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Date cell */
    .ratings-date-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .ratings-date-value {
        font-weight: 600;
        color: #1f2937;
    }

    .ratings-time-value {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Actions cell */
    .ratings-actions-cell {
        justify-content: center;
        gap: 6px;
    }

    .ratings-actions-cell .btn {
        padding: 6px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    /* Empty state */
    .ratings-grid-empty {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    /* Responsive */
    @media (max-width: 1400px) {
        .ratings-grid-header,
        .ratings-grid-row {
            grid-template-columns: 130px 130px 100px 180px 130px 100px 90px;
        }
    }

    @media (max-width: 1200px) {
        .ratings-grid-header,
        .ratings-grid-row {
            grid-template-columns: 120px 120px 90px 160px 120px 90px 80px;
        }
        .ratings-grid-cell {
            font-size: 0.8rem;
            padding: 12px 8px;
        }
    }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='guide_ratings'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>

    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
  <div class="main-content">
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

        <!-- Ratings Grid Table -->
        <div class="ratings-grid-container fade-in">
            <!-- Grid Header -->
            <div class="ratings-grid-header">
                <div class="ratings-grid-header-cell">HDV</div>
                <div class="ratings-grid-header-cell">Khách hàng</div>
                <div class="ratings-grid-header-cell">Điểm</div>
                <div class="ratings-grid-header-cell">Nhận xét</div>
                <div class="ratings-grid-header-cell">Tour</div>
                <div class="ratings-grid-header-cell">Ngày</div>
                <div class="ratings-grid-header-cell">Hành động</div>
            </div>
            
            <!-- Grid Body -->
            <div class="ratings-grid-body">
                <?php if(!empty($ratings)): foreach($ratings as $r): ?>
                    <div class="ratings-grid-row">
                        <!-- Guide Cell -->
                        <div class="ratings-grid-cell ratings-guide-cell">
                            <div class="ratings-guide-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="ratings-guide-info">
                                <div class="ratings-guide-name"><?= htmlspecialchars($r['guide_name'] ?? 'N/A') ?></div>
                                <div class="ratings-guide-role">HDV</div>
                            </div>
                        </div>
                        
                        <!-- Customer Cell -->
                        <div class="ratings-grid-cell ratings-customer-cell">
                            <div class="ratings-customer-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="ratings-customer-info">
                                <div class="ratings-customer-name"><?= htmlspecialchars($r['rater_name'] ?? 'Khách hàng') ?></div>
                                <div class="ratings-customer-role">Khách hàng</div>
                            </div>
                        </div>
                        
                        <!-- Rating Cell -->
                        <div class="ratings-grid-cell ratings-rating-cell">
                            <span class="ratings-rating-badge"><?= $r['rating'] ?>/5</span>
                            <div class="ratings-rating-stars">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <?php if($i <= $r['rating']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <?php if (!empty($r['comment'])): ?>
                                <div class="ratings-rating-comment" title="<?= htmlspecialchars($r['comment']) ?>">
                                    <?= htmlspecialchars(mb_substr($r['comment'], 0, 50)) ?>...
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Tour Cell -->
                        <div class="ratings-grid-cell ratings-tour-cell">
                            <div class="ratings-tour-name"><?= htmlspecialchars($r['tour_name'] ?? 'N/A') ?></div>
                            <div class="ratings-tour-date"><?= date('d/m/Y', strtotime($r['tour_date'] ?? 'now')) ?></div>
                        </div>
                        
                        <!-- Date Cell -->
                        <div class="ratings-grid-cell ratings-date-cell">
                            <div class="ratings-date-value"><?= date('d/m/Y', strtotime($r['rating_date'] ?? 'now')) ?></div>
                            <div class="ratings-time-value"><?= date('H:i', strtotime($r['rating_date'] ?? 'now')) ?></div>
                        </div>
                        
                        <!-- Actions Cell -->
                        <div class="ratings-grid-cell ratings-actions-cell">
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
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="ratings-grid-empty">
                        <div class="text-muted">
                            <i class="fas fa-star fa-3x mb-3 opacity-50"></i>
                            <h5>Chưa có đánh giá nào</h5>
                            <p>Bắt đầu bằng cách thêm đánh giá đầu tiên</p>
                            <a href="<?= BASE_URL ?>?r=guide_ratings_create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm đánh giá
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if(!empty($ratings)): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
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
        </div>
        <?php endif; ?>
  </div>
</body>

</html>
