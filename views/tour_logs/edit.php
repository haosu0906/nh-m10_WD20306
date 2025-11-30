<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Chỉnh sửa nhật ký tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Chỉnh sửa nhật ký tour</h3>
        <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=tour_logs_show&id=<?= (int)$log['id'] ?>">Quay lại</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Tour</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($log['tour_title'] ?? ('Tour #'.$log['tour_id'])) ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Thời gian ghi nhật ký</label>
            <input type="datetime-local" name="log_date" class="form-control"
                   value="<?= date('Y-m-d\TH:i', strtotime($log['log_date'])) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Thời tiết</label>
            <input type="text" name="weather" class="form-control" value="<?= htmlspecialchars($log['weather']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Sự kiện / diễn biến trong tour *</label>
            <textarea name="incident_details" rows="4" class="form-control" required><?= htmlspecialchars($log['incident_details']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Phản hồi của khách</label>
            <textarea name="customer_feedback" rows="3" class="form-control"><?= htmlspecialchars($log['customer_feedback']) ?></textarea>
        </div>

        <?php if (!empty($log['images'])): ?>
            <div class="mb-3">
                <label class="form-label">Hình ảnh hiện có</label>
                <div class="row g-3">
                    <?php foreach ($log['images'] as $img): ?>
                        <div class="col-6 col-md-3">
                            <div class="card h-100">
                                <img src="<?= PATH_ASSETS_UPLOADS . $img['image_path'] ?>" class="card-img-top" style="height:120px;object-fit:cover;">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Thêm hình ảnh mới (có thể chọn nhiều)</label>
            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
        </div>

        <button class="btn btn-primary">Lưu thay đổi</button>
    </form>
</div>
</body>
</html>
