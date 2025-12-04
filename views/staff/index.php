<?php
require_once __DIR__ . '/../../assets/configs/env.php';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Quản lý Nhân Sự</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    .main{ margin-left:200px; padding:86px 22px 22px }
    .table{ font-size:13.5px }
    .badge{ border-radius:.4rem }
  </style>
</head>
<body>
  <?php $current_page='staff'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
  <main class="main">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Quản lý Nhân Sự</h3>
      <a href="<?= BASE_URL ?>?r=staff_create" class="btn btn-primary"><i class="fa-solid fa-user-plus me-2"></i>Thêm nhân sự</a>
    </div>

    <?php if(isset($_GET['created'])): ?>
      <div class="alert alert-success">Đã thêm nhân sự</div>
    <?php endif; ?>
    <?php if(isset($_GET['updated'])): ?>
      <div class="alert alert-success">Đã cập nhật nhân sự</div>
    <?php endif; ?>
    <?php if(isset($_GET['deleted'])): ?>
      <div class="alert alert-success">Đã xóa nhân sự</div>
    <?php endif; ?>

    <div class="card shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Điện thoại</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th class="text-center">Hành động</th>
              </tr>
            </thead>
            <tbody>
            <?php if (!empty($staff)): foreach ($staff as $row): ?>
              <tr>
                <td>#<?= (int)$row['id'] ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($row['full_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                <td>
                  <?php 
                    $role = $row['role'] ?? 'staff';
                    $map = ['admin'=>['Admin','danger'],'manager'=>['Quản lý','warning'],'staff'=>['Nhân viên','info']];
                    $info = $map[$role] ?? [$role,'secondary'];
                  ?>
                  <span class="badge bg-<?= $info[1] ?>"><?= $info[0] ?></span>
                </td>
                <td>
                  <?php $active = (int)($row['status'] ?? $row['is_active'] ?? 1); ?>
                  <span class="badge bg-<?= $active? 'success':'secondary' ?>"><?= $active? 'Hoạt động':'Ngưng' ?></span>
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= BASE_URL ?>?r=staff_edit&id=<?= (int)$row['id'] ?>" class="btn btn-outline-primary" title="Sửa"><i class="fa-regular fa-pen-to-square"></i></a>
                    <a href="<?= BASE_URL ?>?r=staff_delete&id=<?= (int)$row['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Xóa nhân sự này?')" title="Xóa"><i class="fa-regular fa-trash-can"></i></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="7" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
