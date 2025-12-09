<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Nhật ký tour</title>
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
    <h3 class="mb-0">Nhật ký tour</h3>
    <div class="d-flex gap-2">
      <a class="btn btn-primary" href="<?= BASE_URL ?>?r=tour_logs_create">Thêm nhật ký</a>
      <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=tour_logs_export&type=csv&tour_id=<?= urlencode($_GET['tour_id'] ?? '') ?>&guide_id=<?= urlencode($_GET['guide_id'] ?? '') ?>&from=<?= urlencode($_GET['from'] ?? '') ?>&to=<?= urlencode($_GET['to'] ?? '') ?>">Xuất CSV</a>
      <a class="btn btn-outline-secondary" target="_blank" href="<?= BASE_URL ?>?r=tour_logs_export&type=print&tour_id=<?= urlencode($_GET['tour_id'] ?? '') ?>&guide_id=<?= urlencode($_GET['guide_id'] ?? '') ?>&from=<?= urlencode($_GET['from'] ?? '') ?>&to=<?= urlencode($_GET['to'] ?? '') ?>">In</a>
    </div>
  </div>
  <div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">Danh sách nhật ký</div>
    <div class="card-body">
      <form class="row g-3 mb-3" method="get" action="<?= BASE_URL ?>">
        <input type="hidden" name="r" value="tour_logs" />
        <div class="col-md-3">
          <label class="form-label">Tour</label>
          <select name="tour_id" class="form-select">
            <option value="">-- Tất cả --</option>
            <?php foreach(($tours ?? []) as $t): ?>
              <option value="<?= (int)$t['id'] ?>" <?= (!empty($_GET['tour_id']) && (int)$_GET['tour_id']==(int)$t['id'])?'selected':'' ?>><?= htmlspecialchars($t['title'] ?? ('#'.$t['id'])) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">HDV</label>
          <select name="guide_id" class="form-select">
            <option value="">-- Tất cả --</option>
            <?php foreach(($guides ?? []) as $g): ?>
              <option value="<?= (int)$g['id'] ?>" <?= (!empty($_GET['guide_id']) && (int)$_GET['guide_id']==(int)$g['id'])?'selected':'' ?>><?= htmlspecialchars($g['full_name'] ?? ('#'.$g['id'])) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Từ ngày</label>
          <input type="datetime-local" name="from" class="form-control" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>" />
        </div>
        <div class="col-md-3">
          <label class="form-label">Đến ngày</label>
          <input type="datetime-local" name="to" class="form-control" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>" />
        </div>
        <div class="col-12 d-flex justify-content-end">
          <button class="btn btn-outline-primary">Lọc</button>
        </div>
      </form>
      <div class="table-responsive">
        <table class="table table-sm table-striped">
          <thead class="table-light">
            <tr>
              <th>Tour</th>
              <th>HDV</th>
              <th>Thời điểm</th>
              <th>Sự kiện</th>
              <th>Phản hồi KH</th>
              <th>Thời tiết</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach(($logs ?? []) as $log): ?>
              <tr>
                <td><?= htmlspecialchars($log['tour_title'] ?? '') ?></td>
                <td><?= htmlspecialchars($log['guide_name'] ?? '') ?></td>
                <td><?= !empty($log['log_date']) ? date('d/m/Y H:i', strtotime($log['log_date'])) : '' ?></td>
                <td><?= htmlspecialchars($log['incident_details'] ?? '') ?></td>
                <td><?= htmlspecialchars($log['customer_feedback'] ?? '') ?></td>
                <td><?= htmlspecialchars($log['weather'] ?? '') ?></td>
                <td>
                  <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>?r=tour_logs_edit&id=<?= (int)$log['id'] ?>">Sửa</a>
                  <a class="btn btn-sm btn-outline-danger" href="<?= BASE_URL ?>?r=tour_logs_delete&id=<?= (int)$log['id'] ?>" onclick="return confirm('Xóa nhật ký này?')">Xóa</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
