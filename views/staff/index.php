<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Quản lý Nhân Sự</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root { --accent:#667eea; --accent-dark:#5568d3 }
    .sidebar{position:fixed;left:0;top:0;bottom:0;width:200px;padding:20px;background:linear-gradient(180deg,var(--accent),#764ba2);color:#fff;overflow:auto}
    .sidebar h3{font-weight:700;margin-bottom:1rem;text-align:center;font-size:16px}
    .nav-link{color:rgba(255,255,255,.95);display:flex;align-items:center;gap:.6rem;padding:.6rem;border-radius:.5rem;text-decoration:none}
    .nav-link:hover,.nav-link.active{background:rgba(255,255,255,.1)}
    .main{margin-left:200px;padding:22px}
    .badge{display:inline-block;line-height:1.2}
    </style>
</head>
<body>
    <?php $current_page='staff'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>

    <main class="main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">Quản lý Nhân Sự</h3>
                <p class="text-muted mb-0">Danh sách nhân sự (ngoại trừ HDV)</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>?r=staff_create" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Thêm nhân sự
                </a>
                <a href="<?= BASE_URL ?>?r=staff_all_users" class="btn btn-outline-secondary">
                    <i class="fas fa-users me-2"></i>Xem tất cả Users
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Danh sách</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">ID</th>
                                <th class="border-0">Tên</th>
                                <th class="border-0">Email</th>
                                <th class="border-0">SĐT</th>
                                <th class="border-0">Vai trò</th>
                                <th class="border-0">Trạng thái</th>
                                <th class="border-0 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): foreach ($items as $row): ?>
                                <tr>
                                    <td><?= (int)$row['id'] ?></td>
                                    <td><?= htmlspecialchars((string)($row['full_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($row['email'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($row['phone'] ?? '')) ?></td>
                                    <td>
                                        <?php
                                        $roleInfo = [
                                            'admin' => ['Admin','bg-danger'],
                                            'manager' => ['Quản lý','bg-warning'],
                                            'staff' => ['Nhân viên','bg-info'],
                                            'traveler' => ['Khách','bg-secondary']
                                        ];
                                        $role = $roleInfo[$row['role'] ?? 'staff'] ?? ['Khác','bg-secondary'];
                                        ?>
                                        <span class="badge <?= $role[1] ?>"><?= $role[0] ?></span>
                                    </td>
                                    <td>
                                        <?php if ((int)($row['is_active'] ?? 0) === 1): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Vô hiệu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>?r=staff_edit&id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                                        <a href="<?= BASE_URL ?>?r=staff_delete&id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                                            <h5>Chưa có nhân sự nào</h5>
                                            <p>Thêm nhân sự mới để bắt đầu</p>
                                            <a href="<?= BASE_URL ?>?r=staff_create" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Thêm nhân sự
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
