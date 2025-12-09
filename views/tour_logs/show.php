<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Chi tiết nhật ký tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
</head>
<body>
<?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
<?php $current_page='tour_logs'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Chi tiết nhật ký tour</h3>
        <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=tour_logs">Quay lại danh sách</a>
    </div>

    <?php if (!$log): ?>
        <div class="alert alert-danger">Không tìm thấy nhật ký.</div>
    <?php else: ?>
        <div class="card mb-3">
            <div class="card-body">
                <h4><?= htmlspecialchars($log['tour_title'] ?? ('Tour #'.$log['tour_id'])) ?></h4>
                <p class="mb-1"><b>Thời gian:</b> <?= htmlspecialchars($log['log_date']) ?></p>
                <p class="mb-1"><b>Thời tiết:</b> <?= htmlspecialchars($log['weather']) ?></p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5>Sự kiện / diễn biến</h5>
                <p><?= nl2br(htmlspecialchars($log['incident_details'])) ?></p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5>Phản hồi của khách</h5>
                <p><?= nl2br(htmlspecialchars($log['customer_feedback'])) ?></p>
            </div>
        </div>

        <?php if (!empty($log['images'])): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Hình ảnh đính kèm</h5>
                    <div class="row g-3">
                        <?php foreach ($log['images'] as $img): ?>
                            <div class="col-6 col-md-3">
                                <div class="card h-100">
                                    <img src="<?= PATH_ASSETS_UPLOADS . $img['image_path'] ?>" class="card-img-top" style="height:140px;object-fit:cover;">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
