<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Quản trị — Hệ thống Tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    :root{--accent:#667eea;--accent-dark:#5568d3}
    *{box-sizing:border-box}
    body{font-family:Inter,Segoe UI,Arial;background:#f5f7fb;margin:0;color:#222}
    .sidebar{position:fixed;left:0;top:0;bottom:0;width:200px;padding:20px;background:linear-gradient(180deg,var(--accent),#764ba2);color:#fff;overflow:auto}
    .sidebar h3{font-weight:700;margin-bottom:1rem;text-align:center;font-size:16px}
    .nav-link{color:rgba(255,255,255,.95);display:flex;align-items:center;gap:.6rem;padding:.6rem;border-radius:.5rem;text-decoration:none}
    .nav-link:hover, .nav-link.active{background:rgba(255,255,255,.1);color:#fff}
    .main{margin-left:200px;padding:22px}
    .card{box-shadow:0 6px 18px rgba(20,20,30,.06)}
    @media (max-width:900px){.sidebar{position:relative;width:100%}.main{margin-left:0}}
  </style>
</head>
<body>
  <div class="sidebar">
    <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
    <nav class="nav flex-column">
      <a class="nav-link" href="/base/?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
      <a class="nav-link" href="/base/?r=tour_categories"><i class="fas fa-map"></i> Tours</a>
      <a class="nav-link" href="/base/?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
      <a class="nav-link" href="/base/?r=schedules"><i class="fas fa-calendar-day"></i> Lịch Khởi Hành</a>
      <a class="nav-link" href="/base/?r=bookings"><i class="fas fa-calendar-check"></i> Đặt Tour</a>
    </nav>
  </div>

  <main class="main">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4>Bảng Quản Trị — Hệ thống Tour</h4>
      <small class="text-muted">Chọn từ menu bên trái</small>
    </div>

    <div class="card p-4">
      <h5>Chào mừng!</h5>
      <p>Hệ thống quản lý tour - Tripmate</p>
      <div class="row mt-3">
        <div class="col-md-4">
          <div class="card bg-light">
            <div class="card-body">
              <h6><i class="fas fa-map"></i> Tours</h6>
              <p class="small">Quản lý danh mục tour</p>
              <a href="/base/?r=tour_categories" class="btn btn-sm btn-primary">Vào</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-light">
            <div class="card-body">
              <h6><i class="fas fa-users"></i> Nhân Sự</h6>
              <p class="small">Quản lý nhân sự</p>
              <a href="/base/?r=staff" class="btn btn-sm btn-primary">Vào</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>
</html>