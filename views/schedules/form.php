<?php
$errors = flash('errors') ?? [];
$old = flash('old') ?? [];
$editing = isset($schedule) && !empty($schedule);
$title = $editing ? 'Sửa lịch tour' : 'Thêm lịch tour';
$formAction = $editing ? BASE_URL . '?r=schedules_update&id=' . (int)$schedule['id'] : BASE_URL . '?r=schedules_store';
$preselectTourId = isset($_GET['tour_id']) ? (int)$_GET['tour_id'] : 0;
if ($preselectTourId > 0 && empty($old['tour_id']) && empty($schedule['tour_id'])) {
    $old['tour_id'] = $preselectTourId;
}
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3
    }

    .sidebar {}

    .sidebar h3 {
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        font-size: 16px
    }

    
    </style>
</head>

<body>
    <?php $current_page='schedules'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0"><?= $title ?></h3>
            <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=schedules">Quay lại</a>
        </div>

        <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $message): ?>
                <li><?= htmlspecialchars($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="post" action="<?= $formAction ?>">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Thông tin lịch tour</div>
                <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tour <span class="text-danger">*</span></label>
                    <select name="tour_id" class="form-select form-select-lg" required>
                        <option value="">-- Chọn tour --</option>
                        <?php foreach ($tours as $t): ?>
                        <?php $selected = ($old['tour_id'] ?? ($schedule['tour_id'] ?? '')) == $t['id'] ? 'selected' : ''; ?>
                        <option value="<?= $t['id'] ?>" <?= $selected ?>><?= htmlspecialchars($t['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Ngày đi <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control form-control-lg" required
                        value="<?= htmlspecialchars($old['start_date'] ?? ($schedule['start_date'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Ngày về <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control form-control-lg" required
                        value="<?= htmlspecialchars($old['end_date'] ?? ($schedule['end_date'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">HDV</label>
                    <select name="guide_user_id" class="form-select form-select-lg">
                        <option value="">-- Chọn HDV --</option>
                        <?php foreach ($guides as $g): ?>
                        <?php $selGuide = ($old['guide_user_id'] ?? ($schedule['guide_user_id'] ?? '')) == $g['id'] ? 'selected' : ''; ?>
                        <option value="<?= $g['id'] ?>" <?= $selGuide ?>><?= htmlspecialchars($g['full_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tài xế</label>
                    <input type="number" name="driver_user_id" class="form-control form-control-lg" placeholder="ID tài xế"
                        value="<?= htmlspecialchars($old['driver_user_id'] ?? ($schedule['driver_user_id'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Sức chứa tối đa</label>
                    <input type="number" name="max_capacity" class="form-control form-control-lg"
                        value="<?= htmlspecialchars($old['max_capacity'] ?? ($schedule['max_capacity'] ?? 20)) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giá người lớn</label>
                    <input type="number" name="price_adult" class="form-control form-control-lg" min="0" step="1000"
                        value="<?= htmlspecialchars($old['price_adult'] ?? ($schedule['price_adult'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giá trẻ em</label>
                    <input type="number" name="price_child" class="form-control form-control-lg" min="0" step="1000"
                        value="<?= htmlspecialchars($old['price_child'] ?? ($schedule['price_child'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giá em bé</label>
                    <input type="number" name="price_infant" class="form-control form-control-lg" min="0" step="1000"
                        value="<?= htmlspecialchars($old['price_infant'] ?? ($schedule['price_infant'] ?? '')) ?>">
                </div>
            </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary btn-lg">Lưu lịch</button>
            </div>
        </form>
    </div>
</body>

</html>
