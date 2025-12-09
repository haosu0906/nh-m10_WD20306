<?php
require_once __DIR__ . '/../../assets/configs/env.php';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Danh sách Thanh toán</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
  <style>
    .table{ font-size: 13.5px }
    .status-badge{ font-size:.85rem; padding:.35rem .6rem }
  </style>
</head>
<body>
  <?php $current_page='payments'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0"><i class="fas fa-credit-card me-2"></i>Danh sách Thanh toán</h3>
      <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>?r=payments_create" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Thêm thanh toán</a>
        <a href="<?= BASE_URL ?>?r=booking" class="btn btn-outline-secondary"><i class="fas fa-book me-2"></i>Booking</a>
      </div>
    </div>

    <?php if (!empty($_SESSION['payment_message'])): ?>
      <div class="alert alert-<?= ($_SESSION['payment_status'] ?? '') === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['payment_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['payment_message'], $_SESSION['payment_status']); endif; ?>

    <div class="card mb-3">
      <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
          <input type="hidden" name="r" value="payments">
          <div class="col-md-4">
            <label class="form-label">Booking</label>
            <select name="booking_id" class="form-select">
              <option value="">-- Tất cả --</option>
              <?php foreach (($bookings ?? []) as $b): ?>
              <option value="<?= (int)$b['id'] ?>" <?= (string)($_GET['booking_id'] ?? '')===(string)$b['id']?'selected':'' ?>>
                BK<?= (int)$b['id'] ?> - <?= htmlspecialchars($b['tour_title'] ?? '') ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Người tạo (ID)</label>
            <input type="number" class="form-control" name="created_by" value="<?= htmlspecialchars($_GET['created_by'] ?? '') ?>" placeholder="Nhập ID người tạo">
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-filter me-2"></i>Lọc</button>
            <a href="<?= BASE_URL ?>?r=payments" class="btn btn-outline-secondary">Xóa lọc</a>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Booking</th>
                <th>Tour</th>
                <th>Ngày thanh toán</th>
                <th>Số tiền</th>
                <th>Phương thức</th>
                <th>Mã giao dịch</th>
                <th>Trạng thái</th>
                <th>Người tạo</th>
                <th class="text-center">Hành động</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($payments)): foreach ($payments as $p): ?>
              <tr>
                <td>#<?= (int)$p['id'] ?></td>
                <td>
                  <a href="<?= BASE_URL ?>?r=booking_payment_history&booking_id=<?= (int)$p['booking_id'] ?>" class="text-decoration-none">
                    BK<?= (int)$p['booking_id'] ?>
                  </a>
                </td>
                <td><?= htmlspecialchars($p['tour_title'] ?? '') ?></td>
                <td><?= !empty($p['payment_date']) ? date('d/m/Y H:i', strtotime($p['payment_date'])) : '---' ?></td>
                <td class="fw-semibold text-primary"><?= number_format((float)($p['amount'] ?? 0), 0, ',', '.') ?> VNĐ</td>
                <td>
                  <?php $methods=['cash'=>'Tiền mặt','bank_transfer'=>'Chuyển khoản','credit_card'=>'Thẻ tín dụng','momo'=>'MoMo','zalopay'=>'ZaloPay']; ?>
                  <?= $methods[$p['payment_method']] ?? ($p['payment_method'] ?? '---') ?>
                </td>
                <td><?= htmlspecialchars($p['transaction_id'] ?? '') ?: '---' ?></td>
                <td>
                  <?php $colors=['pending'=>'warning','completed'=>'success','failed'=>'danger','refunded'=>'secondary']; $labels=['pending'=>'Chờ xử lý','completed'=>'Hoàn tất','failed'=>'Thất bại','refunded'=>'Đã hoàn']; ?>
                  <span class="badge bg-<?= $colors[$p['status']] ?? 'secondary' ?> status-badge"><?= $labels[$p['status']] ?? htmlspecialchars($p['status'] ?? '---') ?></span>
                </td>
                <td><?= htmlspecialchars($p['created_by_name'] ?? '') ?: '---' ?></td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= BASE_URL ?>?r=payments_edit&id=<?= (int)$p['id'] ?>" class="btn btn-outline-primary" title="Sửa"><i class="fas fa-edit"></i></a>
                    <a href="<?= BASE_URL ?>?r=payments_delete&id=<?= (int)$p['id'] ?>&csrf_token=<?= urlencode(csrf_token()) ?>" class="btn btn-outline-danger" onclick="return confirm('Xác nhận xóa giao dịch này?')" title="Xóa"><i class="fas fa-trash"></i></a>
                  </div>
                </td>
              </tr>
              <?php endforeach; else: ?>
              <tr><td colspan="10" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
