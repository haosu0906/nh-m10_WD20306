<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Sửa Phân công HDV</title>
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
    <div class="sidebar">
        <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-layer-group"></i> Danh mục tour</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=payments"><i class="fas fa-credit-card"></i> Thanh toán</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=cancellation_policies"><i class="fas fa-ban"></i> Chính sách hủy</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Sửa Phân công HDV</h3>
            <a href="<?= BASE_URL ?>?r=guide_assignments" class="btn btn-outline-secondary">Quay lại</a>
        </div>

        <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['flash_error'] ?>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['flash_success'] ?>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>?r=guide_assignments_update&id=<?= $assignment['id'] ?>">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tour <span class="text-danger">*</span></label>
                            <select name="tour_id" class="form-select" required>
                                <option value="">-- Chọn tour --</option>
                                <?php foreach ($tours as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (int)$t['id'] === (int)$assignment['tour_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['title']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hướng dẫn viên <span class="text-danger">*</span></label>
                            <select name="guide_id" class="form-select" required>
                                <option value="">-- Chọn hướng dẫn viên --</option>
                                <?php foreach ($guides as $g): ?>
                                <option value="<?= $g['id'] ?>" <?= (int)$g['id'] === (int)$assignment['guide_user_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($g['full_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại phân công</label>
                            <select name="assignment_type" class="form-select">
                                <option value="main_guide" <?= $assignment['assignment_type'] === 'main_guide' ? 'selected' : '' ?>>HDV Chính</option>
                                <option value="assistant_guide" <?= $assignment['assignment_type'] === 'assistant_guide' ? 'selected' : '' ?>>HDV Phụ</option>
                                <option value="tour_leader" <?= $assignment['assignment_type'] === 'tour_leader' ? 'selected' : '' ?>>Trưởng đoàn</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày phân công</label>
                            <input type="date" name="assignment_date" class="form-control" value="<?= htmlspecialchars($assignment['assignment_date'] ?? date('Y-m-d')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="pending" <?= $assignment['assignment_status'] === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                                <option value="confirmed" <?= $assignment['assignment_status'] === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                                <option value="completed" <?= $assignment['assignment_status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                <option value="cancelled" <?= $assignment['assignment_status'] === 'cancelled' ? 'selected' : '' ?>>Hủy</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="Ghi chú về phân công này..."><?= htmlspecialchars($assignment['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                    <a href="<?= BASE_URL ?>?r=guide_assignments" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </div>
        </form>

        <!-- Thông tin hiện tại -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Thông tin phân công hiện tại</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Tour:</strong> <?= htmlspecialchars($assignment['tour_title']) ?><br>
                        <strong>Ngày phân công:</strong> <?= date('d/m/Y', strtotime($assignment['assignment_date'])) ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($assignment['created_at'])) ?><br>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
