<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Chi tiết lợi nhuận tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
</head>
<body>
<?php $current_page='reports'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
<div class="main-content">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Chi tiết tour #<?= htmlspecialchars($_GET['tour_id'] ?? '') ?></h3>
    <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=reports_profit">Quay lại</a>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-light fw-bold">Danh sách booking</div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped">
          <thead class="table-light">
            <tr>
              <th>Booking</th>
              <th>Ngày đặt</th>
              <th class="text-end">Tổng giá</th>
              <th class="text-end">Đã thanh toán</th>
              <th>Trạng thái</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (($bookings ?? []) as $b): ?>
              <tr>
                <td>BK<?= (int)$b['id'] ?></td>
                <td><?= !empty($b['date_booked']) ? date('d/m/Y H:i', strtotime($b['date_booked'])) : '' ?></td>
                <td class="text-end"><?= number_format($b['total_price'] ?? 0,0,',','.') ?></td>
                <td class="text-end"><?= number_format($b['paid'] ?? 0,0,',','.') ?></td>
                <td><?= htmlspecialchars($b['booking_status'] ?? '') ?></td>
                <td><a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>?r=booking_detail&id=<?= (int)$b['id'] ?>">Xem</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">Chi phí liên quan</div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped">
          <thead class="table-light">
            <tr>
              <th>Loại chi phí</th>
              <th>Mô tả</th>
              <th>Nhà cung cấp</th>
              <th>Ngày</th>
              <th class="text-end">Số tiền</th>
            </tr>
          </thead>
          <tbody>
            <?php $totalCost=0; foreach (($expenses ?? []) as $e): ?>
              <?php $totalCost += (float)($e['amount'] ?? 0); ?>
              <tr>
                <td><?= htmlspecialchars($e['expense_type'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['description'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['supplier_name'] ?? '') ?></td>
                <td><?= !empty($e['date_incurred']) ? date('d/m/Y', strtotime($e['date_incurred'])) : '' ?></td>
                <td class="text-end"><?= number_format($e['amount'] ?? 0,0,',','.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr class="table-light">
              <th colspan="4">Tổng</th>
              <th class="text-end"><?= number_format($totalCost,0,',','.') ?></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
