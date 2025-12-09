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

    .main-content {}

    /* CSS Grid Layout for Suppliers Table */
    .suppliers-grid-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .suppliers-grid-header {
        display: grid;
        grid-template-columns: 40px minmax(240px, 2fr) 200px 140px minmax(200px, 1.5fr) 120px;
        background: #fff;
        color: #0f172a;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .suppliers-grid-header-cell {
        padding: 16px 12px;
        border-right: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .suppliers-grid-header-cell:last-child {
        border-right: none;
    }

    .suppliers-grid-body {
        max-height: 600px;
        overflow-y: auto;
    }

    .suppliers-grid-row {
        display: grid;
        grid-template-columns: 40px minmax(240px, 2fr) 200px 140px minmax(200px, 1.5fr) 120px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 6px;
        background: #fff;
        transition: background-color 0.2s;
    }

    .suppliers-grid-row:hover {
        background-color: #f8fafc;
    }

    .suppliers-grid-cell {
        padding: 18px 14px;
        border-right: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        min-height: 82px;
    }

    .suppliers-grid-cell:last-child {
        border-right: none;
    }

    /* Checkbox cell */
    .suppliers-checkbox-cell {
        justify-content: center;
    }

    /* Supplier name cell */
    .suppliers-name-cell {
        gap: 12px;
    }

    .suppliers-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea20, #764ba220);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
    }

    .suppliers-info {
        flex: 1;
    }

    .suppliers-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .suppliers-subtitle {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Contact person cell */
    .suppliers-contact-cell {
        gap: 8px;
    }

    .suppliers-contact-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: linear-gradient(135deg, #3b82f620, #1d4ed820);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
    }

    .suppliers-contact-info {
        flex: 1;
    }

    .suppliers-contact-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .suppliers-contact-email {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Service type cell */
    .suppliers-service-cell {
        justify-content: flex-start;
    }

    .suppliers-service-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    /* Contact info cell */
    .suppliers-contactinfo-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .suppliers-phone {
        font-weight: 600;
        color: #059669;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .suppliers-address {
        color: #6b7280;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Actions cell */
    .suppliers-actions-cell {
        justify-content: center;
        gap: 6px;
    }

    .suppliers-actions-cell .btn {
        padding: 6px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    /* Empty state */
    .suppliers-grid-empty {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .suppliers-grid-header,
        .suppliers-grid-row {
            grid-template-columns: 40px 200px 180px 120px 150px 100px;
        }
    }

    @media (max-width: 992px) {
        .suppliers-grid-header,
        .suppliers-grid-row {
            grid-template-columns: 40px 150px 140px 100px 120px 80px;
        }
        .suppliers-grid-cell {
            font-size: 0.8rem;
            padding: 12px 8px;
        }
    }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='suppliers'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
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

        <!-- Suppliers Grid Table -->
        <div class="suppliers-grid-container fade-in">
            <!-- Grid Header -->
            <div class="suppliers-grid-header">
                <div class="suppliers-grid-header-cell">
                    <input type="checkbox" class="form-check-input">
                </div>
                <div class="suppliers-grid-header-cell">Nhà cung cấp</div>
                <div class="suppliers-grid-header-cell">Người liên hệ</div>
                <div class="suppliers-grid-header-cell">Dịch vụ</div>
                <div class="suppliers-grid-header-cell">Liên hệ</div>
                <div class="suppliers-grid-header-cell">Hành động</div>
            </div>
            
            <!-- Grid Body -->
            <div class="suppliers-grid-body">
                <?php if (!empty($suppliers)): foreach ($suppliers as $s): ?>
                    <div class="suppliers-grid-row">
                        <!-- Checkbox Cell -->
                        <div class="suppliers-grid-cell suppliers-checkbox-cell">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        
                        <!-- Supplier Name Cell -->
                        <div class="suppliers-grid-cell suppliers-name-cell">
                            <div class="suppliers-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="suppliers-info">
                                <div class="suppliers-title"><?= htmlspecialchars((string)$s['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="suppliers-subtitle">ID: <?= (int)$s['id'] ?></div>
                            </div>
                        </div>
                        
                        <!-- Contact Person Cell -->
                        <div class="suppliers-grid-cell suppliers-contact-cell">
                            <div class="suppliers-contact-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="suppliers-contact-info">
                                <div class="suppliers-contact-name"><?= htmlspecialchars((string)$s['contact_person'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="suppliers-contact-email"><?= htmlspecialchars((string)($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                        
                        <!-- Service Type Cell -->
                        <div class="suppliers-grid-cell suppliers-service-cell">
                            <?php
                            $map = [
                                'hotel' => ['Khách sạn', 'fa-hotel', 'bg-primary'],
                                'restaurant' => ['Nhà hàng', 'fa-utensils', 'bg-success'],
                                'transport' => ['Vận chuyển', 'fa-bus', 'bg-warning'],
                                'ticket' => ['Vé tham quan', 'fa-ticket-alt', 'bg-info'],
                                'attraction' => ['Điểm tham quan', 'fa-landmark', 'bg-info'],
                                'insurance' => ['Bảo hiểm', 'fa-shield-alt', 'bg-secondary'],
                                'guide' => ['HDV', 'fa-user-tie', 'bg-dark'],
                                'meal' => ['Ăn uống', 'fa-coffee', 'bg-danger'],
                                'entertain' => ['Giải trí', 'fa-music', 'bg-purple'],
                                'other' => ['Dịch vụ khác', 'fa-cogs', 'bg-light']
                            ];
                            $st = $s['service_type'] ?? 'other';
                            $serviceInfo = $map[$st] ?? $map['other'];
                            ?>
                            <span class="suppliers-service-badge <?= $serviceInfo[2] ?>">
                                <i class="fas <?= $serviceInfo[1] ?> me-1"></i>
                                <?= htmlspecialchars((string)$serviceInfo[0], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                        
                        <!-- Contact Info Cell -->
                        <div class="suppliers-grid-cell suppliers-contactinfo-cell">
                            <div class="suppliers-phone">
                                <i class="fas fa-phone"></i>
                                <?= htmlspecialchars((string)$s['phone'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <?php if (!empty($s['address'])): ?>
                            <div class="suppliers-address">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars(substr((string)$s['address'], 0, 30)) ?>...
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Actions Cell -->
                        <div class="suppliers-grid-cell suppliers-actions-cell">
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
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="suppliers-grid-empty">
                        <div class="text-muted">
                            <i class="fas fa-handshake fa-3x mb-3 opacity-50"></i>
                            <h5>Chưa có nhà cung cấp nào</h5>
                            <p>Bắt đầu bằng cách thêm nhà cung cấp đầu tiên</p>
                            <a href="<?= BASE_URL ?>?r=suppliers_create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if(!empty($suppliers)): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
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
        </div>
        <?php endif; ?>
    </div>
</body>

</html>
