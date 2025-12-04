<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Phân công HDV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='guide_assignments'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <main class="main" style="margin-left:200px;padding:86px 22px 22px">
        <h2>Phân công Hướng dẫn viên</h2>
        
        <div class="mb-3">
            <a href="<?= BASE_URL ?>?r=guide_assignments_create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm phân công
            </a>
        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tour</th>
                    <th>Hướng dẫn viên</th>
                    <th>Ngày</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($assignments)): foreach($assignments as $assignment): ?>
                <tr>
                    <td><?= htmlspecialchars($assignment['id']) ?></td>
                    <td><?= htmlspecialchars($assignment['tour_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($assignment['guide_name'] ?? 'N/A') ?></td>
                    <td><?= date('d/m/Y', strtotime($assignment['assignment_date'] ?? 'now')) ?></td>
                    <td>
                        <?php
                        $statusColors = [
                            'pending' => ['bg-warning', 'Chờ xác nhận'],
                            'confirmed' => ['bg-success', 'Đã xác nhận'],
                            'completed' => ['bg-primary', 'Đã hoàn thành'],
                            'cancelled' => ['bg-secondary', 'Đã hủy']
                        ];
                        $status = $assignment['assignment_status'] ?? 'pending';
                        $info = $statusColors[$status] ?? ['bg-secondary', 'Unknown'];
                        ?>
                        <span class="badge <?= $info[0] ?>"><?= $info[1] ?></span>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>?r=guide_assignments_edit&id=<?= $assignment['id'] ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <a href="<?= BASE_URL ?>?r=guide_assignments_show&id=<?= $assignment['id'] ?>" class="btn btn-sm btn-outline-info">Xem</a>
                        <a href="<?= BASE_URL ?>?r=guide_assignments_delete&id=<?= $assignment['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
