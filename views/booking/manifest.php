<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Danh sách đoàn — Booking #<?= (int)$booking['id'] ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
  <style>
    :root{--blue:#3b82f6;--indigo:#4f46e5;--slate:#475569;--text:#0f172a;--muted:#64748b;--green:#10b981;--teal:#14b8a6;--red:#ef4444}
    body{background:#f8fafc}
    .card-glass{background:rgba(255,255,255,.95);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.8);border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.08)}
    .header-card{padding:18px}
    .clip-icon{width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,var(--blue),var(--indigo));display:flex;align-items:center;justify-content:center;color:#fff}
    .bk-id{font-size:24px;font-weight:700;background:linear-gradient(90deg,#0f172a,#475569);-webkit-background-clip:text;background-clip:text;color:transparent}
    .badge-confirm{background:#dbeafe;border:1px solid #93c5fd;color:#1e40af;font-weight:700}
    .header-actions .btn{border-radius:10px}
    .btn-print{background:#fff;border:1px solid #e2e8f0;color:#475569}
    .btn-export{background:linear-gradient(135deg,var(--blue),var(--indigo));color:#fff;border:none}
    .stat{transition:transform .2s ease, box-shadow .2s ease;border-top:4px solid transparent}
    .stat:hover{transform:translateY(-8px) scale(1.02);box-shadow:0 20px 40px rgba(0,0,0,.12)}
    .stat.border-grad{border-image:linear-gradient(90deg,#667eea,#764ba2,#f093fb) 1}
    .stat .icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff}
    .icon.users{background:linear-gradient(135deg,#3b82f6,#2563eb)}
    .icon.check{background:linear-gradient(135deg,#10b981,#059669)}
    .icon.cross{background:linear-gradient(135deg,#ef4444,#dc2626)}
    .stat .num{font-size:36px}
    .stat .label{color:#64748b;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
    .progress.shimmer{position:relative;height:8px;background:#f1f5f9}
    .progress.shimmer .progress-bar{background:linear-gradient(90deg,#10b981,#059669)}
    .progress.shimmer:after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,.3),transparent);animation:sh 2s infinite}
    @keyframes sh{0%{transform:translateX(-100%)}100%{transform:translateX(100%)}}
    .action-bar .form-control{border:1px solid #e2e8f0}
    .action-bar .form-control:focus{border-color:var(--blue);box-shadow:0 0 0 4px rgba(59,130,246,.1);transform:translateY(-2px)}
    .btn-filter{background:#fff;border:1px solid #e2e8f0;color:#475569}
    .btn-checkall{background:linear-gradient(135deg,var(--green),var(--teal));color:#fff;border:none;font-weight:700}
    .btn-ripple{position:relative;overflow:hidden}
    .ripple{position:absolute;border-radius:50%;background:rgba(255,255,255,.3);transform:scale(0);animation:rip .6s ease-out;pointer-events:none}
    @keyframes rip{to{transform:scale(12);opacity:0}}
    .table thead th{background:linear-gradient(180deg,#f8fafc,#f1f5f9);color:#475569;font-size:12px;font-weight:700;text-transform:uppercase;border-bottom:1px solid #e2e8f0}
    .guest-row{transition:transform .15s ease;border-left:3px solid transparent;position:relative}
    .guest-row:hover{transform:translateX(4px);background:linear-gradient(90deg,#f0f9ff,#e0f2fe)}
    .guest-row:hover:after{content:"";position:absolute;right:6px;top:20%;width:3px;height:60%;background:linear-gradient(180deg,#667eea,#764ba2,#f093fb);border-radius:10px}
    .avatar{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700}
    .g1{background:linear-gradient(135deg,#3b82f6,#2563eb)}.g2{background:linear-gradient(135deg,#10b981,#059669)}.g3{background:linear-gradient(135deg,#f59e0b,#d97706)}.g4{background:linear-gradient(135deg,#ef4444,#dc2626)}.g5{background:linear-gradient(135deg,#8b5cf6,#7c3aed)}.g6{background:linear-gradient(135deg,#ec4899,#db2777)}.g7{background:linear-gradient(135deg,#14b8a6,#0d9488)}.g8{background:linear-gradient(135deg,#f97316,#ea580c)}.g9{background:linear-gradient(135deg,#06b6d4,#0891b2)}.g10{background:linear-gradient(135deg,#a855f7,#9333ea)}
    .tag-adult{background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe;border-radius:999px;padding:2px 8px;font-weight:700}
    .tag-child{background:#fef3c7;color:#92400e;border:1px solid #fde68a;border-radius:999px;padding:2px 8px;font-weight:700}
    .status-badge.arrived{background:linear-gradient(180deg,#d1fae5,#a7f3d0);color:#065f46;border:1px solid #6ee7b7;border-radius:999px;padding:2px 8px;font-weight:700}
    .status-badge.pending{background:linear-gradient(180deg,#f1f5f9,#e2e8f0);color:#475569;border:1px solid #cbd5e1;border-radius:999px;padding:2px 8px;font-weight:700}
    .status-badge.noshow{background:linear-gradient(180deg,#fee2e2,#fecaca);color:#7f1d1d;border:1px solid #fca5a5;border-radius:999px;padding:2px 8px;font-weight:700}
    .switch .form-check-input{width:48px;height:26px}
    .switch .form-check-input:checked{background-color:#10b981;border-color:#059669}
    .switch .form-check-input:focus{box-shadow:none}
    .toast-dark{background:linear-gradient(135deg,#1e293b,#0f172a);color:#fff;border-radius:12px}
    ::-webkit-scrollbar{width:12px}
    ::-webkit-scrollbar-track{background:linear-gradient(180deg,#f1f5f9,#e2e8f0)}
    ::-webkit-scrollbar-thumb{background:linear-gradient(180deg,#3b82f6,#2563eb);border:3px solid #f1f5f9;border-radius:10px}
    .btn-noshowall{background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border:none;font-weight:700}
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <?php $current_page='booking'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
  <?php
    $scheduleInfo = null;
    try {
      if (!empty($booking['schedule_id'])) {
        require_once __DIR__ . '/../../models/ScheduleModel.php';
        $scm = new ScheduleModel();
        $scheduleInfo = $scm->find((int)$booking['schedule_id']);
      }
    } catch (Throwable $e) {}
    $statusText = 'Đã Xác Nhận';
  ?>

  <div class="main-content">
    <div class="card-glass header-card mb-3">
      <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <div class="clip-icon"><i class="fa-solid fa-clipboard"></i></div>
          <div>
            <div class="bk-id">#BK<?= (int)$booking['id'] ?></div>
            <div class="fw-bold" style="font-size:16px; color:var(--text)"><?= htmlspecialchars($booking['tour_name'] ?? ($booking['title'] ?? '')) ?></div>
            <div class="text-muted"><i class="fa-regular fa-calendar me-1"></i>
              <?php if(!empty($scheduleInfo)): ?>
                <?= date('d/m/Y', strtotime($scheduleInfo['start_date'] ?? 'now')) ?>
              <?php else: ?>
                <?= date('d/m/Y', strtotime($booking['created_at'] ?? 'now')) ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 header-actions">
          <span class="badge badge-confirm"><?= $statusText ?></span>
          <button type="button" class="btn btn-print btn-ripple" onclick="window.print()"><i class="fa-solid fa-print me-1"></i>In Danh Sách</button>
          <?php if(!empty($booking['schedule_id'])): ?>
            <a href="<?= BASE_URL ?>?r=tour_manifest_export&departure_id=<?= (int)$booking['schedule_id'] ?>&type=csv" class="btn btn-export btn-ripple"><i class="fa-solid fa-file-export me-1"></i>Xuất Excel</a>
          <?php else: ?>
            <button class="btn btn-export" disabled><i class="fa-solid fa-file-export me-1"></i>Xuất Excel</button>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php $pct = ($stats['total']>0)? round($stats['checked']*100/$stats['total']) : 0; ?>
    <div class="row g-3 mb-3">
      <div class="col-lg-4 col-md-6">
        <div class="card-glass p-3 stat border-grad">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="label">TỔNG KHÁCH</div>
              <div id="st-total" class="num" style="color:var(--blue)"><?= (int)$stats['total'] ?></div>
            </div>
            <div class="icon users"><i class="fa-solid fa-users"></i></div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card-glass p-3 stat border-grad">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
              <div class="label">ĐÃ CHECK‑IN</div>
              <div id="st-checked" class="num" style="color:var(--green)"><?= (int)$stats['checked'] ?></div>
            </div>
            <div class="icon check"><i class="fa-solid fa-check"></i></div>
          </div>
          <div class="progress shimmer"><div id="st-bar" class="progress-bar" role="progressbar" style="width: <?= $pct ?>%"></div></div>
          <div id="st-pct" class="small mt-1" style="color:var(--green)"><?= $pct ?>%</div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card-glass p-3 stat border-grad">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="label">VẮNG MẶT</div>
              <div id="st-noshow" class="num" style="color:var(--red)"><?= (int)$stats['noshow'] ?></div>
            </div>
            <div class="icon cross"><i class="fa-solid fa-xmark"></i></div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-glass p-3 mb-3 action-bar">
      <div class="row g-2 align-items-center">
        <div class="col-md-6">
          <div class="input-group">
            <span class="input-group-text" style="color:var(--blue)"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input id="searchInput" type="text" class="form-control" placeholder="Tìm kiếm hành khách..." />
          </div>
        </div>
        <div class="col-md-3">
          <button type="button" class="btn btn-filter w-100"><i class="fa-solid fa-filter me-1"></i>Lọc</button>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <form method="post" action="<?= BASE_URL ?>?r=booking_group_checkin" class="d-flex justify-content-end">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>" />
            <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>" />
            <input type="hidden" name="ajax" value="1" />
            <button class="btn btn-checkall btn-ripple w-100"><i class="fa-solid fa-user-check me-1"></i>Check‑in Tất Cả</button>
          </form>
          <form method="post" action="<?= BASE_URL ?>?r=booking_group_noshow" class="d-flex justify-content-end">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>" />
            <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>" />
            <input type="hidden" name="ajax" value="1" />
            <button class="btn btn-noshowall btn-ripple w-100"><i class="fa-solid fa-user-xmark me-1"></i>Vắng Mặt Tất Cả</button>
          </form>
        </div>
      </div>
    </div>

    <div class="card-glass p-0">
      <div class="p-3"><h5 class="mb-0">Danh sách hành khách</h5></div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th style="width:33%">Hành khách</th>
              <th style="width:17%">Liên hệ</th>
              <th style="width:25%">Ghi chú</th>
              <th style="width:17%">Trạng thái</th>
              <th style="width:8%" class="text-center">Hành động</th>
            </tr>
          </thead>
          <tbody id="guestTableBody">
            <?php $i=0; foreach ($guests as $g): $i++; ?>
              <?php $checked=!empty($g['is_checked_in']); $ns=!empty($g['is_no_show']); $gt=strtolower($g['guest_type'] ?? 'adult'); $name=trim((string)($g['full_name'] ?? 'Khách')); $init=''; foreach (preg_split('/\s+/', $name) as $w){ if($w!=='' && mb_strlen($init)<2){ $init .= mb_strtoupper(mb_substr($w,0,1)); } } $color='g'.(($i%10)+1); ?>
              <tr class="guest-row" data-gid="<?= (int)$g['id'] ?>">
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <div class="avatar <?= $color ?>"><?= htmlspecialchars($init) ?></div>
                    <div>
                      <div class="fw-bold" style="color:#0f172a; font-size:15px;"><?= htmlspecialchars($name) ?></div>
                      <div class="mt-1">
                        <span class="<?= $gt==='child' ? 'tag-child' : 'tag-adult' ?>"><?= $gt==='child' ? 'TRẺ EM' : 'NGƯỜI LỚN' ?></span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="text-muted">
                  <i class="fa-solid fa-phone me-1" style="color:var(--blue)"></i><?= htmlspecialchars((string)($g['phone'] ?? $g['guest_phone'] ?? '')) ?>
                </td>
                <td><span class="fst-italic" style="color:#64748b; font-size:13px;"><?= htmlspecialchars((string)($g['notes'] ?? '')) ?: 'Không có ghi chú' ?></span></td>
                <td>
                  <span class="status-badge <?= ($ns?'noshow':($checked?'arrived':'pending')) ?>">
                    <?php if($ns): ?>Vắng mặt<?php elseif($checked): ?>Đã đến<?php else: ?>Chờ đến<?php endif; ?>
                  </span>
                </td>
                <td class="text-center">
                  <div class="d-flex align-items-center justify-content-center gap-2">
                    <div class="form-check form-switch switch">
                      <input class="form-check-input guest-toggle" type="checkbox" <?= $checked && !$ns ? 'checked' : '' ?> data-guest="<?= (int)$g['id'] ?>" data-booking="<?= (int)$booking['id'] ?>">
                    </div>
                    <button class="btn btn-sm btn-outline-danger noshow-toggle" title="<?= $ns?'Bỏ vắng mặt':'Đánh dấu vắng mặt' ?>" data-guest="<?= (int)$g['id'] ?>" data-flag="<?= $ns?0:1 ?>"><i class="fa-solid fa-user-xmark"></i></button>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                      <ul class="dropdown-menu">
                        <li><button class="dropdown-item noshow-toggle" data-guest="<?= (int)$g['id'] ?>" data-flag="<?= $ns?0:1 ?>"><?= $ns?'Bỏ vắng mặt':'Đánh dấu vắng mặt' ?></button></li>
                      </ul>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-3">
      <a href="<?= BASE_URL ?>?r=booking_detail&id=<?= (int)$booking['id'] ?>" class="btn btn-outline-secondary">Quay lại chi tiết Booking</a>
    </div>
  </div>
<div class="position-fixed bottom-0 start-50 translate-middle-x mb-3" style="z-index:1080">
  <div id="manifestToast" class="toast toast-dark" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body"><i class="fa-solid fa-check me-2" style="color:#10b981"></i>Đã cập nhật trạng thái hành khách</div>
  </div>
</div>
<script>
  (function(){
    var toastEl = document.getElementById('manifestToast');
    function showToast(){ if(toastEl){ new bootstrap.Toast(toastEl,{delay:2500}).show(); } }
    document.querySelectorAll('.btn-ripple').forEach(function(btn){
      btn.addEventListener('click',function(e){
        var r=document.createElement('span');var rect=btn.getBoundingClientRect();var x=e.clientX-rect.left;var y=e.clientY-rect.top;r.className='ripple';r.style.left=x+'px';r.style.top=y+'px';r.style.width='20px';r.style.height='20px';btn.appendChild(r);setTimeout(function(){r.remove();},600);
      });
    });
    var searchInput=document.getElementById('searchInput');
    if(searchInput){
      searchInput.addEventListener('input',function(){
        var q=searchInput.value.toLowerCase();
        document.querySelectorAll('#guestTableBody tr').forEach(function(tr){
          var name=(tr.querySelector('.fw-bold')?.textContent||'').toLowerCase();
          var phone=(tr.querySelector('td:nth-child(2)')?.textContent||'').toLowerCase();
          tr.style.display = (name.indexOf(q)>=0 || phone.indexOf(q)>=0) ? '' : 'none';
        });
      });
    }
    document.querySelectorAll('.guest-toggle').forEach(function(chk){
      chk.addEventListener('change',function(){
        var gid=chk.getAttribute('data-guest');var bid=chk.getAttribute('data-booking');
        var fd=new FormData();fd.append('csrf_token','<?= htmlspecialchars(csrf_token()) ?>');fd.append('booking_id',bid);fd.append('guest_id',gid);fd.append('checked', chk.checked ? 1 : 0);fd.append('ajax','1');
        fetch('<?= BASE_URL ?>?r=booking_guest_checkin',{method:'POST',body:fd}).then(function(r){return r.json()}).then(function(data){
          if(!data||!data.success)return;showToast();
          var tr=chk.closest('tr');if(!tr)return;var badge=tr.querySelector('.status-badge');
          var ns=data.is_no_show===1;var isChecked=data.is_checked_in===1 && !ns;badge.className='status-badge '+(ns?'noshow':(isChecked?'arrived':'pending'));badge.textContent= ns ? 'Vắng mặt' : (isChecked?'Đã đến':'Chờ đến');
          var stC=document.getElementById('st-checked');var stN=document.getElementById('st-noshow');var stT=document.getElementById('st-total');var stPct=document.getElementById('st-pct');var stBar=document.getElementById('st-bar');
          if(stC&&stN&&stT&&data.stats){stC.textContent=data.stats.checked;stN.textContent=data.stats.noshow;stT.textContent=data.stats.total;var pct=(data.stats.total>0)?Math.round(data.stats.checked*100/data.stats.total):0;if(stPct)stPct.textContent=pct+'%';if(stBar)stBar.style.width=pct+'%';}
        }).catch(function(){});
      });
    });
    document.querySelectorAll('.noshow-toggle').forEach(function(btn){
      btn.addEventListener('click',function(){
        var gid=btn.getAttribute('data-guest');var flag=parseInt(btn.getAttribute('data-flag'))||1;var fd=new FormData();
        fd.append('csrf_token','<?= htmlspecialchars(csrf_token()) ?>');fd.append('booking_id','<?= (int)$booking['id'] ?>');fd.append('guest_id',gid);fd.append('no_show',flag);fd.append('ajax','1');
        fetch('<?= BASE_URL ?>?r=booking_guest_noshow',{method:'POST',body:fd}).then(function(r){return r.json()}).then(function(data){
          if(!data||!data.success)return;showToast();
          var tr=btn.closest('tr');if(!tr)return;var badge=tr.querySelector('.status-badge');
          var ns=data.is_no_show===1;var isChecked=data.is_checked_in===1 && !ns;badge.className='status-badge '+(ns?'noshow':(isChecked?'arrived':'pending'));badge.textContent= ns ? 'Vắng mặt' : (isChecked?'Đã đến':'Chờ đến');
          var switchEl=tr.querySelector('.guest-toggle');if(switchEl){switchEl.checked = !ns && (data.is_checked_in===1);} 
          var stC=document.getElementById('st-checked');var stN=document.getElementById('st-noshow');var stT=document.getElementById('st-total');var stPct=document.getElementById('st-pct');var stBar=document.getElementById('st-bar');
          if(stC&&stN&&stT&&data.stats){stC.textContent=data.stats.checked;stN.textContent=data.stats.noshow;stT.textContent=data.stats.total;var pct=(data.stats.total>0)?Math.round(data.stats.checked*100/data.stats.total):0;if(stPct)stPct.textContent=pct+'%';if(stBar)stBar.style.width=pct+'%';}
        }).catch(function(){});
      });
    });
  })();
</script>
</body>
</html>
