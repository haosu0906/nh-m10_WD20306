<?php
require_once __DIR__ . '/../../assets/configs/env.php';
?>
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

    /* Sidebar styles are provided by modern-ui.css */

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

    .main {
        margin-left: 200px;
        padding: 86px 22px 22px;
    }

    /* CSS Grid Layout for Guide Assignments Table */
    .assignments-grid-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .assignments-grid-header {
        display: grid;
        grid-template-columns: 60px minmax(220px, 1.8fr) minmax(280px, 2fr) 140px 140px 120px;
        background: #fff;
        color: #0f172a;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .assignments-grid-header-cell {
        padding: 16px 12px;
        border-right: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .assignments-grid-header-cell:last-child {
        border-right: none;
    }

    .assignments-grid-body {
        max-height: 600px;
        overflow-y: auto;
    }

    .assignments-grid-row {
        display: grid;
        grid-template-columns: 60px minmax(220px, 1.8fr) minmax(280px, 2fr) 140px 140px 120px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 6px;
        background: #fff;
        transition: background-color 0.2s;
    }

    .assignments-grid-row:hover {
        background-color: #f8fafc;
    }

    .assignments-grid-cell {
        padding: 18px 14px;
        border-right: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        min-height: 82px;
    }

    .assignments-grid-cell:last-child {
        border-right: none;
    }

    /* ID cell */
    .assignments-id-cell {
        justify-content: center;
    }

    .assignments-id-badge {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Tour cell */
    .assignments-tour-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .assignments-tour-name {
        font-weight: 600;
        color: #1f2937;
    }

    .assignments-tour-id {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Guide cell */
    .assignments-guide-cell {
        gap: 12px;
    }

    .assignments-guide-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: linear-gradient(135deg, #3b82f620, #1d4ed820);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
    }

    .assignments-guide-info {
        flex: 1;
    }

    .assignments-guide-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .assignments-guide-phone {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Date cell */
    .assignments-date-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
    }

    .assignments-date {
        font-weight: 600;
        color: #1f2937;
    }

    .assignments-time {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Status cell */
    .assignments-status-cell {
        justify-content: flex-start;
    }

    .assignments-status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    /* Actions cell */
    .assignments-actions-cell {
        justify-content: center;
        gap: 6px;
    }

    .assignments-actions-cell .btn {
        padding: 6px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    /* Empty state */
    .assignments-grid-empty {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    /* Responsive */
    @media (max-width: 1400px) {
        .assignments-grid-header,
        .assignments-grid-row {
            grid-template-columns: 50px 180px 220px 100px 120px 100px;
        }
    }

    @media (max-width: 1200px) {
        .assignments-grid-header,
        .assignments-grid-row {
            grid-template-columns: 40px 160px 200px 90px 110px 90px;
        }
        .assignments-grid-cell {
            font-size: 0.8rem;
            padding: 12px 8px;
        }
    }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='guide_assignments'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
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

        <!-- Assignments Grid Table -->
        <div class="assignments-grid-container fade-in">
            <!-- Grid Header -->
            <div class="assignments-grid-header">
                <div class="assignments-grid-header-cell">ID</div>
                <div class="assignments-grid-header-cell">Tour</div>
                <div class="assignments-grid-header-cell">HDV</div>
                <div class="assignments-grid-header-cell">Ngày</div>
                <div class="assignments-grid-header-cell">Trạng thái</div>
                <div class="assignments-grid-header-cell">Hành động</div>
            </div>
            
            <!-- Grid Body -->
            <div class="assignments-grid-body">
                <?php if(!empty($assignments)): foreach($assignments as $assignment): ?>
                    <div class="assignments-grid-row">
                        <!-- ID Cell -->
                        <div class="assignments-grid-cell assignments-id-cell">
                            <span class="assignments-id-badge"><?= htmlspecialchars($assignment['id']) ?></span>
                        </div>
                        
                        <!-- Tour Cell -->
                        <div class="assignments-grid-cell assignments-tour-cell">
                            <div class="assignments-tour-name"><?= htmlspecialchars($assignment['tour_title'] ?? 'Chưa có tên tour') ?></div>
                            <div class="assignments-tour-id">ID: <?= htmlspecialchars($assignment['tour_id'] ?? 'N/A') ?></div>
                        </div>
                        
                        <!-- Guide Cell -->
                        <div class="assignments-grid-cell assignments-guide-cell">
                            <div class="assignments-guide-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="assignments-guide-info">
                                <div class="assignments-guide-name"><?= htmlspecialchars($assignment['guide_name'] ?? 'Chưa có tên HDV') ?></div>
                                <div class="assignments-guide-phone"><?= htmlspecialchars($assignment['guide_phone'] ?? 'Chưa có SĐT') ?></div>
                            </div>
                        </div>
                        
                        <!-- Date Cell -->
                        <div class="assignments-grid-cell assignments-date-cell">
                            <div class="assignments-date"><?= date('d/m/Y', strtotime($assignment['assignment_date'] ?? 'now')) ?></div>
                            <div class="assignments-time"><?= date('H:i', strtotime($assignment['start_time'] ?? 'now')) ?></div>
                        </div>
                        
                        <!-- Status Cell -->
                        <div class="assignments-grid-cell assignments-status-cell">
                            <?php
                            $statusColors = [
                                'pending' => ['bg-warning', 'fa-clock', 'Chờ xác nhận'],
                                'confirmed' => ['bg-success', 'fa-check-circle', 'Đã xác nhận'],
                                'completed' => ['bg-primary', 'fa-check', 'Đã hoàn thành'],
                                'cancelled' => ['bg-danger', 'fa-times-circle', 'Đã hủy']
                            ];
                            $status = $assignment['assignment_status'] ?? 'pending';
                            $info = $statusColors[$status] ?? ['bg-secondary', 'fa-question', 'Chưa rõ'];
                            ?>
                            <span class="assignments-status-badge <?= $info[0] ?>">
                                <i class="fas <?= $info[1] ?> me-1"></i>
                                <?= $info[2] ?>
                            </span>
                        </div>
                        
                        <!-- Actions Cell -->
                        <div class="assignments-grid-cell assignments-actions-cell">
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
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="assignments-grid-empty">
                        <div class="text-muted">
                            <i class="fas fa-user-check fa-3x mb-3 opacity-50"></i>
                            <h5>Chưa có phân công nào</h5>
                            <p>Bắt đầu bằng cách thêm phân công đầu tiên</p>
                            <a href="<?= BASE_URL ?>?r=guide_assignments_create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm phân công
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if(!empty($assignments)): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
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
        </div>
        <?php endif; ?>
    </div>
</body>

</html>
