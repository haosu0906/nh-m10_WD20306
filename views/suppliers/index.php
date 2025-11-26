<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Nhà cung cấp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    :root{--accent:#667eea;--accent-dark:#5568d3}
    .sidebar{position:fixed;left:0;top:0;bottom:0;width:200px;padding:20px;background:linear-gradient(180deg,var(--accent),#764ba2);color:#fff;overflow:auto}
    .sidebar h3{font-weight:700;margin-bottom:1rem;text-align:center;font-size:16px}
    .nav-link{color:rgba(255,255,255,.95);display:flex;align-items:center;gap:.6rem;padding:.6rem;border-radius:.5rem;text-decoration:none}
    .nav-link:hover,.nav-link.active{background:rgba(255,255,255,.1)}
    .main{margin-left:200px;padding:22px}
  </style>
</head>
<body>
  <div class="sidebar">
    <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
    <nav class="nav flex-column">
      <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-layer-group"></i> Danh mục tour</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
      <a class="nav-link active" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guide_dashboard"><i class="fas fa-door-open"></i> Portal HDV</a>
    </nav>
  </div>

  <main class="main">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3>Nhà cung cấp dịch vụ</h3>
        <p class="text-muted mb-1">Xem danh sách đối tác (khách sạn, nhà hàng, vận chuyển...)</p>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <table class="table table-striped mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>Tên NCC</th>
              <th>Người liên hệ</th>
              <th>Loại dịch vụ</th>
              <th>Điện thoại</th>
              <th>Xem chi tiết</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!empty($suppliers)): foreach ($suppliers as $s): ?>
            <tr>
              <td><?= htmlspecialchars((string)$s['name'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars((string)$s['contact_person'], ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <?php
                  $map = [
                    'hotel'       => 'Khách sạn',
                    'restaurant'  => 'Nhà hàng',
                    'transport'   => 'Vận chuyển',
                    'ticket'      => 'Vé tham quan',
                    'insurance'   => 'Bảo hiểm',
                    'guide'       => 'Hướng dẫn viên',
                    'meal'        => 'Ăn uống',
                    'entertain'   => 'Giải trí',
                    'other'       => 'Dịch vụ khác',
                  ];
                  $st = $s['service_type'] ?? '';
                ?>
                <span class="badge bg-primary">
                  <?= htmlspecialchars((string)($map[$st] ?? $st), ENT_QUOTES, 'UTF-8') ?>
                </span>
              </td>
              <td><?= htmlspecialchars((string)$s['phone'], ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>?r=suppliers_show&id=<?= (int)$s['id'] ?>">Xem dịch vụ</a>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có nhà cung cấp</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
