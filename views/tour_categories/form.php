<?php
$editing = isset($item) && !empty($item);
$title = $editing ? 'Sửa tour' : 'Thêm tour';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title><?= $title ?></title>
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
      <a class="nav-link" href="/base/?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
      <a class="nav-link active" href="/base/?r=tour_categories"><i class="fas fa-map"></i> Tours</a>
      <a class="nav-link" href="/base/?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
    </nav>
  </div>

  <main class="main">
    <h3><?= $title ?></h3>
    <div class="card p-4">
      <form method="post" action="<?= $editing ? '/base/?r=tour_categories_update&id='.$item['id'] : '/base/?r=tour_categories_store' ?>">
        <div class="mb-3">
          <label class="form-label">Tên tour</label>
          <input class="form-control" type="text" name="title" required value="<?= $editing ? htmlspecialchars($item['title']) : '' ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Mô tả</label>
          <textarea class="form-control" name="description" rows="4"><?= $editing ? htmlspecialchars($item['description']) : '' ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Loại tour</label>
          <input class="form-control" type="text" name="tour_type" value="<?= $editing ? htmlspecialchars($item['tour_type'] ?? '') : '' ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Giá</label>
          <input class="form-control" type="number" name="price" value="<?= $editing ? htmlspecialchars($item['price'] ?? '') : '' ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Trạng thái</label>
          <input class="form-control" type="text" name="status" value="<?= $editing ? htmlspecialchars($item['status'] ?? '') : '' ?>">
        </div>
        <button class="btn btn-primary" type="submit">Lưu</button>
        <a class="btn btn-secondary" href="/base/?r=tour_categories">Hủy</a>
      </form>
    </div>
  </main>
</body>
</html>