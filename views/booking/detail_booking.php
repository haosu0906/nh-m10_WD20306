<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Chi tiết Booking <?= isset($item['id']) ? booking_code_from_id($item['id']) : '' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3;
    }

    body {
        background: #f8f9fa;
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
        overflow: auto;
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
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, .1);
    }

    .main {
        margin-left: 200px;
        padding: 22px;
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
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân sự</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_dashboard"><i class="fas fa-door-open"></i> Portal HDV</a>
        </nav>
    </div>

    <!-- Main content -->
    <main class="main">
        <?php if ($item): ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Chi tiết Booking <?= htmlspecialchars(booking_code_from_id($item['id'])) ?></h3>
                <p class="text-muted mb-0">Thông tin chi tiết và quản lý trạng thái booking</p>
            </div>
        </div>

        <?php
        $statusLabels = [
            'pending' => 'Chờ xác nhận',
            'deposit' => 'Đã cọc',
            'completed' => 'Hoàn tất',
            'canceled' => 'Hủy'
        ];
        ?>

        <!-- Thông tin tổng quan -->
        <div class="card mb-4">
            <div class="card-body row">
                <div class="col-md-6">
                    <p><b>Mã booking:</b> <?= htmlspecialchars(booking_code_from_id($item['id'])) ?></p>
                    <p><b>Tour:</b> <?= htmlspecialchars($tour['title'] ?? ('#' . $item['tour_id'])) ?></p>
                    <p><b>Ngày đặt:</b> <?= !empty($item['date_booked']) ? date('d/m/Y H:i', strtotime($item['date_booked'])) : '---' ?></p>
                    <p><b>Tổng khách:</b> <?= (int)$item['total_guests'] ?></p>
                </div>
                <div class="col-md-6">
                    <p><b>Khách hàng:</b>
                        <?= htmlspecialchars($customer['full_name'] ?? ('ID: ' . ($item['customer_user_id'] ?? '---'))) ?>
                        <?php if (!empty($customer['email'])): ?>
                            (<?= htmlspecialchars($customer['email']) ?>)
                        <?php endif; ?>
                    </p>
                    <p><b>Nhân viên sales:</b>
                        <?= htmlspecialchars($sales['full_name'] ?? ('ID: ' . ($item['sales_user_id'] ?? '---'))) ?>
                    </p>
                    <p><b>Tổng tiền:</b> <?= number_format((float)($item['total_price'] ?? 0), 0, ',', '.') ?> đ</p>
                </div>
            </div>
        </div>

        <!-- Thông tin trạng thái và form cập nhật -->
        <div class="card mb-4">
            <div class="card-body">
                <p><b>Trạng thái hiện tại:</b>
                    <?= $statusLabels[$item['booking_status']] ?? htmlspecialchars($item['booking_status']) ?>
                </p>

                <form method="post" action="<?= BASE_URL ?>?r=booking_update_status">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">

                    <label>Trạng thái mới</label>
                    <select class="form-select" name="status" required>
                        <?php foreach ($statusLabels as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($item['booking_status']==$key)?'selected':'' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label class="mt-2">Ghi chú</label>
                    <textarea class="form-control" name="note"></textarea>

                    <button class="btn btn-primary mt-3">Cập nhật</button>
                </form>
            </div>
        </div>

        <!-- Danh sách khách trong booking -->
        <div class="card mb-4">
            <div class="card-body">
                <h4>Danh sách khách</h4>
                <?php if (!empty($guests)): ?>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Họ tên</th>
                                <th>Giới tính</th>
                                <th>Ngày sinh</th>
                                <th>CMND/CCCD</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($guests as $g): ?>
                            <tr>
                                <td><?= htmlspecialchars($g['full_name']) ?></td>
                                <td><?= htmlspecialchars($g['gender']) ?></td>
                                <td><?= !empty($g['dob']) ? date('d/m/Y', strtotime($g['dob'])) : '---' ?></td>
                                <td><?= htmlspecialchars($g['id_document_no']) ?></td>
                                <td><?= htmlspecialchars($g['notes']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Chưa có thông tin khách.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lịch sử thay đổi trạng thái -->
        <div class="card">
            <div class="card-body">
                <h4>Lịch sử thay đổi</h4>
                <?php if ($logs && count($logs) > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Người đổi</th>
                            <th>Từ</th>
                            <th>Đến</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($log['changed_at'])) ?></td>
                            <td><?= htmlspecialchars($log['changed_name'] ?? '---') ?></td>
                            <td><?= $statusLabels[$log['old_status']] ?? htmlspecialchars($log['old_status']) ?></td>
                            <td><?= $statusLabels[$log['new_status']] ?? htmlspecialchars($log['new_status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-muted">Chưa có lịch sử thay đổi trạng thái</p>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <p class="text-danger">Không tìm thấy booking này.</p>
        <?php endif; ?>
    </main>
</body>

</html>