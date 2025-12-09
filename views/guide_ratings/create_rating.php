<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Thêm Đánh giá HDV</title>
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

    .main-content {}

    .rating-stars {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .star-input {
        font-size: 1.5rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }

    .star-input:hover,
    .star-input.active {
        color: #ffc107;
    }

    .rating-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .rating-section {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .rating-value {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .booking-info {
        background: #e7f3ff;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='guide_ratings'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Thêm Đánh giá HDV</h3>
            <a href="<?= BASE_URL ?>?r=guide_ratings" class="btn btn-outline-secondary">Quay lại</a>
        </div>

        <?php $flash = function_exists('flash_get') ? flash_get() : null; if (!empty($flash)): ?>
        <div class="alert alert-<?= $flash['type']==='error'?'danger':'success' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message'] ?? '') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($booking): ?>
        <div class="booking-info">
            <h6><i class="fas fa-info-circle"></i> Thông tin Booking</h6>
            <div class="row">
                <div class="col-md-6">
                    <strong>Mã booking:</strong> #<?= htmlspecialchars($booking['id']) ?><br>
                    <strong>Khách hàng:</strong> <?= htmlspecialchars($booking['full_name']) ?><br>
                    <strong>Tour:</strong> <?= htmlspecialchars($booking['title']) ?>
                </div>
                <div class="col-md-6">
                    <strong>Ngày đi:</strong> <?= date('d/m/Y', strtotime($booking['date_booked'])) ?><br>
                    <strong>HDV:</strong> <?= htmlspecialchars($guide['full_name']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>?r=guide_ratings_store">
            <input type="hidden" name="guide_id" value="<?= $guide['id'] ?? 0 ?>">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?? 0 ?>">
            <input type="hidden" name="schedule_id" value="0">
            <input type="hidden" name="rater_type" value="<?= $_GET['rater_type'] ?? 'staff' ?>">
            <input type="hidden" name="rater_id" value="1">

            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">Thông tin đánh giá</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hướng dẫn viên</label>
                            <select name="guide_id" class="form-select form-select-lg" required>
                                <option value="">-- Chọn hướng dẫn viên --</option>
                                <?php if (!empty($guides)): foreach ($guides as $gOpt): ?>
                                <option value="<?= (int)$gOpt['id'] ?>" <?= (!empty($guide['id']) && (int)$guide['id']===(int)$gOpt['id'])?'selected':'' ?>>
                                    <?= htmlspecialchars($gOpt['full_name'] ?? ('#'.$gOpt['id'])) ?>
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Loại người đánh giá</label>
                            <select name="rater_type" class="form-select form-select-lg" required>
                                <option value="customer" <?= (($_GET['rater_type'] ?? '') === 'customer') ? 'selected' : '' ?>>Khách hàng</option>
                                <option value="staff" <?= (($_GET['rater_type'] ?? '') === 'staff' ? 'selected' : '') ?>>Nhân viên</option>
                                <option value="admin" <?= (($_GET['rater_type'] ?? '') === 'admin' ? 'selected' : '') ?>>Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Sections -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light fw-bold">
                    <h6 class="mb-0"><i class="fas fa-star"></i> Đánh giá chi tiết</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="rating-item">
                                <div class="rating-label">Đánh giá chung</div>
                                <div class="rating-stars" data-rating="overall">
                                    <i class="fas fa-star star-input" data-value="1"></i>
                                    <i class="fas fa-star star-input" data-value="2"></i>
                                    <i class="fas fa-star star-input" data-value="3"></i>
                                    <i class="fas fa-star star-input" data-value="4"></i>
                                    <i class="fas fa-star star-input" data-value="5"></i>
                                </div>
                                <input type="hidden" name="rating" value="5" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light fw-bold">
                    <h6 class="mb-0"><i class="fas fa-comment"></i> Nhận xét</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Nhận xét chi tiết</label>
                            <textarea name="comment" rows="4" class="form-control form-control-lg" placeholder="Hãy chia sẻ trải nghiệm của bạn với HDV..." required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Điểm mạnh</label>
                            <textarea name="pros" rows="3" class="form-control form-control-lg" placeholder="Những điểm bạn thấy HDV làm tốt..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Điểm cần cải thiện</label>
                            <textarea name="cons" rows="3" class="form-control form-control-lg" placeholder="Những điểm HDV có thể cải thiện..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
                <a href="<?= BASE_URL ?>?r=guide_ratings" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>

    <script>
    // Rating stars interaction đơn giản
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.rating-stars').forEach(function(container){
            const stars = container.querySelectorAll('.star-input');
            const input = container.nextElementSibling;
            function paint(value){
                stars.forEach(function(star, idx){
                    star.style.color = (idx < value) ? '#ffc107' : '#ddd';
                    star.classList.toggle('active', idx < value);
                });
            }
            // init from current value
            paint(parseInt(input.value||'0',10));
            stars.forEach(function(star){
                star.addEventListener('click', function(){
                    const value = parseInt(this.getAttribute('data-value'),10);
                    input.value = value;
                    paint(value);
                });
            });
        });
    });
    </script>
</body>

</html>
