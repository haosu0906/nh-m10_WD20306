<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Quản lý Lịch tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    :root{--accent:#667eea;--accent-dark:#5568d3}
    .sidebar{position:fixed;left:0;top:0;bottom:0;width:200px;padding:20px;background:linear-gradient(180deg,var(--accent),#764ba2);color:#fff;overflow:auto}
    .sidebar h3{font-weight:700;margin-bottom:1rem;text-align:center;font-size:16px}
    .nav-link{color:rgba(255,255,255,.95);display:flex;align-items:center;gap:.6rem;padding:.6rem;border-radius:.5rem;text-decoration:none}
    .nav-link:hover, .nav-link.active{background:rgba(255,255,255,.1)}
    .main{margin-left:200px;padding:22px}
  </style>
</head>
<body>
  <div class="sidebar">
    <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
    <nav class="nav flex-column">
      <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
      <a class="nav-link active" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch Tour</a>
      <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
    </nav>
  </div>

  <main class="main">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3>Quản lý lịch tour</h3>
        <p class="text-muted mb-0">Tạo lịch khởi hành, gán HDV và sức chứa</p>
      </div>
      <a class="btn btn-success" href="<?= BASE_URL ?>?r=schedules_create"><i class="fas fa-plus"></i> Thêm lịch</a>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <table class="table table-striped mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>Tour</th>
              <th>Ngày đi</th>
              <th>Ngày về</th>
              <th>HDV</th>
              <th>Sức chứa</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($schedules)): foreach($schedules as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['tour_title'] ?? ('#'.$row['tour_id'])) ?></td>
                <td><?= htmlspecialchars($row['start_date']) ?></td>
                <td><?= htmlspecialchars($row['end_date']) ?></td>
                <td><?= htmlspecialchars($row['guide_name'] ?? '---') ?></td>
                <td><?= (int)($row['max_capacity'] ?? 0) ?></td>
                <td>
                  <a class="btn btn-sm btn-primary" href="<?= BASE_URL ?>?r=schedules_edit&id=<?= (int)$row['id'] ?>">Sửa</a>
                  <a class="btn btn-sm btn-danger" href="<?= BASE_URL ?>?r=schedules_delete&id=<?= (int)$row['id'] }" onclick="return confirm('Xóa lịch này?')">Xóa</a>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có lịch</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
