<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Quản lý nhân sự</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root{--accent:#667eea;--accent-dark:#5568d3}
    .main-content{margin-left:260px;padding:86px 22px 22px}
    .staff-grid-container{background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.06);overflow:hidden}
    .staff-grid-header{display:grid;grid-template-columns:40px minmax(240px,2fr) minmax(220px,1.6fr) 130px 120px 160px;border-bottom:1px solid #e5e7eb;font-weight:600;font-size:.875rem;text-transform:uppercase;letter-spacing:.3px;color:#0f172a}
    .staff-grid-header-cell{padding:14px 12px;border-right:1px solid #e5e7eb;display:flex;align-items:center}
    .staff-grid-header-cell:last-child{border-right:none}
    .staff-grid-body{max-height:640px;overflow-y:auto}
    .staff-grid-row{display:grid;grid-template-columns:40px minmax(240px,2fr) minmax(220px,1.6fr) 130px 120px 160px;border:1px solid #e5e7eb;border-radius:10px;margin:6px;background:#fff;transition:background-color .2s}
    .staff-grid-row:hover{background:#f8fafc}
    .staff-grid-cell{padding:16px 12px;border-right:1px solid #e5e7eb;display:flex;align-items:center;font-size:.95rem;min-height:80px}
    .staff-grid-cell:last-child{border-right:none}
    .staff-checkbox-cell{justify-content:center}
    .staff-info-cell{gap:12px}
    .staff-avatar{width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,#667eea20,#764ba220);display:flex;align-items:center;justify-content:center;color:#667eea;overflow:hidden}
    .staff-avatar img{width:42px;height:42px;object-fit:cover}
    .staff-details{line-height:1.2}
    .staff-name{font-weight:600;color:#1f2937;margin-bottom:4px}
    .staff-join{color:#6b7280;font-size:.8rem}
    .staff-contact-cell{gap:12px}
    .staff-contact-item{display:flex;align-items:center;gap:8px;color:#374151}
    .staff-role-cell{justify-content:flex-start}
    .staff-role-badge{padding:6px 10px;border-radius:14px;font-size:.78rem;font-weight:600;white-space:nowrap;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff}
    .staff-status-cell{justify-content:flex-start;gap:8px}
    .status-dot{width:10px;height:10px;border-radius:999px}
    .status-active{background:#10b981}
    .status-inactive{background:#ef4444}
    .staff-actions-cell{justify-content:center;gap:6px}
    .staff-actions-cell .btn{padding:6px 8px;font-size:.78rem;border-radius:6px}
    .fade-in{animation:fadeIn .2s ease-in}
    @keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
    @media (max-width:1400px){.staff-grid-header,.staff-grid-row{grid-template-columns:40px 200px 180px 120px 100px 140px}.staff-grid-cell{font-size:.9rem}}
    @media (max-width:1200px){.staff-grid-header,.staff-grid-row{grid-template-columns:40px 160px 160px 110px 90px 120px}.staff-grid-cell{font-size:.85rem;padding:12px 8px}}
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='staff'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h1 class="mb-2">👥 Nhân Sự</h1>
                <p class="text-muted mb-0">Quản lý hồ sơ, liên hệ và trạng thái</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=staff_fix_data">
                    <i class="fas fa-wrench me-2"></i>Sửa dữ liệu demo
                </a>
                <a class="btn btn-success" href="<?= BASE_URL ?>?r=staff_create">
                    <i class="fas fa-plus me-2"></i>Thêm nhân sự
                </a>
            </div>
        </div>

        

        <?php
            $q = trim($_GET['q'] ?? '');
            $roleFilter = $_GET['role'] ?? '';
            $statusFilter = $_GET['status'] ?? '';
            $roleOptions = [];
            if(!empty($staff)){
                foreach($staff as $st){ $r = $st['role'] ?? ''; if($r!==''){ $roleOptions[$r] = true; } }
            }
            $filtered = [];
            if(!empty($staff)){
                foreach($staff as $st){
                    if($roleFilter !== '' && (($st['role'] ?? '') !== $roleFilter)) continue;
                    if($statusFilter !== '' && ((string)($st['is_active'] ?? $st['status'] ?? 1) !== (string)$statusFilter)) continue;
                    if($q !== ''){
                        $hay = strtolower(($st['full_name'] ?? '') . ' ' . ($st['email'] ?? '') . ' ' . ($st['phone'] ?? ''));
                        if(strpos($hay, strtolower($q)) === false) continue;
                    }
                    $filtered[] = $st;
                }
            }
        ?>

        <div class="card border-0 shadow-sm mb-4 fade-in">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Tìm kiếm & Lọc</h5>
            </div>
            <form method="get" class="card-body">
                <input type="hidden" name="r" value="staff">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Từ khóa</label>
                        <input type="text" class="form-control" name="q" placeholder="Tên, email hoặc SĐT" value="<?= htmlspecialchars($q) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Vai trò</label>
                        <select class="form-select" name="role">
                            <option value="">Tất cả</option>
                            <?php foreach(array_keys($roleOptions) as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>" <?= ($roleFilter === $opt) ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="">Tất cả</option>
                            <option value="1" <?= ($statusFilter==='1')?'selected':'' ?>>Hoạt động</option>
                            <option value="0" <?= ($statusFilter==='0')?'selected':'' ?>>Nghỉ</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Tìm kiếm
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="staff-grid-container fade-in">
            <div class="staff-grid-header">
                <div class="staff-grid-header-cell"><input type="checkbox" class="form-check-input"></div>
                <div class="staff-grid-header-cell">Nhân sự</div>
                <div class="staff-grid-header-cell">Liên hệ</div>
                <div class="staff-grid-header-cell">Vai trò</div>
                <div class="staff-grid-header-cell">Trạng thái</div>
                <div class="staff-grid-header-cell">Hành động</div>
            </div>
            <div class="staff-grid-body">
                <?php if(!empty($filtered)): foreach($filtered as $row): ?>
                    <div class="staff-grid-row">
                        <div class="staff-grid-cell staff-checkbox-cell">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="staff-grid-cell staff-info-cell">
                            <div class="staff-avatar">
                                <?php if(!empty($row['avatar'])): ?>
                                    <img src="<?= BASE_URL . $row['avatar'] ?>" alt="">
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <div class="staff-details">
                                <div class="staff-name"><?= htmlspecialchars($row['full_name'] ?? ($row['username'] ?? '')) ?></div>
                                <div class="staff-join">Tham gia: <?= !empty($row['created_at']) ? date('d/m/Y', strtotime($row['created_at'])) : '---' ?></div>
                            </div>
                        </div>
                        <div class="staff-grid-cell staff-contact-cell">
                            <div class="staff-contact-item"><i class="fas fa-envelope"></i><span><?= htmlspecialchars($row['email'] ?? '') ?></span></div>
                            <div class="staff-contact-item"><i class="fas fa-phone"></i><span><?= htmlspecialchars($row['phone'] ?? '') ?></span></div>
                        </div>
                        <div class="staff-grid-cell staff-role-cell">
                            <span class="staff-role-badge"><i class="fas fa-id-badge me-1"></i><?= htmlspecialchars($row['role'] ?? '') ?></span>
                        </div>
                        <div class="staff-grid-cell staff-status-cell">
                            <?php $active = (bool)($row['is_active'] ?? $row['status'] ?? 1); ?>
                            <span class="status-dot <?= $active?'status-active':'status-inactive' ?>"></span>
                            <span class="<?= $active?'text-success':'text-danger' ?>"><?= $active?'Hoạt động':'Nghỉ' ?></span>
                        </div>
                        <div class="staff-grid-cell staff-actions-cell">
                            <div class="btn-group" role="group">
                                <a href="<?= BASE_URL ?>?r=staff_edit&id=<?= (int)($row['id'] ?? 0) ?>" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="fas fa-edit"></i></a>
                                <a href="<?= BASE_URL ?>?r=staff_delete&id=<?= (int)($row['id'] ?? 0) ?>&csrf_token=<?= urlencode(csrf_token()) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận xóa nhân sự này?')" title="Xóa"><i class="fas fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="text-center p-5 text-muted">
                        <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                        <h5>Chưa có nhân sự</h5>
                        <p>Thêm nhân sự đầu tiên để bắt đầu</p>
                        <a href="<?= BASE_URL ?>?r=staff_create" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Thêm nhân sự</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if(!empty($filtered)): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <small class="text-muted">Hiển thị <?= count($filtered) ?> nhân sự</small>
                <div class="btn-group">
                    <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>?r=staff_create"><i class="fas fa-plus me-1"></i> Thêm</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
