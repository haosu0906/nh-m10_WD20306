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
    @media (max-width:900px){.sidebar{position:relative;width:100%}.main{margin-left:0}}
  </style>
</head>
<body>
  <div class="sidebar">
    <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
    <nav class="nav flex-column">
      <a class="nav-link" href="/base/?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
      <a class="nav-link active" href="/base/?r=tour_categories"><i class="fas fa-map"></i> Tours</a>
      <a class="nav-link" href="/base/?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
    </nav>
  </div>

  <main class="main">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Quản lý Tours</h3>
      <a class="btn btn-success" href="/base/?r=tour_categories_create">+ Thêm tour</a>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <table class="table table-striped mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Tên</th>
              <th>Mô tả</th>
              <th>Loại</th>
              <th>Giá</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($items)): foreach($items as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= htmlspecialchars(substr($row['description'] ?? '', 0, 50)) ?></td>
              <td><?= htmlspecialchars($row['tour_type'] ?? '') ?></td>
              <td><?= number_format($row['price'] ?? 0, 0, ',', '.') ?> VND</td>
              <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
               <td> của cột Hành động:
              <td>
                <a class="btn btn-sm btn-primary" href="/base/?r=tour_edit&id=<?= $row['id'] ?>">Sửa</a>
                  <a class="btn btn-sm btn-info" href="/base/?r=tour_itinerary_items&tour_id=<?= $row['id'] ?>">Lịch trình</a> 
                <a class="btn btn-sm btn-danger" href="/base/?r=tour_delete&id=<?= $row['id'] ?>" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
              </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Chưa có dữ liệu</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>