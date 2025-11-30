<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Nhật ký tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Nhật ký tour</h3>
        <a class="btn btn-primary" href="<?= BASE_URL ?>?r=tour_logs_create">
            <i class="fas fa-plus"></i> Thêm nhật ký
        </a>
    </div>

    <?php if (empty($logs)): ?>
        <div class="alert alert-info">Chưa có nhật ký tour nào.</div>
    <?php else: ?>
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>Ngày giờ</th>
                <th>Tour</th>
                <th>Thời tiết</th>
                <th>Chi tiết</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['log_date']) ?></td>
                    <td><?= htmlspecialchars($row['tour_title'] ?? ('#'.$row['tour_id'])) ?></td>
                    <td><?= htmlspecialchars($row['weather']) ?></td>
                    <td>
                        <?php
                        $short = mb_substr($row['incident_details'], 0, 80, 'UTF-8');
                        if (mb_strlen($row['incident_details'], 'UTF-8') > 80) {
                            $short .= '...';
                        }
                        echo nl2br(htmlspecialchars($short));
                        ?>
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>?r=tour_logs_show&id=<?= (int)$row['id'] ?>">Xem</a>
                        <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>?r=tour_logs_edit&id=<?= (int)$row['id'] ?>">Sửa</a>
                        <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa nhật ký này?');" href="<?= BASE_URL ?>?r=tour_logs_delete&id=<?= (int)$row['id'] ?>">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
