<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Chi tiết Đặt Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 200px;
            padding: 20px;
            background: linear-gradient(180deg, #667eea, #764ba2);
            color: #fff;
            overflow-y: auto;
        }
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
            margin-bottom: 0.25rem;
        }
        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, .1);
        }
        .main-content {
            margin-left: 200px;
            padding: 86px 20px 20px;
        }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
            border: 2px solid #fff;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân sự</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=payments"><i class="fas fa-credit-card"></i> Thanh toán</a>
        </nav>
    </div>

    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-book me-2"></i>Chi tiết Đặt Tour</h2>
                <a href="<?= BASE_URL ?>?r=booking" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>

            <div class="row">
                <!-- Booking Info -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Thông tin Booking</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-exchange-alt me-1"></i> Chuyển trạng thái
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>?r=booking/updateStatus&id=<?= $booking['id'] ?>&status=pending">Chờ xác nhận</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>?r=booking/updateStatus&id=<?= $booking['id'] ?>&status=deposit">Đã cọc</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>?r=booking/updateStatus&id=<?= $booking['id'] ?>&status=completed">Hoàn tất</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>?r=booking/updateStatus&id=<?= $booking['id'] ?>&status=canceled">Hủy</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Mã booking:</strong> <?= $booking['booking_code'] ?? 'BK' . $booking['id'] ?></p>
                                    <p><strong>Tour:</strong> <?= htmlspecialchars($booking['tour_name']) ?></p>
                                    <p><strong>Ngày khởi hành:</strong> <?= $booking['start_date'] ? date('d/m/Y', strtotime($booking['start_date'])) : 'Chưa có' ?></p>
                                    <p><strong>Số lượng khách:</strong> <?= $booking['total_guests'] ?> người</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Tổng giá:</strong> <?= number_format($booking['total_price']) ?> đ</p>
                                    <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($booking['date_booked'])) ?></p>
                                    <p><strong>Trạng thái:</strong> 
                                        <span class="badge bg-info"><?= ucfirst($booking['booking_status']) ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin khách hàng</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($guests)): ?>
                                <?php foreach ($guests as $index => $guest): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <h6 class="text-muted">Khách hàng <?= $index + 1 ?></h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Họ tên:</strong> <?= htmlspecialchars($guest['full_name']) ?>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>SĐT:</strong> <?= htmlspecialchars($guest['phone']) ?>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Email:</strong> <?= htmlspecialchars($guest['email'] ?? 'N/A') ?>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Giới tính:</strong> 
                                                <?php
                                                $genders = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                                echo $genders[$guest['gender']] ?? 'Khác';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">Chưa có thông tin khách hàng</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Status History -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Lịch sử thay đổi trạng thái</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($statusHistory)): ?>
                                <div class="timeline">
                                    <?php foreach ($statusHistory as $history): ?>
                                        <div class="timeline-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong><?= ucfirst($history['new_status']) ?></strong>
                                                    <?php if ($history['old_status']): ?>
                                                        <small class="text-muted">(từ <?= $history['old_status'] ?>)</small>
                                                    <?php endif; ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        bởi <?= htmlspecialchars($history['changed_by_name'] ?? 'Hệ thống') ?>
                                                    </small>
                                                    <?php if ($history['note']): ?>
                                                        <br><small class="text-info"><?= htmlspecialchars($history['note']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?= date('d/m H:i', strtotime($history['changed_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Chưa có lịch sử thay đổi</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
