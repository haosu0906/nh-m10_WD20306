<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>HDV - Lịch làm việc</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Lịch tour của tôi</h3>
      <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=guide_logout">Đăng xuất</a>
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
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
