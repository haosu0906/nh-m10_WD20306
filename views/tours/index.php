<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Quản lý Tours</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    :root{--accent:#667eea;--accent-dark:#5568d3}
    .sidebar{position:fixed;left:0;top:0;bottom:0;width:200px;padding:20px;background:linear-gradient(180deg,var(--accent),#764ba2);color:#fff;overflow:auto}
    .sidebar h3{font-weight:700;margin-bottom:1rem;text-align:center;font-size:16px}
    .nav-link{color:rgba(255,255,255,.95);display:flex;align-items:center;gap:.6rem;padding:.6rem;border-radius:.5rem;text-decoration:none}
    .nav-link:hover, .nav-link.active{background:rgba(255,255,255,.1)}
    .main{margin-left:200px;padding:22px}
    .tour-cover{width:80px;height:60px;object-fit:cover;border-radius:6px}
  </style>
</head>
<body>
  <div class="sidebar">
    <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
    <nav class="nav flex-column">
      <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
      <a class="nav-link active" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
    </nav>
  </div>

  <main class="main">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3>Quản lý tour</h3>
        <p class="text-muted mb-0">Thông tin cơ bản, lịch trình, giá và hình ảnh</p>
      </div>
      <a class="btn btn-success" href="<?= BASE_URL ?>?r=tours_create"><i class="fas fa-plus"></i> Thêm tour</a>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <table class="table table-striped mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>Tour</th>
              <th>Danh mục</th>
              <th>Loại</th>
              <th>Nhà cung cấp</th>
              <th>Trạng thái</th>
              <th>Giá người lớn</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($tours)): foreach($tours as $tour): ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <?php
                      $cover = $tour['cover_image'] ?? ($tour['image'] ?? '');
                      $isExternal = preg_match('/^https?:\/\//i', (string)$cover);
                    ?>
                    <?php if(!empty($cover)): ?>
                      <img src="<?= $isExternal ? $cover : (BASE_ASSETS_UPLOADS . $cover) ?>" class="tour-cover" alt="<?= htmlspecialchars($tour['title']) ?>">
                    <?php else: ?>
                      <div class="tour-cover bg-light d-flex justify-content-center align-items-center text-muted">N/A</div>
                    <?php endif; ?>
                    <div>
                      <div class="fw-semibold"><?= htmlspecialchars($tour['title']) ?></div>
                      <small class="text-muted"><?= htmlspecialchars(mb_substr($tour['description'] ?? '', 0, 60)) ?>...</small>
                    </div>
                  </div>
                </td>
                <td><?= htmlspecialchars($tour['category_name'] ?? 'Chưa phân loại') ?></td>
                <td><span class="badge bg-primary"><?= htmlspecialchars($types[$tour['tour_type']] ?? $tour['tour_type']) ?></span></td>
                <td><?= htmlspecialchars($tour['supplier_name'] ?? '---') ?></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($statuses[$tour['status']] ?? $tour['status']) ?></span></td>
                <?php $adult = $priceByTour[$tour['id']]['adult_price'] ?? ($tour['adult_price_join'] ?? ($tour['price'] ?? 0)); ?>
                <td><?= number_format((float)$adult, 0, ',', '.') ?> VND</td>
                <td>
                  <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>?r=tours_itinerary&id=<?= $tour['id'] ?>">Lịch trình</a>
                  <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>?r=schedules&tour_id=<?= $tour['id'] ?>">Lịch khởi hành</a>
                  <a class="btn btn-sm btn-primary" href="<?= BASE_URL ?>?r=tours_edit&id=<?= $tour['id'] ?>">Sửa</a>
                  <a class="btn btn-sm btn-danger" href="<?= BASE_URL ?>?r=tours_delete&id=<?= $tour['id'] ?>" onclick="return confirm('Xác nhận xóa tour này?')">Xóa</a>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có tour nào</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>

