<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Báo cáo lợi nhuận tài chính</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    :root{--blue:#3b82f6;--indigo:#4f46e5;--green:#10b981;--red:#ef4444;--slate:#64748b;--text:#0f172a;--purple:#8b5cf6}
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
    .margin-progress{height:12px;border-radius:999px;background:#e5e7eb}
    .margin-progress .progress-bar{background:linear-gradient(90deg,#22c55e,#10b981,#14b8a6);animation:shimmer 2s infinite}
    .pill-progress{height:12px;border-radius:999px}
    .sticky-header{position:sticky;top:0;z-index:1020}
    .chart-wrap{height:280px}
    @media (min-width: 1200px){ .chart-wrap{height:340px} }
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
    @keyframes shimmer{0%{filter:brightness(1)}50%{filter:brightness(1.2)}100%{filter:brightness(1)}}
    @keyframes bg-pan{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
  </style>
</head>
<body>
<?php $current_page='reports_profit'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
<div class="main-content">
  <div class="glass-card sticky-header mb-4 p-3">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-box"><i class="fa-solid fa-chart-line"></i></div>
        <div>
          <div class="h4 mb-0 text-gradient">Báo Cáo Lợi Nhuận Tài Chính</div>
          <div class="text-muted fw-semibold">Bảng Điều Khiển Quản Trị</div>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <?php $qs = http_build_query(['r'=>'reports_profit_export','format'=>'csv','start'=>($_GET['start']??''),'end'=>($_GET['end']??'')]); ?>
        <a class="btn btn-outline-primary rounded-pill" href="<?= BASE_URL ?>?<?= $qs ?>"><i class="fa-solid fa-file-csv me-1"></i>CSV</a>
        <?php $qs2 = http_build_query(['r'=>'reports_profit_export','format'=>'pdf','start'=>($_GET['start']??''),'end'=>($_GET['end']??'')]); ?>
        <a class="btn btn-outline-secondary rounded-pill" href="<?= BASE_URL ?>?<?= $qs2 ?>" target="_blank"><i class="fa-regular fa-file-pdf me-1"></i>PDF</a>
      </div>
    </div>
    <form id="filterForm" method="get" action="<?= BASE_URL ?>" class="mt-3">
      <input type="hidden" name="r" value="reports_profit">
      <div class="row g-2 align-items-center">
        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
            <input type="date" class="form-control rounded-pill" name="start" value="<?= htmlspecialchars($_GET['start'] ?? '') ?>" />
          </div>
        </div>
        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
            <input type="date" class="form-control rounded-pill" name="end" value="<?= htmlspecialchars($_GET['end'] ?? '') ?>" />
          </div>
        </div>
        <div class="col-md-3">
          <button type="button" id="filterBtn" class="btn btn-gradient rounded-pill w-100"><i class="fa-solid fa-filter me-1"></i>Lọc Dữ Liệu</button>
        </div>
      </div>
    </form>
  </div>

  <?php $totalRev=0; $totalCost=0; $totalProfit=0; $totalBookings=0; foreach(($rows??[]) as $r){ $totalRev += (float)$r['revenue']; $totalCost += (float)$r['cost']; $totalProfit += (float)$r['profit']; $totalBookings += (int)$r['booking_count']; } $marginPct = $totalRev>0 ? round(($totalProfit/$totalRev)*100,1) : 0; $revGrowth=0; $profitGrowth=0; if(!empty($trendRevenue) && count($trendRevenue)>=2){ $prev=$trendRevenue[count($trendRevenue)-2]; $last=$trendRevenue[count($trendRevenue)-1]; if((float)$prev!==0.0) $revGrowth=round((($last-$prev)/$prev)*100,1); } if(!empty($trendProfit) && count($trendProfit)>=2){ $prevp=$trendProfit[count($trendProfit)-2]; $lastp=$trendProfit[count($trendProfit)-1]; if((float)$prevp!==0.0) $profitGrowth=round((($lastp-$prevp)/$prevp)*100,1); } ?>
  <div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
      <div class="glass-card p-3 kpi-card">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted">Tổng doanh thu</div>
            <div class="display-6 kpi-value text-primary"><?= number_format($totalRev,0,',','.') ?>₫</div>
            <div class="text-success small"><?= $revGrowth>=0?('+'.$revGrowth):$revGrowth ?>% so kỳ trước</div>
          </div>
          <div class="icon-box"><i class="fa-solid fa-coins"></i></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
      <div class="glass-card p-3 kpi-card">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted">Tổng chi phí</div>
            <div class="display-6 kpi-value text-warning"><?= number_format($totalCost,0,',','.') ?>₫</div>
            <div class="text-muted small"> </div>
          </div>
          <div class="icon-box" style="background:linear-gradient(135deg,#f59e0b,#fb7185)"><i class="fa-solid fa-receipt"></i></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
      <div class="glass-card p-3 kpi-card">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted">Lợi nhuận ròng</div>
            <div class="display-6 kpi-value text-success"><?= number_format($totalProfit,0,',','.') ?>₫</div>
            <div class="text-success small"><?= $profitGrowth>=0?('+'.$profitGrowth):$profitGrowth ?>% so kỳ trước</div>
          </div>
          <div class="icon-box" style="background:linear-gradient(135deg,#10b981,#14b8a6)"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
      <div class="glass-card p-3 kpi-card">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted">Tỷ suất lợi nhuận</div>
            <div class="display-6 kpi-value" style="color:#8b5cf6"><?= number_format($marginPct,1,',','.') ?>%</div>
            <div class="text-muted small"> </div>
          </div>
          <div class="icon-box" style="background:linear-gradient(135deg,#8b5cf6,#4f46e5)"><i class="fa-solid fa-calculator"></i></div>
        </div>
      </div>
    </div>
  </div>

  <div class="glass-card mb-4 p-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="h5 mb-0">Xu Hướng Tài Chính</div>
      <div class="d-flex align-items-center gap-3"><span class="badge bg-primary">Doanh Thu</span><span class="badge bg-success">Lợi Nhuận</span></div>
    </div>
    <div class="chart-wrap"><canvas id="trendChart"></canvas></div>
  </div>

  <div class="glass-card p-0">
    <div class="p-3 pb-0"><div class="h5 mb-0">Hiệu Suất Tour</div><div class="text-muted">Phân tích chi tiết theo từng gói tour</div></div>
    <div class="table-responsive">
      <table class="table table-borderless table-hover align-middle thead-grad row-hover mb-0">
        <thead>
          <tr>
            <th>Tên tour</th>
            <th class="text-end">Doanh thu</th>
            <th class="text-end">Chi phí</th>
            <th class="text-end">Lợi nhuận</th>
            <th style="min-width:160px">Tỷ suất</th>
            <th class="text-end">Booking</th>
            <th class="text-end"></th>
          </tr>
        </thead>
        <tbody>
          <?php $totalRev=0;$totalCost=0;$totalProfit=0;$totalBookings=0; foreach(($rows ?? []) as $row): $totalRev+=$row['revenue']; $totalCost+=$row['cost']; $totalProfit+=$row['profit']; $totalBookings+=(int)$row['booking_count']; $name=trim($row['tour_title'] ?? ''); $parts=preg_split('/\s+/', $name); $abbr=''; foreach($parts as $w){ if($w!==''){ $abbr .= mb_strtoupper(mb_substr($w,0,1)); if(mb_strlen($abbr)>=2) break; } } $margin = (float)$row['margin']; $profitPos = (float)$row['profit']>=0; ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <div class="icon-box" style="width:36px;height:36px"><span class="fw-bold"><?= htmlspecialchars($abbr) ?></span></div>
                <div>
                  <div class="fw-bold" style="color:#0f172a"><?= htmlspecialchars($name) ?></div>
                  <div class="text-muted small"> </div>
                </div>
              </div>
            </td>
            <td class="text-end"><?= number_format((float)$row['revenue'],0,',','.') ?></td>
            <td class="text-end"><?= number_format((float)$row['cost'],0,',','.') ?></td>
            <td class="text-end <?= $profitPos ? 'text-success' : 'text-danger' ?>"><?= ($profitPos?'+':'') . number_format((float)$row['profit'],0,',','.') ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress margin-progress flex-grow-1">
                  <div class="progress-bar" role="progressbar" style="width: <?= max(0,min(100,$margin)) ?>%"></div>
                </div>
                <span class="fw-semibold" style="color:#475569"><?= number_format($margin,1,',','.') ?>%</span>
              </div>
            </td>
            <td class="text-end"><?= (int)$row['booking_count'] ?></td>
            <td class="text-end">
              <?php $link = BASE_URL . '?r=reports_profit_detail&tour_id=' . (int)$row['tour_id'] . '&start=' . urlencode($_GET['start']??'') . '&end=' . urlencode($_GET['end']??''); ?>
              <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= $link ?>">Chi tiết</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="table-dark">
            <th>Tổng cộng</th>
            <th class="text-end"><?= number_format($totalRev,0,',','.') ?></th>
            <th class="text-end"><?= number_format($totalCost,0,',','.') ?></th>
            <th class="text-end"><?= number_format($totalProfit,0,',','.') ?></th>
            <th>
              <div class="d-flex align-items-center gap-2">
                <div class="progress pill-progress flex-grow-1">
                  <div class="progress-bar" style="width: <?= $totalRev>0?round(($totalProfit/$totalRev)*100):0 ?>%; background:linear-gradient(90deg,#22c55e,#10b981,#14b8a6)"></div>
                </div>
                <span class="text-white fw-bold"><?= $totalRev>0?number_format(($totalProfit/$totalRev)*100,1,',','.'):'0.0' ?>%</span>
              </div>
            </th>
            <th class="text-end"><?= (int)$totalBookings ?></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index:1080">
    <div id="filterToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body">Đang lọc dữ liệu...</div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  var labels = <?= json_encode($trendLabels ?? []) ?>;
  var rev = <?= json_encode($trendRevenue ?? []) ?>;
  var profit = <?= json_encode($trendProfit ?? []) ?>;
  var ctx = document.getElementById('trendChart');
  if(ctx){
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {label:'Doanh Thu', data:rev, borderColor:'#3b82f6', backgroundColor:'rgba(59,130,246,.18)', tension:.3, fill:true},
          {label:'Lợi Nhuận', data:profit, borderColor:'#10b981', backgroundColor:'rgba(16,185,129,.18)', tension:.3, fill:true}
        ]
      },
      options: {responsive:true, maintainAspectRatio:false, animation:false, scales:{y:{beginAtZero:true}}, plugins:{legend:{position:'top'}}}
    });
  }
  var fb=document.getElementById('filterBtn');
  if(fb){
    fb.addEventListener('click',function(){
      var t=document.getElementById('filterToast'); if(t){ new bootstrap.Toast(t,{delay:1200}).show(); }
      setTimeout(function(){ document.getElementById('filterForm').submit(); }, 400);
    });
  }
</script>
</body>
</html>
