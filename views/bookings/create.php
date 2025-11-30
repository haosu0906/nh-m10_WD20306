<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Tạo Đơn Đặt Tour Mới</title>
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Tạo đơn đặt tour mới</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="<?= BASE_URL ?>?r=booking/store">
                                <div class="mb-3">
                                    <label for="tour_id" class="form-label">Chọn tour <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tour_id" name="tour_id" required>
                                        <option value="">-- Chọn tour --</option>
                                        <?php foreach ($tours as $tour): ?>
                                            <option value="<?= $tour['id'] ?>" 
                                                    <?= ($_POST['tour_id'] ?? '') == $tour['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($tour['title']) ?> 
                                                (<?= number_format($tour['price']) ?> đ/người)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_name" class="form-label">Họ tên khách hàng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                               value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                               value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="customer_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                           value="<?= htmlspecialchars($_POST['customer_email'] ?? '') ?>">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_date" class="form-label">Ngày khởi hành <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" required
                                               min="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="number_of_people" class="form-label">Số lượng người <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="number_of_people" name="number_of_people" 
                                               min="1" value="<?= $_POST['number_of_people'] ?? 1 ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="note" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="note" name="note" rows="3"><?= 
                                        htmlspecialchars($_POST['note'] ?? '') 
                                    ?></textarea>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="<?= BASE_URL ?>?r=booking" class="btn btn-secondary me-md-2">Quay lại</a>
                                    <button type="submit" class="btn btn-primary">Tạo đơn đặt tour</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
