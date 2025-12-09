<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Khách theo tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
</head>
<body>
<?php $current_page='guide_login'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
<div class="main-content">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Danh sách khách theo tour</h3>
    <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=guide_portal">Quay lại</a>
  </div>

  <?php
    $stats = ['total'=>0,'arrived'=>0,'pending'=>0,'noshow'=>0];
    foreach(($guests ?? []) as $gx){
      $stats['total']++;
      if (!empty($gx['is_no_show'])) $stats['noshow']++; else if (!empty($gx['is_checked_in'])) $stats['arrived']++; else $stats['pending']++;
    }
  ?>
  <div class="card mb-3">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
      <div class="badge bg-info">Tổng: <span id="st-total"><?= (int)$stats['total'] ?></span></div>
      <div class="badge bg-success">Đã đến: <span id="st-arrived"><?= (int)$stats['arrived'] ?></span></div>
      <div class="badge bg-secondary">Chưa đến: <span id="st-pending"><?= (int)$stats['pending'] ?></span></div>
      <div class="badge bg-danger">Vắng: <span id="st-noshow"><?= (int)$stats['noshow'] ?></span></div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">Khách đoàn</div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped">
          <thead class="table-light">
            <tr>
              <th>Booking</th>
              <th>Họ tên</th>
              <th>Giới tính</th>
              <th>Năm sinh</th>
              <th>Giấy tờ</th>
              <th>Ghi chú</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach(($guests ?? []) as $g): ?>
              <?php $gid = (int)($g['id'] ?? 0); $state = !empty($g['is_no_show']) ? 'noshow' : (!empty($g['is_checked_in']) ? 'arrived' : 'pending'); ?>
              <tr data-guest="<?= $gid ?>" data-booking="<?= (int)($g['booking_id'] ?? 0) ?>" data-state="<?= $state ?>">
                <td>BK<?= (int)($g['booking_id'] ?? 0) ?></td>
                <td><?= htmlspecialchars($g['full_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($g['gender'] ?? '') ?></td>
                <td><?= htmlspecialchars(!empty($g['dob']) ? date('Y', strtotime($g['dob'])) : '') ?></td>
                <td><?= htmlspecialchars($g['id_document_no'] ?? '') ?></td>
                <td>
                  <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($g['notes'] ?? '') ?>" data-guest="<?= (int)($g['id'] ?? 0) ?>" placeholder="Nhập ghi chú...">
                </td>
                <td>
                  <span class="badge <?= $state==='arrived'?'bg-success':($state==='noshow'?'bg-danger':'bg-secondary') ?> me-2" data-status-label><?= $state==='arrived'?'Đã đến':($state==='noshow'?'Vắng':'Chờ') ?></span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-secondary seg-btn" data-status="pending" data-guest="<?= $gid ?>" data-booking="<?= (int)($g['booking_id'] ?? 0) ?>">Chờ</button>
                    <button class="btn btn-sm btn-outline-success seg-btn" data-status="arrived" data-guest="<?= $gid ?>" data-booking="<?= (int)($g['booking_id'] ?? 0) ?>">Đã đến</button>
                    <button class="btn btn-sm btn-outline-danger seg-btn" data-status="noshow" data-guest="<?= $gid ?>" data-booking="<?= (int)($g['booking_id'] ?? 0) ?>">Vắng</button>
                    <button class="btn btn-sm btn-outline-primary" data-save-note data-guest="<?= $gid ?>">Lưu ghi chú</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
  <script>
    const CSRF_TOKEN = '<?= htmlspecialchars(csrf_token()) ?>';
    function post(url, data){ data = Object.assign({}, data, { csrf_token: CSRF_TOKEN }); return fetch(url,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams(data)}).then(r=>r.text()); }
    function setStatus(guestId, bookingId, status){
      var row = document.querySelector('tr[data-guest="'+guestId+'"]'); if(!row) return;
      var p1, p2;
      if(status==='arrived'){ p1=post('<?= BASE_URL ?>?r=booking_guest_checkin',{booking_id:bookingId,guest_id:guestId,checked:1,stage:'gather'}); p2=post('<?= BASE_URL ?>?r=booking_guest_noshow',{booking_id:bookingId,guest_id:guestId,no_show:0}); }
      else if(status==='noshow'){ p1=post('<?= BASE_URL ?>?r=booking_guest_checkin',{booking_id:bookingId,guest_id:guestId,checked:0}); p2=post('<?= BASE_URL ?>?r=booking_guest_noshow',{booking_id:bookingId,guest_id:guestId,no_show:1}); }
      else { p1=post('<?= BASE_URL ?>?r=booking_guest_checkin',{booking_id:bookingId,guest_id:guestId,checked:0}); p2=post('<?= BASE_URL ?>?r=booking_guest_noshow',{booking_id:bookingId,guest_id:guestId,no_show:0}); }
      Promise.all([p1,p2]).then(function(){
        row.dataset.state=status;
        var label=row.querySelector('[data-status-label]');
        if(label){ if(status==='arrived'){ label.className='badge bg-success me-2'; label.textContent='Đã đến'; } else if(status==='noshow'){ label.className='badge bg-danger me-2'; label.textContent='Vắng'; } else { label.className='badge bg-secondary me-2'; label.textContent='Chờ'; } }
        recalcStats();
      }).catch(function(){});
    }
    function recalcStats(){
      var rows=document.querySelectorAll('tbody tr[data-state]');
      var total=0,arrived=0,pending=0,noshow=0;
      rows.forEach(function(r){ total++; var s=r.dataset.state; if(s==='arrived') arrived++; else if(s==='noshow') noshow++; else pending++; });
      document.getElementById('st-total').textContent=total; document.getElementById('st-arrived').textContent=arrived; document.getElementById('st-pending').textContent=pending; document.getElementById('st-noshow').textContent=noshow;
    }
    document.addEventListener('DOMContentLoaded', function(){
      document.querySelectorAll('.seg-btn').forEach(function(btn){ btn.addEventListener('click', function(){ var gid=this.dataset.guest; var bid=this.dataset.booking; var st=this.dataset.status; setStatus(gid,bid,st); }); });
      document.querySelectorAll('[data-save-note]').forEach(function(btn){ btn.addEventListener('click', function(){ var gid=this.getAttribute('data-guest'); var input=document.querySelector('input[data-guest="'+gid+'"]'); var note=input? (input.value||'') : ''; fetch('<?= BASE_URL ?>?r=guide_portal_update_guest_note', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams({guest_id:gid,notes:note}) }).then(function(r){ return r.text(); }).then(function(txt){ if(txt==='ok'){ btn.classList.remove('btn-outline-primary'); btn.classList.add('btn-success'); btn.textContent='✓'; setTimeout(function(){ btn.classList.add('btn-outline-primary'); btn.classList.remove('btn-success'); btn.textContent='Lưu ghi chú'; }, 1200); } }); }); });
    });
  </script>
</body>
</html>
