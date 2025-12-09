<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Portal HDV</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
</head>
<body>
<?php $current_page='guide_login'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
<div class="main-content">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Portal HDV</h3>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">Các phân công gần đây</div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped">
          <thead class="table-light">
            <tr>
              <th>Tour</th>
              <th>Ngày bắt đầu</th>
              <th>Ngày kết thúc</th>
              <th>Vai trò</th>
              <th>Ghi chú</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (($assignments ?? []) as $a): ?>
              <tr>
                <td><?= htmlspecialchars($a['tour_title'] ?? '') ?></td>
                <td><?= !empty($a['start_date']) ? date('d/m/Y', strtotime($a['start_date'])) : '' ?></td>
                <td><?= !empty($a['end_date']) ? date('d/m/Y', strtotime($a['end_date'])) : '' ?></td>
                <td><?= htmlspecialchars($a['assignment_type'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['notes'] ?? '') ?></td>
                <td class="d-flex gap-2">
                  <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>?r=guide_portal_customers&tour_id=<?= (int)($a['tour_id'] ?? 0) ?>&start=<?= urlencode($a['start_date'] ?? '') ?>&end=<?= urlencode($a['end_date'] ?? '') ?>">Danh sách khách</a>
                  <?php if (!empty($a['schedule_id'])): ?>
                  <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>?r=tour_manifest&departure_id=<?= (int)$a['schedule_id'] ?>">Danh sách đoàn</a>
                  <?php endif; ?>
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
