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
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
</head>
<body>
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <?php $current_page='tour_categories'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>

  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0"><?= $title ?></h3>
      <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=tour_categories">Quay lại</a>
    </div>
    <div class="card shadow-sm">
      <div class="card-header bg-light fw-bold">Thông tin danh mục</div>
      <div class="card-body">
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
          <label class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
          <?php
            $nameValue = $old['name'] ?? ($item['name'] ?? '');
          ?>
          <input class="form-control form-control-lg" type="text" name="name" required value="<?= htmlspecialchars($nameValue) ?>">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Loại tour <span class="text-danger">*</span></label>
          <?php $selectedType = $old['category_type'] ?? ($item['category_type'] ?? 'domestic'); ?>
          <select class="form-select form-select-lg" name="category_type" required>
            <?php foreach ($types as $key => $label): ?>
              <option value="<?= $key ?>" <?= $selectedType === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Mô tả</label>
          <?php
            $descValue = $old['description'] ?? ($item['description'] ?? '');
          ?>
          <textarea class="form-control form-control-lg" name="description" rows="4"><?= htmlspecialchars($descValue) ?></textarea>
        </div>
        <div class="text-end">
          <button class="btn btn-primary btn-lg" type="submit">Lưu</button>
          <a class="btn btn-outline-secondary btn-lg" href="<?= BASE_URL ?>?r=tour_categories">Hủy</a>
        </div>
      </form>
      </div>
    </div>
  </div>
</body>
</html>
