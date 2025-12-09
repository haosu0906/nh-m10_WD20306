<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title><?= isset($log)?'Sửa nhật ký':'Thêm nhật ký' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
  <style>
    header{left:260px!important;width:calc(100% - 260px)!important;}
  </style>
</head>
<body>
<?php $current_page='guides'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
<div class="main-content" style="margin-left:260px;margin-top:60px">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><?= isset($log)?'Sửa nhật ký':'Thêm nhật ký' ?></h3>
    <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=tour_logs">Quay lại</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">Thông tin</div>
    <div class="card-body">
      <form method="post" action="<?= BASE_URL ?>?r=<?= isset($log)?'tour_logs_update':'tour_logs_store' ?>">
        <?php if(isset($log)): ?><input type="hidden" name="id" value="<?= (int)$log['id'] ?>"><?php endif; ?>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Tour</label>
            <select name="tour_id" class="form-select" required>
              <?php foreach(($tours ?? []) as $t): ?>
                <option value="<?= (int)$t['id'] ?>" <?= isset($log)&& (int)$log['tour_id']===(int)$t['id']?'selected':'' ?>><?= htmlspecialchars($t['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Thời điểm</label>
            <input type="datetime-local" name="log_date" class="form-control" value="<?= isset($log)&&!empty($log['log_date'])?date('Y-m-d\TH:i', strtotime($log['log_date'])):'' ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Sự kiện/Sự cố</label>
            <textarea name="incident_details" class="form-control" rows="3"><?= htmlspecialchars($log['incident_details'] ?? '') ?></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Phản hồi khách hàng</label>
            <textarea name="customer_feedback" class="form-control" rows="3"><?= htmlspecialchars($log['customer_feedback'] ?? '') ?></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Thời tiết</label>
            <input type="text" name="weather" class="form-control" value="<?= htmlspecialchars($log['weather'] ?? '') ?>">
          </div>
        </div>
        <div class="text-end mt-3">
          <button class="btn btn-primary">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
