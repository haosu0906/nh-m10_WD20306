<?php
$editing = isset($item) && !empty($item);
$title = $editing ? 'Sửa danh mục tour' : 'Thêm danh mục tour';
$formAction = $editing ? BASE_URL . '?r=tour_categories_update&id='.$item['id'] : BASE_URL . '?r=tour_categories_store';
$errors = $errors ?? [];
$old = $old ?? [];
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
      <a class="nav-link active" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=payments"><i class="fas fa-credit-card"></i> Thanh toán</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=cancellation_policies"><i class="fas fa-ban"></i> Chính sách hủy</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guide_schedules"><i class="fas fa-calendar-alt"></i> Lịch HDV</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guide_ratings"><i class="fas fa-star"></i> Đánh giá HDV</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guide_login">
        <i class="fas fa-door-open"></i> Portal HDV
      </a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=admin_login">
        <i class="fas fa-user-shield"></i> Đăng nhập Admin
      </a>
    </nav>
  </div>

  <main class="main">
    <h3><?= $title ?></h3>
    <div class="card p-4">
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $message): ?>
              <li><?= htmlspecialchars($message) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <form method="post" action="<?= $formAction ?>">
        <div class="mb-3">
          <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
          <?php
            $nameValue = $old['name'] ?? ($item['name'] ?? '');
          ?>
          <input class="form-control" type="text" name="name" required value="<?= htmlspecialchars($nameValue) ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Loại tour <span class="text-danger">*</span></label>
          <?php $selectedType = $old['category_type'] ?? ($item['category_type'] ?? 'domestic'); ?>
          <select class="form-select" name="category_type" required>
            <?php foreach ($types as $key => $label): ?>
              <option value="<?= $key ?>" <?= $selectedType === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Mô tả</label>
          <?php
            $descValue = $old['description'] ?? ($item['description'] ?? '');
          ?>
          <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($descValue) ?></textarea>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-primary" type="submit">Lưu</button>
          <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=tour_categories">Hủy</a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>