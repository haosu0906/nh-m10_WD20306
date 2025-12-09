<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Báo cáo công nợ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    :root{--blue:#3b82f6;--indigo:#4f46e5;--green:#10b981;--pink:#ec4899;--orange:#f59e0b;--slate:#64748b;--text:#0f172a;--red:#ef4444}
    body{font-family:'Inter','Plus Jakarta Sans',system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans','Liberation Sans',sans-serif;background:linear-gradient(120deg,#f8fafc,#eef2ff,#fff1f2);background-size:400% 400%;animation:bg-pan 15s infinite}
    .glass-card{background:rgba(255,255,255,.95);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.8);box-shadow:0 8px 32px rgba(31,38,135,.15);border-radius:24px}
    .text-gradient{background:linear-gradient(90deg,#3b82f6,#8b5cf6);-webkit-background-clip:text;background-clip:text;color:transparent}
    .icon-box{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#3b82f6,#4f46e5);display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 6px 18px rgba(63,81,181,.25)}
    .btn-gradient{background:linear-gradient(135deg,#3b82f6,#4f46e5);color:#fff;border:none}
    .kpi-card{animation:float 6s ease-in-out infinite}
    .kpi-value{font-weight:800}
    .thead-grad thead th{background:linear-gradient(180deg,#eef2ff,#f8fafc);text-transform:uppercase;letter-spacing:.06em;font-size:12px;color:#475569;border-bottom:1px solid #e2e8f0}
    .row-hover tbody tr{transition:transform .15s ease}
    .row-hover tbody tr:hover{transform:translateX(8px)}
    .pill-progress{height:12px;border-radius:999px;background:#e5e7eb}
    .pill-progress .progress-bar{background:linear-gradient(90deg,#22c55e,#10b981,#14b8a6);animation:shimmer 2s infinite}
    .sticky-header{position:sticky;top:0;z-index:1020}
    .chart-wrap{height:280px}
    @media (min-width: 1200px){ .chart-wrap{height:340px} }
    .btn-ripple{position:relative;overflow:hidden}
    .ripple{position:absolute;border-radius:50%;background:rgba(255,255,255,.3);transform:scale(0);animation:rip .6s ease-out;pointer-events:none}
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
    @keyframes shimmer{0%{filter:brightness(1)}50%{filter:brightness(1.2)}100%{filter:brightness(1)}}
    @keyframes bg-pan{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
    @keyframes rip{to{transform:scale(12);opacity:0}}
  </style>
</head>
<body>
  <?php $current_page='reports_debts'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
  <div class="main-content">
    <div class="glass-card sticky-header mb-4 p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
          <div class="icon-box"><i class="fa-solid fa-wallet"></i></div>
          <div>
            <div class="h4 mb-0 text-gradient">Báo Cáo Công Nợ</div>
            <div class="text-muted fw-semibold">Phân tích thu hồi và khoản còn nợ</div>
          </div>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-outline-secondary rounded-pill" href="<?= BASE_URL ?>?r=reports_profit"><i class="fa-solid fa-chart-line me-1"></i> Lợi nhuận</a>
          <a class="btn btn-outline-primary rounded-pill" href="<?= BASE_URL ?>?r=reports_debts_export<?= isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? ('&' . $_SERVER['QUERY_STRING']) : '' ?>"><i class="fa-solid fa-file-csv me-1"></i> Xuất CSV</a>
        </div>
      </div>
      <form id="debtsFilter" method="get" action="<?= BASE_URL ?>" class="mt-3">
        <input type="hidden" name="r" value="reports_debts" />
        <div class="row g-2 align-items-center">
          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-list"></i></span>
              <input type="number" class="form-control rounded-pill" name="tour_id" value="<?= htmlspecialchars($_GET['tour_id'] ?? '') ?>" placeholder="ID tour">
            </div>
          </div>
          <div class="col-md-3">
            <?php $st = $_GET['status'] ?? ''; ?>
            <select class="form-select rounded-pill" name="status">
              <option value="" <?= $st===''?'selected':'' ?>>Tất cả</option>
              <option value="pending" <?= $st==='pending'?'selected':'' ?>>Chờ xác nhận</option>
              <option value="deposit" <?= $st==='deposit'?'selected':'' ?>>Đã cọc</option>
              <option value="completed" <?= $st==='completed'?'selected':'' ?>>Hoàn tất</option>
              <option value="canceled" <?= $st==='canceled'?'selected':'' ?>>Hủy</option>
            </select>
          </div>
          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
              <input type="date" class="form-control rounded-pill" name="start" value="<?= htmlspecialchars($_GET['start'] ?? '') ?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
              <input type="date" class="form-control rounded-pill" name="end" value="<?= htmlspecialchars($_GET['end'] ?? '') ?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-filter"></i></span>
              <input type="number" class="form-control rounded-pill" name="min_remaining" value="<?= htmlspecialchars($_GET['min_remaining'] ?? '0') ?>" placeholder="Còn nợ từ">
            </div>
          </div>
          <div class="col-md-3">
            <button type="button" id="debtsFilterBtn" class="btn btn-gradient rounded-pill w-100 btn-ripple"><i class="fa-solid fa-filter me-1"></i>Lọc Dữ Liệu</button>
          </div>
        </div>
      </form>
    </div>

    <?php $sumTotal=0;$sumPaid=0;$sumRemaining=0;$chartMap=[]; if(!empty($items)){ foreach($items as $row){ $sumTotal+=(float)($row['total']??0); $sumPaid+=(float)($row['paid']??0); $sumRemaining+=(float)($row['remaining']??0); $d = !empty($row['date_booked']) ? date('Y-m', strtotime($row['date_booked'])) : date('Y-m'); if(!isset($chartMap[$d])) $chartMap[$d] = ['paid'=>0,'remain'=>0]; $chartMap[$d]['paid'] += (float)($row['paid']??0); $chartMap[$d]['remain'] += (float)($row['remaining']??0); } }
      $labels = array_keys($chartMap); sort($labels); $chartPaid=[]; $chartRemain=[]; foreach($labels as $m){ $chartPaid[] = $chartMap[$m]['paid']; $chartRemain[] = $chartMap[$m]['remain']; }
      $rate = $sumTotal>0 ? round(($sumPaid/$sumTotal)*100,1) : 0; ?>

    <div class="row g-4 mb-4">
      <div class="col-12 col-md-6 col-xl-3"><div class="glass-card p-3 kpi-card"><div class="d-flex align-items-center justify-content-between"><div><div class="text-muted">Tổng giá trị</div><div class="display-6 kpi-value text-primary"><?= number_format($sumTotal,0,',','.') ?>₫</div></div><div class="icon-box"><i class="fa-solid fa-file-invoice-dollar"></i></div></div></div></div>
      <div class="col-12 col-md-6 col-xl-3"><div class="glass-card p-3 kpi-card"><div class="d-flex align-items-center justify-content-between"><div><div class="text-muted">Đã thu</div><div class="display-6 kpi-value text-success"><?= number_format($sumPaid,0,',','.') ?>₫</div></div><div class="icon-box" style="background:linear-gradient(135deg,#10b981,#14b8a6)"><i class="fa-solid fa-money-bill-wave"></i></div></div></div></div>
      <div class="col-12 col-md-6 col-xl-3"><div class="glass-card p-3 kpi-card"><div class="d-flex align-items-center justify-content-between"><div><div class="text-muted">Còn nợ</div><div class="display-6 kpi-value" style="color:#ef4444"><?= number_format($sumRemaining,0,',','.') ?>₫</div></div><div class="icon-box" style="background:linear-gradient(135deg,#ef4444,#fb7185)"><i class="fa-solid fa-triangle-exclamation"></i></div></div></div></div>
      <div class="col-12 col-md-6 col-xl-3"><div class="glass-card p-3 kpi-card"><div class="d-flex align-items-center justify-content-between"><div><div class="text-muted">Tỷ lệ thu hồi</div><div class="display-6 kpi-value" style="color:#8b5cf6"><?= number_format($rate,1,',','.') ?>%</div></div><div class="icon-box" style="background:linear-gradient(135deg,#8b5cf6,#4f46e5)"><i class="fa-solid fa-gauge-high"></i></div></div></div></div>
    </div>

    <div class="glass-card mb-4 p-3">
      <div class="d-flex justify-content-between align-items-center mb-2"><div class="h5 mb-0">Công nợ theo thời gian</div><div class="d-flex align-items-center gap-3"><span class="badge bg-success">Đã thu</span><span class="badge bg-danger">Còn nợ</span></div></div>
      <div class="chart-wrap"><canvas id="debtsChart"></canvas></div>
    </div>

    <div class="glass-card p-0">
      <div class="p-3 pb-0"><div class="h5 mb-0">Danh sách công nợ</div></div>
      <div class="table-responsive">
        <table class="table table-borderless table-hover align-middle thead-grad row-hover mb-0">
          <thead>
            <tr>
              <th>Booking</th>
              <th>Tour</th>
              <th class="text-end">Tổng tiền</th>
              <th class="text-end">Đã thu</th>
              <th class="text-end">Còn nợ</th>
              <th style="min-width:160px">Tỷ lệ thu</th>
              <th>Trạng thái</th>
              <th>Ngày đặt</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($items)): foreach ($items as $row): $pct = ((float)($row['total']??0)>0)? round(((float)($row['paid']??0)/(float)($row['total']))*100,1) : 0; ?>
            <tr>
              <td>#<?= (int)$row['booking_id'] ?></td>
              <td>
                <div class="d-flex align-items-center gap-3"><div class="icon-box" style="width:36px;height:36px"><i class="fa-solid fa-ticket"></i></div><div><div class="fw-bold" style="color:#0f172a"><?= htmlspecialchars($row['tour_title']) ?></div><div class="text-muted small"></div></div></div>
              </td>
              <td class="text-end"><?= number_format($row['total'], 0, ',', '.') ?></td>
              <td class="text-end"><?= number_format($row['paid'], 0, ',', '.') ?></td>
              <td class="text-end fw-semibold <?= $row['remaining']>0?'text-danger':'' ?>"><?= number_format($row['remaining'], 0, ',', '.') ?></td>
              <td>
                <div class="d-flex align-items-center gap-2"><div class="progress pill-progress flex-grow-1"><div class="progress-bar" role="progressbar" style="width: <?= max(0,min(100,$pct)) ?>%"></div></div><span class="fw-semibold" style="color:#475569"><?= number_format($pct,1,',','.') ?>%</span></div>
              </td>
              <td><span class="badge bg-secondary"><?= htmlspecialchars($row['status']) ?></span></td>
              <td><?= htmlspecialchars($row['date_booked'] ?? '') ?></td>
              <td><a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= BASE_URL ?>?r=booking_detail&id=<?= (int)$row['booking_id'] ?>">Xem booking</a></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="9" class="text-center text-muted">Không có dữ liệu</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index:1080"><div id="debtsToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true"><div class="toast-body">Đang lọc dữ liệu...</div></div></div>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.querySelectorAll('.btn-ripple').forEach(function(btn){ btn.addEventListener('click',function(e){ var r=document.createElement('span'); var rect=btn.getBoundingClientRect(); var x=e.clientX-rect.left; var y=e.clientY-rect.top; r.className='ripple'; r.style.left=x+'px'; r.style.top=y+'px'; r.style.width='20px'; r.style.height='20px'; btn.appendChild(r); setTimeout(function(){ r.remove(); },600); }); });
    var fBtn=document.getElementById('debtsFilterBtn'); if(fBtn){ fBtn.addEventListener('click',function(){ var t=document.getElementById('debtsToast'); if(t){ new bootstrap.Toast(t,{delay:1200}).show(); } setTimeout(function(){ document.getElementById('debtsFilter').submit(); }, 400); }); }
    var labels = <?= json_encode($labels ?? []) ?>; var paid = <?= json_encode($chartPaid ?? []) ?>; var remain = <?= json_encode($chartRemain ?? []) ?>; var ctx = document.getElementById('debtsChart'); if(ctx){ new Chart(ctx,{ type:'line', data:{ labels:labels, datasets:[ {label:'Đã thu', data:paid, borderColor:'#10b981', backgroundColor:'rgba(16,185,129,.18)', tension:.3, fill:true}, {label:'Còn nợ', data:remain, borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,.18)', tension:.3, fill:true} ] }, options:{ responsive:true, maintainAspectRatio:false, animation:false, plugins:{ legend:{ position:'top' } }, scales:{ y:{ beginAtZero:true } } }); }
  </script>
</body>
</html>
