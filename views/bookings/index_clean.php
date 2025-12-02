<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Quản lý Đặt Tour</title>
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

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <input type="hidden" name="r" value="booking">
                        <div class="col-md-4">
                            <label class="form-label">Lọc theo trạng thái</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" <?= ($_GET['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                <option value="deposit" <?= ($_GET['status'] ?? '') == 'deposit' ? 'selected' : '' ?>>Đã cọc</option>
                                <option value="completed" <?= ($_GET['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Hoàn tất</option>
                                <option value="canceled" <?= ($_GET['status'] ?? '') == 'canceled' ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã booking</th>
                                    <th>Tour</th>
                                    <th>Ngày đi</th>
                                    <th>Số khách</th>
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
                                        <td>
                                            <strong><?= $booking['booking_code'] ?? 'BK' . $booking['id'] ?></strong>
                                            <br><small class="text-muted">#<?= $booking['id'] ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($booking['tour_name'] ?? 'N/A') ?></td>
                                        <td><?= $booking['start_date'] ? date('d/m/Y', strtotime($booking['start_date'])) : 'Chưa có' ?></td>
                                        <td><?= $booking['total_guests'] ?></td>
                                        <td><?= number_format($booking['total_price']) ?> đ</td>
                                        <td>
                                            <span class="badge <?= $statusClass ?>">
                                                <?= ucfirst($booking['booking_status'] ?? 'pending') ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($booking['date_booked'])) ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>?r=booking/detail&id=<?= $booking['id'] ?>" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye me-1"></i> Xem chi tiết
                                            </a>
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
