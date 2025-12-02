<?php

require_once __DIR__ . '/../../assets/configs/env.php';

$editing = isset($guide) && !empty($guide);
$title = $editing ? 'Sửa Hướng Dẫn Viên' : 'Thêm Hướng Dẫn Viên';
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
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
    </style>
</head>

<body>
    <?php $current_page='staff'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>

    <main class="main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><?= $title ?></h3>
            <a href="<?= BASE_URL ?>?r=staff" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Lỗi: <?= htmlspecialchars(urldecode($_GET['error'])) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data"
                    action="<?= $editing ? BASE_URL . '?r=staff_update&id='.$guide['id'] : BASE_URL . '?r=staff_store' ?>">
                    
                    <!-- Avatar Upload -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Avatar</label>
                            <div class="text-center">
                                <?php if ($editing && !empty($guide['avatar']) && file_exists($guide['avatar'])): ?>
                                    <img src="<?= BASE_URL . $guide['avatar'] ?>" alt="Avatar" class="rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 120px; height: 120px; font-size: 3rem;">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="avatar" class="form-control" accept="image/*">
                                <small class="text-muted">JPG, PNG tối đa 2MB</small>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="full_name" required
                                        value="<?= $editing ? htmlspecialchars($guide['full_name']) : '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input class="form-control" type="email" name="email" required
                                        value="<?= $editing ? htmlspecialchars($guide['email']) : '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SĐT <span class="text-danger">*</span></label>
                                    <input class="form-control" type="tel" name="phone" required
                                        value="<?= $editing ? htmlspecialchars($guide['phone']) : '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">CMND/CCCD</label>
                                    <input class="form-control" type="text" name="identity_no"
                                        value="<?= $editing ? htmlspecialchars($guide['identity_no'] ?? '') : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Info -->
                    <h5 class="card-title mb-3">Thông tin chuyên môn</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Loại HDV</label>
                            <select class="form-select" name="guide_type">
                                <option value="domestic" <?= $editing && $guide['guide_type'] == 'domestic' ? 'selected' : '' ?>>🏠 Nội địa</option>
                                <option value="international" <?= $editing && $guide['guide_type'] == 'international' ? 'selected' : '' ?>>🌏 Quốc tế</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số chứng chỉ</label>
                            <input class="form-control" type="text" name="certificate_no"
                                value="<?= $editing ? htmlspecialchars($guide['certificate_no'] ?? '') : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kinh nghiệm (năm)</label>
                            <input class="form-control" type="number" name="experience_years" min="0"
                                value="<?= $editing ? htmlspecialchars($guide['experience_years'] ?? 0) : 0 ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ngôn ngữ</label>
                            <input class="form-control" type="text" name="languages" placeholder="Ví dụ: Tiếng Việt, English, 中文"
                                value="<?= $editing ? htmlspecialchars($guide['languages'] ?? '') : '' ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tuyến chuyên</label>
                            <input class="form-control" type="text" name="specialized_route" placeholder="Ví dụ: Miền Bắc Việt Nam, Đông Nam Á"
                                value="<?= $editing ? htmlspecialchars($guide['specialized_route'] ?? '') : '' ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tình trạng sức khỏe</label>
                            <textarea class="form-control" name="health_status" rows="2"
                                placeholder="Ví dụ: Sức khỏe tốt, không bệnh nền"><?= $editing ? htmlspecialchars($guide['health_status'] ?? '') : '' ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="notes" rows="2"><?= $editing ? htmlspecialchars($guide['notes'] ?? '') : '' ?></textarea>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="is_active">
                                <option value="1" <?= $editing && $guide['is_active'] ? 'selected' : '' ?>>✅ Hoạt động</option>
                                <option value="0" <?= $editing && !$guide['is_active'] ? 'selected' : '' ?>>⏸️ Nghỉ</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save me-2"></i>Lưu
                        </button>
                        <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=staff">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
