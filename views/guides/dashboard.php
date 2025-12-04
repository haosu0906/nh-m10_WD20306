<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>HDV - Lịch làm việc</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <?php $current_page='guides'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
  <main class="main" style="margin-left:200px;padding:86px 22px 22px">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Lịch tour của tôi</h3>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="<?= BASE_URL ?>?r=tour_logs&guide_id=<?= (int)($_SESSION['guide_user_id'] ?? 0) ?>">
          Nhật ký tour của tôi
        </a>
        <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=guide_logout">Đăng xuất</a>
      </div>
    </div>

    <?php if (empty($schedules)): ?>
      <div class="alert alert-info">Hiện tại bạn chưa được phân công tour nào.</div>
    <?php else: ?>
      <div class="card">
        <div class="card-body p-0">
          <table class="table mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Tour</th>
                <th>Ngày đi</th>
                <th>Ngày về</th>
                <th>Ghi chú</th>
                <th>Xem lịch trình</th>
                <th>Nhật ký tour</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($schedules as $row): ?>
                <tr>
                  <td><?= htmlspecialchars((string)($row['tour_title'] ?? ('#'.$row['tour_id'])), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars((string)$row['start_date'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars((string)$row['end_date'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td>-</td>
                  <td>
                    <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>?r=tours_itinerary&id=<?= (int)$row['tour_id'] ?>">Lịch trình tour</a>
                  </td>
                  <td>
                    <a class="btn btn-sm btn-outline-success" href="<?= BASE_URL ?>?r=tour_logs&tour_id=<?= (int)$row['tour_id'] ?>&guide_id=<?= (int)($_SESSION['guide_user_id'] ?? 0) ?>">Nhật ký</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
