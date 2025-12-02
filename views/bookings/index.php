<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Quản lý Đặt Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
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
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php $current_page='booking'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-book me-2"></i>Quản lý Đặt Tour</h2>
                <a href="<?= BASE_URL ?>?r=booking/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tạo đơn đặt tour
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Tour</th>
                                    <th>Ngày đi</th>
                                    <th>Số người</th>
                                    <th>Giá tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <?php
                                    $statusClass = [
                                        'pending' => 'bg-warning',
                                        'deposit' => 'bg-info',
                                        'completed' => 'bg-success',
                                        'canceled' => 'bg-danger'
                                    ][$booking['booking_status']] ?? 'bg-secondary';
                                    ?>
                                    <tr>
                                        <td>#<?= $booking['id'] ?></td>
                                        <td>
                                            <div>ID Khách: <?= $booking['customer_user_id'] ?? 'N/A' ?></div>
                                            <small class="text-muted">ID Sales: <?= $booking['sales_user_id'] ?? 'N/A' ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($booking['tour_name'] ?? 'N/A') ?></td>
                                        <td>Chưa có ngày đi</td>
                                        <td><?= $booking['total_guests'] ?? 0 ?></td>
                                        <td><?= number_format($booking['total_price']) ?> đ</td>
                                        <td>
                                            <span class="badge <?= $statusClass ?>">
                                                <?= ucfirst($booking['booking_status'] ?? 'pending') ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($booking['date_booked'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <?php if ($booking['booking_status'] !== 'canceled'): ?>
                                                    <?php if ($booking['booking_status'] !== 'deposit'): ?>
                                                        <a href="<?= BASE_URL ?>?r=booking/updateStatus&id=<?= $booking['id'] ?>&status=deposit" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Xác nhận cọc">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($booking['booking_status'] !== 'completed'): ?>
                                                        <a href="<?= BASE_URL ?>?r=booking/updateStatus&id=<?= $booking['id'] ?>&status=completed" 
                                                           class="btn btn-sm btn-outline-success" 
                                                           title="Đánh dấu hoàn thành">
                                                            <i class="fas fa-flag-checkered"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <a href="<?= BASE_URL ?>?r=booking/updateStatus&id=<?= $booking['id'] ?>&status=canceled" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       title="Hủy đơn hàng"
                                                       onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= BASE_URL ?>?r=booking/detail&id=<?= $booking['id'] ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye me-1"></i> Xem chi tiết
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
