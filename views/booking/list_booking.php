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
    <!-- Sidebar -->
    <div class="sidebar">
        <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân sự</a>
        </nav>
    </div>

    <!-- Main content -->
    <main class="main">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Quản lý Booking</h3>
                <p class="text-muted mb-0">Theo dõi và quản lý tình trạng booking</p>
            </div>
        </div>

        <!-- Form lọc trạng thái -->
        <form method="get" class="mb-3">
            <input type="hidden" name="r" value="booking">
            <select name="status" class="form-select w-25" onchange="this.form.submit()">
                <option value="">Tất cả</option>
                <option value="pending" <?= (($_GET['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Chờ xác nhận
                </option>
                <option value="deposit" <?= (($_GET['status'] ?? '') === 'deposit') ? 'selected' : '' ?>>Đã cọc</option>
                <option value="completed" <?= (($_GET['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Hoàn tất
                </option>
                <option value="canceled" <?= (($_GET['status'] ?? '') === 'canceled') ? 'selected' : '' ?>>Hủy</option>
            </select>
        </form>

        <!-- Table danh sách booking -->
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Tour ID</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($items)): foreach($items as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['id']) ?></td>
                            <td><?= htmlspecialchars($b['user_id']) ?></td>
                            <td><?= htmlspecialchars($b['tour_id']) ?></td>
                            <td><?= !empty($b['booking_date']) ? date('d/m/Y', strtotime($b['booking_date'])) : '---' ?>
                            </td>
                            <td>
                                <?php
                                    $statusLabels = [
                                        'pending' => 'Chờ xác nhận',
                                        'deposit' => 'Đã cọc',
                                        'completed' => 'Hoàn tất',
                                        'canceled' => 'Hủy'
                                    ];
                                    echo $statusLabels[$b['status']] ?? $b['status'];
                                ?>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-primary"
                                    href="<?= BASE_URL ?>?r=booking_detail&id=<?= $b['id'] ?>">Chi tiết</a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Chưa có dữ liệu</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>