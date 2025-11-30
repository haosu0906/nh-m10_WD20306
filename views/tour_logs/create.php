<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Thêm nhật ký tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Thêm nhật ký tour</h3>
        <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=tour_logs">Quay lại</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Tour</label>
            <select name="tour_id" class="form-select" required>
                <option value="">-- Chọn tour --</option>
                <?php foreach ($tours as $tour): ?>
                    <option value="<?= (int)$tour['id'] ?>" <?= (!empty($tourId) && $tourId==$tour['id'])?'selected':'' ?>>
                        <?= htmlspecialchars($tour['title'] ?? ('#'.$tour['id'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Thời gian ghi nhật ký</label>
            <input type="datetime-local" name="log_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Thời tiết</label>
            <input type="text" name="weather" class="form-control" placeholder="Ví dụ: Nắng nhẹ, Mưa, Nhiều mây...">
        </div>

        <div class="mb-3">
            <label class="form-label">Sự kiện / diễn biến trong tour *</label>
            <textarea name="incident_details" rows="4" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Phản hồi của khách</label>
            <textarea name="customer_feedback" rows="3" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh (có thể chọn nhiều)</label>
            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
            <div class="form-text">Ảnh sẽ được lưu cùng nhật ký tour để HDV và quản lý xem lại.</div>
        </div>

        <button class="btn btn-primary">Lưu nhật ký</button>
    </form>
</div>
</body>
</html>
