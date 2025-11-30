<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Đánh giá HDV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .badge {
            display: inline-block;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            line-height: 1.2;
        }
        .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Đánh giá Hướng dẫn viên</h2>
        
        <div class="mb-3">
            <a href="<?= BASE_URL ?>?r=guide_ratings_create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm đánh giá
            </a>
        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Hướng dẫn viên</th>
                    <th>Khách hàng</th>
                    <th>Điểm</th>
                    <th>Nhận xét</th>
                    <th>Ngày</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($ratings)): foreach($ratings as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['guide_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($r['rater_name'] ?? 'Khách hàng') ?></td>
                    <td>
                        <span class="badge bg-warning text-dark"><?= $r['rating'] ?>/5</span>
                        <?php if (!empty($r['comment'])): ?>
                        <br><small><?= htmlspecialchars(mb_substr($r['comment'], 0, 50)) ?>...</small>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['tour_name'] ?? 'N/A') ?></td>
                    <td><?= date('d/m/Y', strtotime($r['rating_date'] ?? 'now')) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>?r=guide_ratings_show&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-info">Xem</a>
                        <a href="<?= BASE_URL ?>?r=guide_ratings_edit&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <a href="<?= BASE_URL ?>?r=guide_ratings_delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
