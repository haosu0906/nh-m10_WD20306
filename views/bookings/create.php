<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Tạo Đơn Đặt Tour Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
        
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
        .main-content {}
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='booking'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>

    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-bold">
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
                                    <label for="tour_id" class="form-label fw-bold">Chọn tour <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" id="tour_id" name="tour_id" required>
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
                                        <label for="customer_name" class="form-label fw-bold">Họ tên khách hàng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg" id="customer_name" name="customer_name" 
                                               value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_phone" class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control form-control-lg" id="customer_phone" name="customer_phone" 
                                               value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="customer_email" class="form-label fw-bold">Email</label>
                                    <input type="email" class="form-control form-control-lg" id="customer_email" name="customer_email" 
                                           value="<?= htmlspecialchars($_POST['customer_email'] ?? '') ?>">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_date" class="form-label fw-bold">Ngày khởi hành <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control form-control-lg" id="start_date" name="start_date" 
                                               value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" required
                                               min="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="number_of_people" class="form-label fw-bold">Số lượng người <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control form-control-lg" id="number_of_people" name="number_of_people" 
                                               min="1" value="<?= $_POST['number_of_people'] ?? 1 ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="note" class="form-label fw-bold">Ghi chú</label>
                                    <textarea class="form-control form-control-lg" id="note" name="note" rows="3"><?= 
                                        htmlspecialchars($_POST['note'] ?? '') 
                                    ?></textarea>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="<?= BASE_URL ?>?r=booking" class="btn btn-outline-secondary btn-lg me-md-2">Quay lại</a>
                                    <button type="submit" class="btn btn-primary btn-lg">Tạo đơn đặt tour</button>
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
