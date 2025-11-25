<?php
$errors = flash('errors') ?? [];
$old = flash('old') ?? [];
$editing = isset($schedule) && !empty($schedule);
$title = $editing ? 'Sửa lịch tour' : 'Thêm lịch tour';
$formAction = $editing ? BASE_URL . '?r=schedules_update&id=' . (int)$schedule['id'] : BASE_URL . '?r=schedules_store';
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
      <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
      <a class="nav-link active" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
    </nav>
  </div>

  <main class="main">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0"><?= $title ?></h3>
      <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=schedules">Quay lại</a>
    </div>

    <?php if(!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $message): ?>
            <li><?= htmlspecialchars($message) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= $formAction ?>" class="card p-4">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Tour <span class="text-danger">*</span></label>
          <select name="tour_id" class="form-select" required>
            <option value="">-- Chọn tour --</option>
            <?php foreach ($tours as $t): ?>
              <?php $selected = ($old['tour_id'] ?? ($schedule['tour_id'] ?? '')) == $t['id'] ? 'selected' : ''; ?>
              <option value="<?= $t['id'] ?>" <?= $selected ?>><?= htmlspecialchars($t['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Ngày đi <span class="text-danger">*</span></label>
          <input type="date" name="start_date" class="form-control" required value="<?= htmlspecialchars($old['start_date'] ?? ($schedule['start_date'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label">Ngày về <span class="text-danger">*</span></label>
          <input type="date" name="end_date" class="form-control" required value="<?= htmlspecialchars($old['end_date'] ?? ($schedule['end_date'] ?? '')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Hướng dẫn viên</label>
          <select name="guide_user_id" class="form-select">
            <option value="">-- Chọn HDV --</option>
            <?php foreach ($guides as $g): ?>
              <?php $selGuide = ($old['guide_user_id'] ?? ($schedule['guide_user_id'] ?? '')) == $g['id'] ? 'selected' : ''; ?>
              <option value="<?= $g['id'] ?>" <?= $selGuide ?>><?= htmlspecialchars($g['full_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tài xế</label>
          <input type="number" name="driver_user_id" class="form-control" placeholder="ID tài xế" value="<?= htmlspecialchars($old['driver_user_id'] ?? ($schedule['driver_user_id'] ?? '')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Sức chứa tối đa</label>
          <input type="number" name="max_capacity" class="form-control" value="<?= htmlspecialchars($old['max_capacity'] ?? ($schedule['max_capacity'] ?? 20)) ?>">
        </div>
      </div>
      <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary">Lưu lịch</button>
      </div>
    </form>
  </main>
</body>
</html>
