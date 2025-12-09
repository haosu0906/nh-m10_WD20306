<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Chi tiết Tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
  <style>
    .main-content{padding-bottom:40px}
    .section-card{border:0;box-shadow:0 4px 12px rgba(0,0,0,.06);border-radius:12px;margin-bottom:16px}
    .item-row{display:flex;gap:12px;align-items:flex-start;padding:8px 0;border-bottom:1px solid #eee}
    .item-row:last-child{border-bottom:none}
    .gallery img{height:110px;width:160px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb}
    .badge-soft{border-radius:10px;padding:4px 8px;font-size:.8rem}
    .hero-card{position:relative;min-height:300px;background-size:cover;background-position:center;border-radius:14px;overflow:hidden}
    .hero-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.35),rgba(0,0,0,.50))}
    .hero-content{position:relative;padding:32px;color:#fff}
    .nav-tabs .nav-link{color:#000 !important; opacity:1 !important}
    .nav-tabs .nav-link.active{color:#000 !important}
    .hero-content h2{font-size:2rem;font-weight:700}
    .hero-actions .btn{box-shadow:0 6px 14px rgba(0,0,0,.2)}
    .stat-card{border-start:4px solid transparent}
    .stat-primary{border-start-color:#0d6efd;background:#eef5ff}
    .stat-success{border-start-color:#198754;background:#eaf8f0}
    .stat-warning{border-start-color:#ffc107;background:#fff7e6}
    .stat-secondary{border-start-color:#6c757d;background:#f2f3f5}
    .trend-up{color:#198754}
    .trend-down{color:#dc3545}
    .trend-flat{color:#6c757d}
    .timeline{position:relative;padding-left:40px}
    .timeline:before{content:"";position:absolute;left:20px;top:0;bottom:0;border-left:2px dashed #cbd5e1}
    .timeline-item{position:relative;padding:12px 0}
    .timeline-item:before{display:none}
    .avatar-badge{width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#e2e8f0;font-weight:700;color:#334155}
    .gallery-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}
    .gallery-grid img{width:100%;height:120px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb}
    :root{--heroStart:#0b1b2d;--heroMid:#1a3c4d;--heroEnd:#244a61;--chipBg:#eef5ff;--chipTxt:#0f172a;--glow:#22c55e;--blue:#0ea5e9;--green:#10b981;--dark:#111827}
    .hero-card:before{content:"";position:absolute;inset:0;background:radial-gradient(1200px 400px at 10% 10%, rgba(255,255,255,.08), transparent),linear-gradient(90deg, rgba(16,185,129,.2), rgba(14,165,233,.15), rgba(16,185,129,.2));mix-blend-mode:soft-light;opacity:.6}
    .badge-glow{box-shadow:0 0 12px rgba(34,197,94,.55);background:#22c55e;color:#fff;border-radius:999px}
    .hero-chips .chip{background:var(--chipBg);color:var(--chipTxt);border-radius:999px;padding:4px 10px;font-weight:600;border:1px solid #dbeafe}
    .btn-hero-edit{background:#10b981;color:#fff;border:none}
    .btn-hero-clone{background:#0ea5e9;color:#fff;border:none}
    .btn-hero-archive{background:#1f2937;color:#fff;border:none}
    .stat-card{transition:transform .2s ease, box-shadow .2s ease}
    .stat-card:hover{transform:translateY(-3px);box-shadow:0 10px 20px rgba(0,0,0,.12)}
    .stat-badge{display:inline-block;border-radius:999px;padding:2px 8px;font-size:.8rem;font-weight:700}
    .stat-badge.green{background:#e6f8ef;color:#0f5132}
    .stat-badge.blue{background:#e6f0ff;color:#1d4ed8}
    .timeline{padding-left:48px}
    .timeline:before{left:24px;border-left:4px solid transparent;background:linear-gradient(180deg,#a7f3d0,#93c5fd);content:"";position:absolute;top:0;bottom:0;width:0}
    
    .timeline-item:hover{background:#f8fafc;border-radius:10px}
    .tag{display:inline-block;background:#e0f2fe;color:#0c4a6e;border-radius:999px;padding:2px 8px;font-size:.8rem;font-weight:700;border:1px solid #bae6fd}
    .duration{display:inline-block;background:#f0fdf4;color:#166534;border-radius:999px;padding:2px 8px;font-size:.8rem;font-weight:700;border:1px solid #bbf7d0}
    .gallery-grid{grid-template-columns:repeat(2,1fr);gap:10px}
    .gallery-item{position:relative;overflow:hidden;border-radius:10px}
    .gallery-item img{width:100%;height:120px;object-fit:cover}
    .gallery-item .overlay{position:absolute;inset:0;background:linear-gradient(135deg, rgba(14,165,233,.35), rgba(16,185,129,.35));opacity:0;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;transition:opacity .2s}
    .gallery-item:hover .overlay{opacity:1}
    .price-badge{background:#ecfdf5;color:#065f46;border:1px solid #b7f3de;border-radius:999px;padding:2px 8px;font-weight:700}
    .supplier-item{display:flex;align-items:center;justify-content:space-between}
    .supplier-left{display:flex;align-items:center;gap:10px}
    .supplier-icon{width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center}
    .supplier-icon.blue{background:#e0f2fe;color:#1e40af}
    .supplier-icon.green{background:#ecfdf5;color:#065f46}
    .supplier-status.green{background:#e6f8ef;color:#146b35;border-radius:999px;padding:2px 8px;font-weight:700}
    .supplier-status.blue{background:#eef2ff;color:#3730a3;border-radius:999px;padding:2px 8px;font-weight:700}
    .toggle-note{color:#6b7280}
    .indicator-bar{height:3px;background:#10b981;border-radius:999px;position:relative}
    .btn-ripple{position:relative;overflow:hidden}
    .ripple{position:absolute;border-radius:50%;background:rgba(255,255,255,.6);transform:scale(0);animation:rip .6s ease-out;pointer-events:none}
    @keyframes rip{to{transform:scale(7);opacity:0}}
  </style>
  </head>
<body>
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <?php $current_page='tours'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>

  <div class="main-content container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="mb-1">Chi tiết Tour</h4>
        <div class="text-muted">Thông tin đầy đủ về tour</div>
      </div>
      <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>?r=tours_edit&id=<?= (int)$tour['id'] ?>" class="btn btn-outline-primary"><i class="fas fa-edit me-1"></i>Sửa</a>
        <a href="<?= BASE_URL ?>?r=tours" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
    </div>
    </div>

    

    <ul class="nav nav-tabs" id="tourTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Tổng quan</button>
      </li>
    </ul>

    <?php $cover = $tour['cover_image'] ?? ($tour['image'] ?? ''); $isExternal = preg_match('/^https?:\/\//i', (string)$cover); $heroUrl = $cover ? ($isExternal ? $cover : (BASE_ASSETS_UPLOADS . $cover)) : (BASE_URL . 'assets/images/hero-default.jpg'); ?>
    <div class="card section-card hero-card">
      <div class="hero-overlay"></div>
      <div class="hero-content d-flex justify-content-between align-items-center">
        <div>
          <?php $activeLbl = !empty($stats['tourActive']) ? 'Đang Hoạt Động' : 'Tạm Dừng'; ?>
          <span class="badge-glow me-2"><i class="fas fa-power-off me-1"></i><?= $activeLbl ?></span>
          <h2 class="mb-1 text-white"><?= htmlspecialchars($tour['title']) ?></h2>
          <div class="text-white-50">Mã: <?= htmlspecialchars($stats['tourCode'] ?? ('T-' . (int)$tour['id'])) ?></div>
          <div class="text-white-50 mt-1"><i class="fas fa-layer-group me-1"></i><?= htmlspecialchars($category['name'] ?? 'Chưa phân loại') ?> • <i class="fas fa-tag me-1"></i><?= htmlspecialchars($types[$tour['tour_type']] ?? $tour['tour_type']) ?></div>
          <div class="hero-chips d-flex align-items-center flex-wrap gap-2 mt-2"><span class="chip">Tour riêng</span><span class="chip">2 ngày 1 đêm</span><span class="chip">Khởi hành hàng ngày</span></div>
        </div>
        <div class="hero-actions d-flex gap-2">
          <a href="<?= BASE_URL ?>?r=tours_edit&id=<?= (int)$tour['id'] ?>" class="btn btn-hero-edit btn-sm btn-ripple"><i class="fas fa-edit me-1"></i>Chỉnh Sửa</a>
          <a href="<?= BASE_URL ?>?r=tours_create&clone_from=<?= (int)$tour['id'] ?>" class="btn btn-hero-clone btn-sm btn-ripple"><i class="fas fa-clone me-1"></i>Nhân Bản</a>
          <a href="#" class="btn btn-hero-archive btn-sm btn-ripple"><i class="fas fa-box-archive me-1"></i>Lưu Trữ</a>
        </div>
      </div>
      <div style="position:absolute;inset:0;background-image:url('<?= $heroUrl ?>');background-size:cover;background-position:center;filter:brightness(0.75);z-index:-1"></div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-lg-3 col-sm-6">
        <div class="card section-card stat-card stat-primary">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small">Tổng chuyến</div>
                <div class="h5 mb-0"><?= (int)($stats['totalRuns'] ?? 0) ?></div>
                <?php $rt = (float)($stats['runsTrendPct'] ?? 0); $rc = $rt>0?'green':'blue'; ?>
                <div class="stat-badge <?= $rc ?>"><?= ($rt>0?'+':'') . sprintf('%.0f', abs($rt)) ?>% tháng này</div>
              </div>
              <i class="fas fa-clipboard-list fa-lg" style="color:#10b981"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6">
        <div class="card section-card stat-card stat-success">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small">Tổng hành khách</div>
                <div class="h5 mb-0"><?= (int)($stats['totalPassengers'] ?? 0) ?></div>
                <div class="stat-badge blue">TB <?= max(1,(int)round(((int)($stats['totalPassengers'] ?? 0)) / max(1,(int)($stats['totalRuns'] ?? 1)))) ?> khách/chuyến</div>
              </div>
              <i class="fas fa-users fa-lg" style="color:#0ea5e9"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6">
        <div class="card section-card stat-card stat-warning">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small">Tổng doanh thu</div>
                <div class="h5 mb-0 text-success"><?= number_format((float)($stats['totalRevenue'] ?? 0),0,',','.') ?>₫</div>
                <?php $vt = (float)($stats['revTrendPct'] ?? 0); ?>
                <div class="stat-badge green"><?= ($vt>0?'+':'') . sprintf('%.0f', abs($vt)) ?>% so tháng trước</div>
              </div>
              <i class="fas fa-coins fa-lg" style="color:#10b981"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6">
        <div class="card section-card stat-card stat-secondary">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small">Trạng thái</div>
                <div class="form-check form-switch">
                  <input class="form-check-input status-toggle" type="checkbox" <?= !empty($stats['tourActive']) ? 'checked' : '' ?>>
                  <label class="form-check-label"><?= !empty($stats['tourActive']) ? 'Hoạt Động' : 'Tạm Dừng' ?></label>
                </div>
                <div class="small toggle-note">Khi hoạt động, tour hiển thị trên website và nhận đặt chỗ mới.</div>
              </div>
              <i class="fas fa-toggle-on fa-lg" style="color:#334155"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-content pt-3" id="tourTabsContent">
      <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
        <div class="row">
      <div class="col-lg-8">
        <div class="card section-card">
          <div class="card-header bg-white"><h6 class="mb-0">Lịch trình (Timeline)</h6></div>
          <div class="card-body">
            <div class="timeline">
              <?php if(!empty($items)): ?>
                <?php foreach($items as $it): ?>
                  <div class="timeline-item">
                    <div class="d-flex justify-content-between">
                      <div class="fw-semibold">Ngày <?= (int)$it['day_number'] ?> • <?= htmlspecialchars($it['title'] ?? '') ?></div>
                      <div class="small text-muted"><?= htmlspecialchars($it['activity_time'] ?? '') ?><?= !empty($it['end_time']) ? (' - '.htmlspecialchars($it['end_time'])) : '' ?></div>
                    </div>
                    <div class="text-muted mb-1"><?= nl2br(htmlspecialchars($it['details'] ?? '')) ?></div>
                    <div class="small d-flex gap-2">
                      <?php if(!empty($it['meal_plan'])): ?><span class="tag"><i class="fas fa-utensils me-1"></i><?= htmlspecialchars($it['meal_plan']) ?></span><?php endif; ?>
                      <?php if(!empty($it['location'])): ?><span class="tag"><i class="fas fa-location-dot me-1"></i><?= htmlspecialchars($it['location']) ?></span><?php endif; ?>
                      <?php if(!empty($it['activity_time'])): ?><span class="duration"><?= !empty($it['end_time']) ? 'Thời lượng ' . htmlspecialchars($it['activity_time']) . ' - ' . htmlspecialchars($it['end_time']) : '' ?></span><?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php elseif(!empty($itineraries)): ?>
                <?php foreach($itineraries as $it): ?>
                  <div class="timeline-item">
                    <div class="fw-semibold">Ngày <?= (int)$it['day_number'] ?> • <?= htmlspecialchars($it['location'] ?? '') ?></div>
                    <div class="text-muted"><?= nl2br(htmlspecialchars($it['activities'] ?? '')) ?></div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-muted">Chưa có lịch trình, sẽ được cập nhật.</div>
              <?php endif; ?>
            </div>
        </div>
      </div>

        <div class="card section-card">
          <div class="card-header bg-white d-flex justify-content-between align-items-center"><h6 class="mb-0">Quản lý lịch khởi hành</h6><a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>?r=schedules_create&tour_id=<?= (int)$tour['id'] ?>"><i class="fas fa-plus me-1"></i>Tạo lịch</a></div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Ngày</th>
                    <th>HDV / Tài xế</th>
                    <th>Sức chứa</th>
                    <th>Giá</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($schedules)): foreach($schedules as $s): ?>
                    <?php 
                      $start = new DateTime($s['start_date']);
                      $end = new DateTime($s['end_date']);
                      $now = new DateTime('now');
                      $stLabel = ($now < $start) ? 'Sắp diễn ra' : (($now > $end) ? 'Kết thúc' : 'Đang diễn ra');
                      $stClass = ($stLabel==='Upcoming')?'bg-info':(($stLabel==='Ongoing')?'bg-success':'bg-secondary');
                      $adult = isset($s['price_adult']) ? (float)$s['price_adult'] : (float)($price['adult_price'] ?? 0);
                      $child = isset($s['price_child']) ? (float)$s['price_child'] : (float)($price['child_price'] ?? 0);
                      $capacity = (int)($s['max_capacity'] ?? 0);
                      $occupied = isset($s['booked_count']) ? (int)$s['booked_count'] : 0; 
                      if ($occupied === 0 && !empty($bookings)) { foreach ($bookings as $b) { if ((int)$b['schedule_id'] === (int)$s['id'] && ($b['booking_status'] ?? '') !== 'canceled') { $occupied += (int)($b['total_guests'] ?? 0); } } }
                      $occText = $capacity > 0 ? ($occupied . ' / ' . $capacity) : ($occupied . ' khách');
                      $occPercent = ($capacity>0) ? min(100, round(($occupied/$capacity)*100)) : 0;
                      $occClass = ($occPercent>90)?'bg-danger':'bg-success';
                      $guideName = !empty($s['guide_name']) ? $s['guide_name'] : 'Chưa phân công';
                      $driverName = !empty($s['driver_name']) ? $s['driver_name'] : 'Chưa phân công';
                      $gch = strtoupper(mb_substr(trim($guideName),0,1));
                      $dch = strtoupper(mb_substr(trim($driverName),0,1));
                    ?>
                    <tr>
                      <td>
                        <div><?= date('d/m/Y', strtotime($s['start_date'])) ?> → <?= date('d/m/Y', strtotime($s['end_date'])) ?></div>
                        <span class="badge <?= $stClass ?> mt-1"><?= $stLabel ?></span>
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <span class="avatar-badge" title="Guide"><?= htmlspecialchars($gch) ?></span> <span><?= htmlspecialchars($guideName) ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                          <span class="avatar-badge" title="Driver"><?= htmlspecialchars($dch) ?></span> <span><?= htmlspecialchars($driverName) ?></span>
                        </div>
                      </td>
                      <td style="min-width:180px">
                        <div class="small text-muted mb-1"><?= $occText ?></div>
                        <div class="progress" style="height:6px">
                          <div class="progress-bar <?= $occClass ?>" role="progressbar" style="width: <?= $occPercent ?>%"></div>
                        </div>
                      </td>
                      <td>
                        <div>NL: <strong class="text-success"><?= number_format($adult,0,',','.') ?>₫</strong></div>
                        <div>TE: <strong class="text-success"><?= number_format($child,0,',','.') ?>₫</strong></div>
                      </td>
                      <td>
                        <a href="<?= BASE_URL ?>?r=schedules_show&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-info">Danh sách đoàn</a>
                        <a href="<?= BASE_URL ?>?r=schedules_edit&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <a href="<?= BASE_URL ?>?r=schedules_delete&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận hủy lịch này?')">Hủy</a>
                      </td>
                    </tr>
                  <?php endforeach; else: ?>
                    <tr><td colspan="5" class="text-center text-muted">Chưa có lịch khởi hành</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        

        <div class="card section-card">
          <div class="card-header bg-white"><h6 class="mb-0">Chính sách</h6></div>
          <div class="card-body">
            <?php if(!empty($policy) && !empty($policy['name'])): ?>
              <div class="mb-2"><span class="badge-soft bg-light"><i class="fas fa-shield-alt me-1"></i><?= htmlspecialchars($policy['name']) ?></span><?php if(isset($policy['refund_percentage'])): ?> <span class="text-muted">(Hoàn: <?= number_format((float)$policy['refund_percentage'],0,',','.') ?>%)</span><?php endif; ?></div>
              <?php if(!empty($policy['description'])): ?><div><?= nl2br(htmlspecialchars($policy['description'])) ?></div><?php endif; ?>
            <?php else: ?>
              <div class="text-muted">Không có chính sách hủy/điều khoản đặc biệt.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card section-card">
          <div class="card-header bg-white d-flex justify-content-between align-items-center"><h6 class="mb-0">Bộ ảnh</h6><a href="<?= BASE_URL ?>?r=tours_edit&id=<?= (int)$tour['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus"></i></a></div>
          <div class="card-body">
            <div class="gallery-grid">
              <?php if(!empty($gallery)): ?>
                <?php foreach($gallery as $img): ?>
                  <div class="gallery-item"><img src="<?= BASE_ASSETS_UPLOADS . $img['image_path'] ?>" alt="image"><span class="overlay">Thư viện ảnh</span></div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-muted">Chưa có hình ảnh.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="card section-card">
          <div class="card-header bg-white"><h6 class="mb-0">Giá</h6></div>
          <div class="card-body">
            <div class="mb-2"><span class="price-badge">Mỗi người</span></div>
            <div class="d-flex flex-column gap-2">
              <div class="d-flex justify-content-between align-items-center"><span>Người lớn</span><span class="fw-semibold text-success"><?= number_format((float)($price['adult_price'] ?? 0),0,',','.') ?>₫</span></div>
              <div class="d-flex justify-content-between align-items-center"><span>Trẻ em (6-11)</span><span class="fw-semibold text-success"><?= number_format((float)($price['child_price'] ?? 0),0,',','.') ?>₫</span></div>
              <div class="d-flex justify-content-between align-items-center"><span>Em bé (0-5)</span><span class="fw-semibold text-success"><?= (float)($price['infant_price'] ?? 0) > 0 ? (number_format((float)$price['infant_price'],0,',','.') . '₫') : 'Miễn Phí' ?></span></div>
            </div>
          </div>
        </div>

        <div class="card section-card">
          <div class="card-header bg-white"><h6 class="mb-0">Nhà cung cấp</h6></div>
          <div class="card-body">
            <?php if(!empty($supplierServices)): ?>
              <ul class="list-group list-group-flush mb-0">
                <?php foreach($supplierServices as $s): ?>
                  <?php $nm = $s['name_clean'] ?? ($s['name'] ?? ''); $st = strtolower($s['status_parsed'] ?? ''); $badge = $st==='confirmed' ? 'bg-success' : ($st==='pending' ? 'bg-warning text-dark' : 'bg-secondary'); $txt = $st==='confirmed' ? 'Đã xác nhận' : ($st==='pending' ? 'Chờ' : 'Không rõ'); ?>
                  <li class="list-group-item supplier-item">
                    <div class="supplier-left">
                      <?php $t=strtolower($s['type'] ?? ''); $isHotel = strpos($t,'hotel')!==false || strpos($t,'khách sạn')!==false; ?>
                      <div class="supplier-icon <?= $isHotel ? 'blue' : 'green' ?>"> <i class="<?= $isHotel ? 'fas fa-hotel' : 'fas fa-van-shuttle' ?>"></i> </div>
                      <div>
                        <div class="fw-semibold"><?= htmlspecialchars($nm) ?></div>
                        <div class="small text-muted">Loại: <?= htmlspecialchars($s['type'] ?? '') ?></div>
                      </div>
                    </div>
                    <span class="supplier-status <?= ($txt==='Đã xác nhận') ? 'green' : 'blue' ?>"><?= $txt ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <div class="text-muted">Chưa có đối tác dịch vụ kèm theo.</div>
            <?php endif; ?>
          </div>
        </div>
        </div>
      </div>

    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080">
    <div id="tourToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body">Trạng thái tour đã cập nhật</div>
    </div>
  </div>
  <script>
    (function(){
      var toggles=document.querySelectorAll('.status-toggle');
      toggles.forEach(function(t){
        t.addEventListener('change',function(){
          var toastEl=document.getElementById('tourToast');
          if(toastEl){
            toastEl.classList.add('text-bg-success');
            var toast=new bootstrap.Toast(toastEl);toast.show();
          }
        });
      });
      document.querySelectorAll('.btn-ripple').forEach(function(btn){
        btn.addEventListener('click',function(e){
          var r=document.createElement('span');
          var rect=btn.getBoundingClientRect();
          var x=e.clientX-rect.left;var y=e.clientY-rect.top;r.className='ripple';r.style.left=x+'px';r.style.top=y+'px';r.style.width='20px';r.style.height='20px';
          btn.appendChild(r);setTimeout(function(){r.remove();},600);
        });
      });
    })();
  </script>
</body>
</html>
