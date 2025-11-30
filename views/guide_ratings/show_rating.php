<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Chi tiết Đánh giá HDV</title>
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

    .rating-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
    }

    .rating-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 1.5rem;
        border-radius: 8px 8px 0 0;
    }

    .rating-stars {
        color: #ffc107;
        font-size: 1.5rem;
    }

    .rating-score {
        font-size: 3rem;
        font-weight: 700;
        margin: 1rem 0;
    }

    .rating-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #eee;
    }

    .rating-item:last-child {
        border-bottom: none;
    }

    .rating-label {
        font-weight: 600;
        color: #495057;
    }

    .rating-bar {
        flex: 1;
        margin: 0 1rem;
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    .rating-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transition: width 0.3s ease;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .comment-box {
        background: white;
        border-left: 4px solid var(--accent);
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 0 8px 8px 0;
    }

    .pros-cons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .pros-box {
        background: #d4edda;
        border-left: 4px solid #28a745;
        padding: 1rem;
        border-radius: 0 8px 8px 0;
    }

    .cons-box {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        padding: 1rem;
        border-radius: 0 8px 8px 0;
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
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_schedules"><i class="fas fa-calendar-alt"></i> Lịch HDV</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=guide_ratings"><i class="fas fa-star"></i> Đánh giá HDV</a>
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
            <h3>Chi tiết Đánh giá HDV</h3>
            <a href="<?= BASE_URL ?>?r=guide_ratings" class="btn btn-outline-secondary">Quay lại</a>
        </div>

        <!-- Overall Rating -->
        <div class="rating-card">
            <div class="rating-header text-center">
                <h5 class="mb-0">Đánh giá HDV</h5>
                <div class="rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star <?= $i <= round($rating['rating overall']) ? '' : 'text-white-50' ?>"></i>
                    <?php endfor; ?>
                </div>
                <div class="rating-score"><?= number_format($rating['rating overall'], 1) ?></div>
                <div class="text-white-75">Trên thang điểm 5.0</div>
            </div>
        </div>

        <!-- Rating Details -->
        <div class="rating-card">
            <div class="card-body">
                <h6 class="mb-3"><i class="fas fa-chart-bar"></i> Điểm chi tiết</h6>
                <div class="rating-item">
                    <span class="rating-label">Chuyên môn</span>
                    <div class="rating-bar">
                        <div class="rating-fill" style="width: <?= ($rating['rating'] / 5) * 100 ?>%"></div>
                    </div>
                    <span class="rating-stars"><?= number_format($rating['rating'], 1) ?></span>
                </div>
                <div class="rating-item">
                    <span class="rating-label">Giao tiếp</span>
                    <div class="rating-bar">
                        <div class="rating-fill" style="width: <?= ($rating['rating'] / 5) * 100 ?>%"></div>
                    </div>
                    <span class="rating-stars"><?= number_format($rating['rating'], 1) ?></span>
                </div>
                <div class="rating-item">
                    <span class="rating-label">Kiến thức</span>
                    <div class="rating-bar">
                        <div class="rating-fill" style="width: <?= ($rating['rating'] / 5) * 100 ?>%"></div>
                    </div>
                    <span class="rating-stars"><?= number_format($rating['rating'], 1) ?></span>
                </div>
                <div class="rating-item">
                    <span class="rating-label">Đúng giờ</span>
                    <div class="rating-bar">
                        <div class="rating-fill" style="width: <?= ($rating['rating'] / 5) * 100 ?>%"></div>
                    </div>
                    <span class="rating-stars"><?= number_format($rating['rating'], 1) ?></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Guide Info -->
                <div class="info-card">
                    <h6 class="mb-3"><i class="fas fa-user-tie"></i> Hướng dẫn viên</h6>
                    <div class="mb-2">
                        <strong>Tên:</strong> <?= htmlspecialchars($rating['guide_name']) ?>
                    </div>
                    <div class="mb-2">
                        <strong>SĐT:</strong> <?= htmlspecialchars($rating['guide_phone'] ?? 'N/A') ?>
                    </div>
                </div>

                <!-- Tour Info -->
                <div class="info-card">
                    <h6 class="mb-3"><i class="fas fa-route"></i> Tour</h6>
                    <div class="mb-2">
                        <strong>Tour:</strong> <?= htmlspecialchars($rating['tour_title']) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Ngày đánh giá:</strong> <?= date('d/m/Y', strtotime($rating['created_at'])) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Mã booking:</strong> #<?= htmlspecialchars($rating['booking_id']) ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Rater Info -->
                <div class="info-card">
                    <h6 class="mb-3"><i class="fas fa-user"></i> Người đánh giá</h6>
                    <div class="mb-2">
                        <?php
                        $raterIcons = ['customer' => 'fa-user', 'staff' => 'fa-user-tie', 'admin' => 'fa-user-shield'];
                        $raterLabels = ['customer' => 'Khách hàng', 'staff' => 'Nhân viên', 'admin' => 'Admin'];
                        ?>
                        <i class="fas <?= $raterIcons[$rating['rater_type']] ?>"></i>
                        <?= $raterLabels[$rating['rater_type']] ?>
                    </div>
                    <?php if (!empty($rating['rater_name'])): ?>
                    <div class="mb-2">
                        <strong>Tên:</strong> <?= htmlspecialchars($rating['rater_name']) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($rating['rater_email'])): ?>
                    <div class="mb-2">
                        <strong>Email:</strong> <?= htmlspecialchars($rating['rater_email']) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Status Info -->
                <div class="info-card">
                    <h6 class="mb-3"><i class="fas fa-info-circle"></i> Trạng thái</h6>
                    <div class="mb-2">
                        <?php
                        $statusColors = [
                            'pending' => 'warning',
                            'approved' => 'success', 
                            'rejected' => 'danger',
                            'hidden' => 'secondary'
                        ];
                        $statusLabels = [
                            'pending' => 'Chờ duyệt',
                            'approved' => 'Đã duyệt',
                            'rejected' => 'Từ chối', 
                            'hidden' => 'Ẩn'
                        ];
                        $status = $rating['status'];
                        ?>
                        <span class="badge bg-<?= $statusColors[$status] ?> status-badge">
                            <?= $statusLabels[$status] ?>
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Ngày đánh giá:</strong> <?= date('d/m/Y H:i', strtotime($rating['rated_at'])) ?>
                    </div>
                    <?php if (!empty($rating['approved_at'])): ?>
                    <div class="mb-2">
                        <strong>Ngày duyệt:</strong> <?= date('d/m/Y H:i', strtotime($rating['approved_at'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Comments -->
        <?php if (!empty($rating['comment'])): ?>
        <div class="comment-box">
            <h6 class="mb-2"><i class="fas fa-comment"></i> Nhận xét</h6>
            <p class="mb-0"><?= nl2br(htmlspecialchars($rating['comment'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Pros and Cons -->
        <?php if (!empty($rating['pros']) || !empty($rating['cons'])): ?>
        <div class="pros-cons">
            <?php if (!empty($rating['pros'])): ?>
            <div class="pros-box">
                <h6 class="mb-2"><i class="fas fa-thumbs-up"></i> Điểm mạnh</h6>
                <p class="mb-0"><?= nl2br(htmlspecialchars($rating['pros'])) ?></p>
            </div>
            <?php endif; ?>
            <?php if (!empty($rating['cons'])): ?>
            <div class="cons-box">
                <h6 class="mb-2"><i class="fas fa-thumbs-down"></i> Điểm cần cải thiện</h6>
                <p class="mb-0"><?= nl2br(htmlspecialchars($rating['cons'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <?php if ($rating['status'] === 'pending'): ?>
                    <a href="<?= BASE_URL ?>?r=guide_ratings_approve&id=<?= $rating['id'] ?>" class="btn btn-success" onclick="return confirm('Duyệt đánh giá này?')">
                        <i class="fas fa-check"></i> Duyệt
                    </a>
                    <a href="<?= BASE_URL ?>?r=guide_ratings_reject&id=<?= $rating['id'] ?>" class="btn btn-danger" onclick="return confirm('Từ chối đánh giá này?')">
                        <i class="fas fa-times"></i> Từ chối
                    </a>
                    <?php elseif ($rating['status'] === 'approved'): ?>
                    <a href="<?= BASE_URL ?>?r=guide_ratings_hide&id=<?= $rating['id'] ?>" class="btn btn-secondary" onclick="return confirm('Ẩn đánh giá này?')">
                        <i class="fas fa-eye-slash"></i> Ẩn
                    </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>?r=guide_ratings" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
