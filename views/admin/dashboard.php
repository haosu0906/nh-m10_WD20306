<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>TripMate Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet">
  <style>
    .metric-card{border-radius:.5rem;padding:.75rem}
    .metric-title{font-size:.85rem;color:#64748b}
    .metric-value{font-size:1.25rem;font-weight:600;color:#0f172a}
    .metric-sub{font-size:.8rem;color:#64748b}
    .metric-icon{width:34px;height:34px;border-radius:999px;display:flex;align-items:center;justify-content:center}
    .metric-icon.primary{background:rgba(13,110,253,.12);color:var(--primary)}
    .metric-icon.success{background:rgba(25,135,84,.12);color:var(--success)}
    .metric-icon.warning{background:rgba(253,126,20,.12);color:var(--warning)}
    .metric-icon.danger{background:rgba(220,53,69,.12);color:var(--danger)}
    .table{font-size:13.5px}
    .table td,.table th{padding:.55rem .65rem}
    .badge{border-radius:.4rem}
    .avatar{width:28px;height:28px;border-radius:999px;object-fit:cover}
    .customer-cell{display:flex;align-items:center;gap:.5rem}
    .action-group .btn{padding:.25rem .45rem;border-radius:.4rem}
  </style>
</head>
<body>
  <?php 
    $m = $metrics ?? ['mtdRevenue'=>128450,'newBookingsToday'=>37,'activeTours'=>124,'pendingIssues'=>9];
    $recent = $recentBookings ?? [
      ['id'=>'BK-10453','customer_name'=>'Nguyễn Minh Anh','customer_phone'=>'+84 912 345 678','tour_title'=>'Hà Nội – Sapa 3N2Đ','date_booked'=>'2025-12-04','amount'=>420,'booking_status'=>'pending','status'=>'pending'],
      ['id'=>'BK-10454','customer_name'=>'Trần Hải Yến','customer_phone'=>'+84 935 222 111','tour_title'=>'Đà Nẵng – Hội An 2N1Đ','date_booked'=>'2025-12-05','amount'=>310.5,'booking_status'=>'deposit','status'=>'deposit'],
      ['id'=>'BK-10455','customer_name'=>'Phạm Đức Long','customer_phone'=>'+84 907 888 777','tour_title'=>'Phú Quốc – Nghỉ dưỡng 4N3Đ','date_booked'=>'2025-12-06','amount'=>899,'booking_status'=>'completed','status'=>'completed'],
    ];
    $fmtRevenue = function($v){ return '$' . number_format((float)$v, 2); };
    $label = function($row){ $s = $row['booking_status'] ?? $row['status'] ?? 'pending'; $map = ['pending'=>['Pending','warning'], 'deposit'=>['Deposited','success'], 'completed'=>['Completed','secondary'], 'canceled'=>['Cancelled','danger'], 'cancelled'=>['Cancelled','danger']]; return $map[$s] ?? ['Pending','warning']; };
  ?>
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <?php $current_page='home'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>

  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>

  <div class="main-content">
    <div class="container-fluid">
      <div class="row g-3 mb-2">
        <div class="col-12"><h4 class="mb-0">Dashboard</h4></div>
      </div>

      <div class="row g-3">
        <div class="col-xl-3 col-sm-6">
          <div class="card shadow-sm metric-card">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-3">
                <div class="metric-icon primary"><i class="fa-solid fa-sack-dollar"></i></div>
                <div>
                  <div class="metric-title">Total Revenue</div>
                  <div class="metric-value"><?= $fmtRevenue($m['mtdRevenue'] ?? 0) ?></div>
                  <div class="metric-sub"><span class="text-success"><i class="fa-solid fa-arrow-up"></i> 5.2%</span> vs last week</div>
                </div>
              </div>
              <div class="text-secondary small">MTD</div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card shadow-sm metric-card">
            <div class="d-flex align-items-center gap-3">
              <div class="metric-icon success"><i class="fa-solid fa-ticket"></i></div>
              <div>
                <div class="metric-title">New Bookings (Today)</div>
                <div class="metric-value"><?= (int)($m['newBookingsToday'] ?? 0) ?></div>
                <div class="metric-sub"><span class="text-success"><i class="fa-solid fa-arrow-up"></i> 12%</span> vs yesterday</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card shadow-sm metric-card">
            <div class="d-flex align-items-center gap-3">
              <div class="metric-icon warning"><i class="fa-solid fa-route"></i></div>
              <div>
                <div class="metric-title">Active Tours</div>
                <div class="metric-value"><?= (int)($m['activeTours'] ?? 0) ?></div>
                <div class="metric-sub">Across 18 destinations</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card shadow-sm metric-card">
            <div class="d-flex align-items-center gap-3">
              <div class="metric-icon danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
              <div>
                <div class="metric-title">Pending Issues</div>
                <div class="metric-value"><?= (int)($m['pendingIssues'] ?? 0) ?></div>
                <div class="metric-sub">Payments & cancellations</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mt-2">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
              <h6 class="mb-0">Recent Bookings & Action Needed</h6>
              <div class="d-flex gap-2">
                <button class="btn btn-light btn-sm"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <button class="btn btn-light btn-sm"><i class="fa-solid fa-arrow-rotate-right me-1"></i> Refresh</button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Booking ID</th>
                      <th>Customer</th>
                      <th>Tour Name</th>
                      <th>Date</th>
                      <th class="text-end">Amount</th>
                      <th>Status</th>
                      <th class="text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($recent as $r): $lab = $label($r); $badgeText=$lab[0]; $badgeClass=$lab[1]; ?>
                    <tr>
                      <td>#<?= htmlspecialchars($r['id']) ?></td>
                      <td>
                        <div class="customer-cell">
                          <img class="avatar" src="https://i.pravatar.cc/40" alt="">
                          <div>
                            <div class="fw-semibold"><?= htmlspecialchars($r['customer_name'] ?? '—') ?></div>
                            <div class="text-secondary" style="font-size:.82rem"><?= htmlspecialchars($r['customer_phone'] ?? '—') ?></div>
                          </div>
                        </div>
                      </td>
                      <td><?= htmlspecialchars($r['tour_title'] ?? '—') ?></td>
                      <td><?= htmlspecialchars(isset($r['date_booked']) ? date('d/m/Y', strtotime($r['date_booked'])) : '—') ?></td>
                      <td class="text-end"><?= $fmtRevenue($r['amount'] ?? 0) ?></td>
                      <td><span class="badge bg-<?= $badgeClass ?><?= $badgeClass==='warning'?' text-dark':'' ?>"><?= $badgeText ?></span></td>
                      <td class="text-center action-group">
                        <a class="btn btn-light btn-sm" title="View" href="#"><i class="fa-regular fa-eye"></i></a>
                        <a class="btn btn-light btn-sm" title="Edit" href="#"><i class="fa-regular fa-pen-to-square"></i></a>
                        <button class="btn btn-light btn-sm" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
              <div class="text-secondary" style="font-size:.88rem">Showing 1–3 of 3</div>
              <div class="d-flex gap-2">
                <button class="btn btn-light btn-sm"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="btn btn-light btn-sm"><i class="fa-solid fa-chevron-right"></i></button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
