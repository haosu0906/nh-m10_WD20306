<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>HDV - Lịch làm việc</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
</head>
<body>
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <?php require_once __DIR__ . '/../../assets/configs/db.php'; ?>
  <?php $current_page='guides'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
  <div class="main-content">
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
                <th>Trạng thái</th>
                <th>Danh sách đoàn</th>
                <th>Quét QR</th>
                <th>Lịch trình</th>
                <th>Nhật ký</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($schedules as $row): ?>
                <?php
                  $pdo = DB::get();
                  $sid = (int)$row['id'];
                  $stats = ['total'=>0,'arrived'=>0,'pending'=>0,'noshow'=>0];
                  try {
                    $st = $pdo->prepare("SELECT COUNT(bg.id) AS total,
                      SUM(CASE WHEN bg.is_checked_in=1 THEN 1 ELSE 0 END) AS arrived,
                      SUM(CASE WHEN bg.is_no_show=1 THEN 1 ELSE 0 END) AS noshow
                      FROM booking_guests bg JOIN bookings b ON bg.booking_id=b.id
                      WHERE b.schedule_id = :sid AND b.booking_status IN ('deposit','completed')");
                    $st->execute([':sid'=>$sid]);
                    $r = $st->fetch(PDO::FETCH_ASSOC);
                    $total = (int)($r['total'] ?? 0); $arr = (int)($r['arrived'] ?? 0); $no = (int)($r['noshow'] ?? 0);
                    $stats['total'] = $total; $stats['arrived'] = $arr; $stats['noshow'] = $no; $stats['pending'] = max(0, $total - $arr - $no);
                  } catch (Throwable $e) {}
                  $locked = false;
                  try {
                    $timeStr = '08:00:00';
                    if (!empty($row['tour_id'])) {
                      $qT = $pdo->query("SHOW TABLES LIKE 'tour_itinerary_items'");
                      if ($qT && $qT->rowCount() > 0) {
                        $qStart = $pdo->query("SHOW COLUMNS FROM `tour_itinerary_items` LIKE 'start_time'");
                        $qAct = $pdo->query("SHOW COLUMNS FROM `tour_itinerary_items` LIKE 'activity_time'");
                        if ($qStart && $qStart->rowCount() > 0) {
                          $stMin = $pdo->prepare("SELECT MIN(start_time) AS t FROM tour_itinerary_items WHERE tour_id = ? AND day_number = 1 AND start_time IS NOT NULL");
                          $stMin->execute([(int)$row['tour_id']]);
                          $rowMin = $stMin->fetch(PDO::FETCH_ASSOC);
                          if (!empty($rowMin['t'])) { $timeStr = $rowMin['t']; }
                        } elseif ($qAct && $qAct->rowCount() > 0) {
                          $stMin2 = $pdo->prepare("SELECT MIN(activity_time) AS t FROM tour_itinerary_items WHERE tour_id = ? AND day_number = 1 AND activity_time IS NOT NULL");
                          $stMin2->execute([(int)$row['tour_id']]);
                          $rowMin2 = $stMin2->fetch(PDO::FETCH_ASSOC);
                          if (!empty($rowMin2['t'])) { $timeStr = $rowMin2['t']; }
                        }
                      }
                    }
                    if (!empty($row['start_date'])) {
                      $cutoff = strtotime($row['start_date'] . ' ' . $timeStr);
                      if ($cutoff && time() >= $cutoff) { $locked = true; }
                    }
                  } catch (Throwable $e) {}
                ?>
                <tr>
                  <td><?= htmlspecialchars((string)($row['tour_title'] ?? ('#'.$row['tour_id'])), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars((string)$row['start_date'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars((string)$row['end_date'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <span class="badge bg-info">Tổng: <?= (int)$stats['total'] ?></span>
                    <span class="badge bg-success">Đến: <?= (int)$stats['arrived'] ?></span>
                    <span class="badge bg-secondary">Chờ: <?= (int)$stats['pending'] ?></span>
                    <span class="badge bg-danger">Vắng: <?= (int)$stats['noshow'] ?></span>
                  </td>
                  <td>
                    <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>?r=tour_manifest&departure_id=<?= (int)$row['id'] ?>">Danh sách đoàn</a>
                  </td>
                  <td>
                    <a class="btn btn-sm btn-outline-primary <?= $locked ? 'disabled' : '' ?>" href="<?= BASE_URL ?>?r=qr_scan&departure_id=<?= (int)$row['id'] ?>">Quét QR</a>
                  </td>
                  <td>
                    <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>?r=tours_itinerary&id=<?= (int)$row['tour_id'] ?>">Lịch trình</a>
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
  </div>
</body>
</html>
