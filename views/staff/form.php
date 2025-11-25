<?php

$editing = isset($item) && !empty($item);
$title = $editing ? 'Sửa nhân sự' : 'Thêm nhân sự';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title><?= $title ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    :root{--accent:#667eea;--accent-dark:#5568d3}
    .sidebar{position:fixed;left:0;top:0;bottom:0;width:200px;padding:20px;background:linear-gradient(180deg,var(--accent),#764ba2);color:#fff;overflow:auto}
    .sidebar h3{font-weight:700;margin-bottom:1rem;text-align:center;font-size:16px}
    .nav-link{color:rgba(255,255,255,.95);display:flex;align-items:center;gap:.6rem;padding:.6rem;border-radius:.5rem;text-decoration:none}
    .nav-link:hover, .nav-link.active{background:rgba(255,255,255,.1)}
    .main{margin-left:200px;padding:22px}
  </style>
</head>
<body>
  <div class="sidebar">
    <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
    <nav class="nav flex-column">
      <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-layer-group"></i> Danh mục tour</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
      <a class="nav-link active" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
       <a class="nav-link active" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
    </nav>
  </div>

  <main class="main">
    <h3><?= $title ?></h3>
    <div class="card p-4">
      <form method="post" action="<?= $editing ? BASE_URL . '?r=staff_update&id='.$item['id'] : BASE_URL . '?r=staff_store' ?>">
        <div class="mb-3">
          <label class="form-label">Họ tên</label>
          <input class="form-control" type="text" name="full_name" required value="<?= $editing ? htmlspecialchars($item['full_name']) : '' ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input class="form-control" type="email" name="email" value="<?= $editing ? htmlspecialchars($item['email']) : '' ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input class="form-control" type="text" name="phone" value="<?= $editing ? htmlspecialchars($item['phone']) : '' ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Vai trò</label>
          <input class="form-control" type="text" name="role" value="<?= $editing ? htmlspecialchars($item['role']) : '' ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Trạng thái</label>
          <select class="form-select" name="is_active">
            <option value="1" <?= $editing && $item['is_active'] ? 'selected' : '' ?>>Hoạt động</option>
            <option value="0" <?= $editing && !$item['is_active'] ? 'selected' : '' ?>>Vô hiệu</option>
          </select>
        </div>
        <button class="btn btn-primary" type="submit">Lưu</button>
        <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=staff">Hủy</a>
      </form>
    </div>
  </main>
</body>
</html>