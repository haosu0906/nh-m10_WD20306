<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Quản lý Nhân Sự</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Quản lý Nhân Sự</h2>
        
        <div class="mb-3">
            <a href="<?= BASE_URL ?>?r=staff_create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm nhân sự
            </a>
        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên nhân viên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($items)): foreach($items as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td>
                        <?php
                        $roleInfo = [
                            'admin' => 'Admin',
                            'manager' => 'Quản lý',
                            'staff' => 'Nhân viên',
                        ];
                        $role = $roleInfo[$row['role']] ?? 'Unknown';
                        ?>
                        <span class="badge bg-danger"><?= $role ?></span>
                    </td>
                    <td>
                        <?php if ($row['is_active']): ?>
                            <span class="badge bg-success">Hoạt động</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Vô hiệu</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>?r=staff_edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <a href="<?= BASE_URL ?>?r=staff_show&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-info">Xem</a>
                        <a href="<?= BASE_URL ?>?r=staff_delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="7" class="text-center">Không có dữ liệu</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
