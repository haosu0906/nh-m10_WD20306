<?php
$editing = isset($guide) && !empty($guide);
$title = $editing ? 'Cập nhật HDV' : 'Thêm HDV mới';
$formAction = $editing ? BASE_URL . '?r=guides_update&id=' . $guide['id'] : BASE_URL . '?r=guides_store';
$errors = $errors ?? [];
$old = $old ?? [];

$value = function($key, $default = '') use ($old, $guide) {
    if (isset($old[$key])) {
        return htmlspecialchars($old[$key]);
    }
    if (isset($guide[$key])) {
        return htmlspecialchars($guide[$key]);
    }
    return htmlspecialchars($default);
};
$selectedType = $old['guide_type'] ?? ($guide['guide_type'] ?? 'domestic');
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
    .avatar-preview{width:100px;height:100px;border-radius:50%;object-fit:cover}
  </style>
</head>
<body>
  <div class="sidebar">
    <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
    <nav class="nav flex-column">
      <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
      <a class="nav-link active" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
    </nav>
  </div>

  <main class="main">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0"><?= $title ?></h3>
      <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=guides">Quay lại</a>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $message): ?>
            <li><?= htmlspecialchars($message) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= $formAction ?>" enctype="multipart/form-data" class="card p-4">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Họ tên <span class="text-danger">*</span></label>
          <input type="text" name="full_name" class="form-control" required value="<?= $value('full_name') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
          <input type="text" name="phone" class="form-control" required value="<?= $value('phone') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" required value="<?= $value('email') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">CMND/CCCD</label>
          <input type="text" name="identity_no" class="form-control" value="<?= $value('identity_no') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Số chứng chỉ</label>
          <input type="text" name="certificate_no" class="form-control" value="<?= $value('certificate_no') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Loại HDV</label>
          <select name="guide_type" class="form-select">
            <?php foreach ($types as $key => $label): ?>
              <option value="<?= $key ?>" <?= $selectedType === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Ảnh đại diện</label>
          <input type="file" name="avatar" class="form-control" accept="image/*">
          <?php if ($editing && !empty($guide['avatar'])): ?>
            <div class="mt-2">
              <img src="<?= BASE_ASSETS_UPLOADS . $guide['avatar'] ?>" class="avatar-preview" alt="avatar">
            </div>
          <?php endif; ?>
        </div>
        <div class="col-12">
          <label class="form-label">Ghi chú</label>
          <textarea name="notes" rows="4" class="form-control"><?= $value('notes') ?></textarea>
        </div>
      </div>
      <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary px-4">Lưu thông tin</button>
      </div>
    </form>
  </main>
</body>
</html>

