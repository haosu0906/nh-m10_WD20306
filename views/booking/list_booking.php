<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Quản lý Booking</title>
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

    .card-header {
        font-weight: 600;
    }
    </style>
</head>

<body>
    <!-- Sidebar (include standard template) -->
    <?php
        $current_page = 'booking';
        require_once __DIR__ . '/../../assets/templates/sidebar.php';
    ?>

    <!-- Main content -->
    <main class="main">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h1 class="mb-2">📋 Quản lý Booking</h1>
                <p class="text-muted mb-0">Theo dõi và quản lý tình trạng booking</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>?r=booking_create" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Tạo booking mới
                </a>
            </div>
        </div>

        <!-- Table danh sách booking -->
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Tour</th>
                            <th>Ngày đặt</th>
                            <th>Số khách</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($items)): foreach($items as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['id']) ?></td>
                            <td><?= htmlspecialchars($b['customer_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($b['tour_name'] ?? 'N/A') ?></td>
                            <td><?= !empty($b['date_booked']) ? date('d/m/Y H:i', strtotime($b['date_booked'])) : '---' ?></td>
                            <td><?= htmlspecialchars($b['total_guests'] ?? '0') ?></td>
                            <td>
                                <?php
                                    $statusLabels = [
                                        'pending' => 'Chờ xác nhận',
                                        'deposit' => 'Đã cọc',
                                        'completed' => 'Hoàn tất',
                                        'canceled' => 'Hủy'
                                    ];
                                    echo $statusLabels[$b['booking_status'] ?? 'pending'] ?? $b['booking_status'];
                                ?>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="<?= BASE_URL ?>?r=booking_detail&id=<?= $b['id'] ?>">
                                    <i class="fas fa-eye"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Chưa có booking nào</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
