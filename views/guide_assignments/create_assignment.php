<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Thêm Phân công HDV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3
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
        padding: 86px 22px 22px
    }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='guide_assignments'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Thêm Phân công HDV</h3>
            <a href="<?= BASE_URL ?>?r=guide_assignments" class="btn btn-outline-secondary">Quay lại</a>
        </div>

        <?php $flash = function_exists('flash_get') ? flash_get() : null; if (!empty($flash)): ?>
        <div class="alert alert-<?= $flash['type']==='error'?'danger':'success' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message'] ?? '') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>?r=guide_assignments_store">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">Thông tin phân công</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tour <span class="text-danger">*</span></label>
                            <select name="tour_id" class="form-select form-select-lg" required>
                                <option value="">-- Chọn tour --</option>
                                <?php foreach ($tours as $t): ?>
                                <option value="<?= $t['id'] ?>">
                                    <?= htmlspecialchars($t['title']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hướng dẫn viên <span class="text-danger">*</span></label>
                            <select name="guide_id" class="form-select form-select-lg" required>
                                <option value="">-- Chọn hướng dẫn viên --</option>
                                <?php foreach ($guides as $g): ?>
                                <option value="<?= $g['id'] ?>">
                                    <?= htmlspecialchars($g['full_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Loại phân công</label>
                            <select name="assignment_type" class="form-select form-select-lg">
                                <option value="main_guide">HDV Chính</option>
                                <option value="assistant_guide">HDV Phụ</option>
                                <option value="tour_leader">Trưởng đoàn</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày phân công</label>
                            <input type="date" name="assignment_date" class="form-control form-control-lg" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Ghi chú</label>
                            <textarea name="notes" rows="3" class="form-control form-control-lg" placeholder="Ghi chú về phân công này..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Lưu phân công
                    </button>
                    <a href="<?= BASE_URL ?>?r=guide_assignments" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
