<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Danh sách đoàn & Check‑in</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
  <style>
    .sticky-bar{position:sticky;top:60px;z-index:1100;background:#fff;border-bottom:1px solid #e5e7eb}
    .group-card{border:1px solid #e5e7eb;border-radius:12px;padding:12px;background:#fff}
    .guest-row{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:10px;border:1px solid #e5e7eb;border-radius:10px;background:#f8fafc;margin-bottom:8px}
    .guest-left{display:flex;align-items:center;gap:10px;flex:1}
    .seg{display:flex;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}
    .seg .btn{border:0;border-right:1px solid #e5e7eb; transition: all .15s ease-in-out}
    .seg .btn:last-child{border-right:0}
    .seg .btn.pending{background:#f1f5f9;color:#0f172a}
    .seg .btn.arrived{background:#ecfdf5;color:#065f46}
    .seg .btn.noshow{background:#fef2f2;color:#7f1d1d}
    .seg .btn.active{box-shadow: 0 0 0 2px rgba(13,110,253,.25) inset}
    .seg .btn.arrived.active{background:#198754;color:#fff}
    .seg .btn.noshow.active{background:#dc3545;color:#fff}
    .seg .btn.pending.active{background:#6c757d;color:#fff}
    .seg .btn.loading{opacity:.7; pointer-events:none}
    .badge-note{background:#fff7ed;color:#9a3412}
    .guest-row.updated{animation: flash .6s}
    @keyframes flash{0%{background:#eef2ff}100%{background:#f8fafc}}
    @media (max-width:576px){.guest-row{flex-direction:column;align-items:flex-start}}
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <?php require_once __DIR__ . '/../../assets/configs/db.php'; ?>
  <?php require_once __DIR__ . '/../../models/ScheduleModel.php'; ?>
  <?php $current_page='booking'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>

  <?php
    $departureId = (int)($_GET['departure_id'] ?? 0);
    $pdo = DB::get();
    $scheduleModel = new ScheduleModel();
    $schedule = $departureId ? $scheduleModel->find($departureId) : null;

    $rows = [];
    if ($departureId > 0) {
      $sql = "SELECT bg.*, b.id AS booking_id, t.title AS tour_title, u.full_name AS leader_name, u.phone AS leader_phone
              FROM booking_guests bg
              JOIN bookings b ON bg.booking_id = b.id
              LEFT JOIN tours t ON b.tour_id = t.id
              LEFT JOIN users u ON b.customer_user_id = u.id
              WHERE b.schedule_id = :dep AND b.booking_status IN ('deposit','completed')
              ORDER BY b.id, bg.id";
      $st = $pdo->prepare($sql);
      $st->execute(['dep'=>$departureId]);
      $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    }

    $groups = [];
    $stats = ['total'=>0,'arrived'=>0,'pending'=>0,'noshow'=>0];
    foreach ($rows as $r) {
      $bid = (int)$r['booking_id'];
      if (empty($groups[$bid])) {
        $groups[$bid] = [
          'booking_id'=>$bid,
          'leader_name'=>$r['leader_name'] ?? '',
          'leader_phone'=>$r['leader_phone'] ?? '',
          'tour_title'=>$r['tour_title'] ?? '',
          'guests'=>[]
        ];
      }
      $groups[$bid]['guests'][] = $r;
      $stats['total']++;
      if (!empty($r['is_no_show'])) $stats['noshow']++; else if (!empty($r['is_checked_in'])) $stats['arrived']++; else $stats['pending']++;
    }

    $role = $_SESSION['role'] ?? '';
    $isLocked = false;
    if (!empty($schedule['start_date'])) {
      $timeStr = '08:00:00';
      try {
        $qT = $pdo->query("SHOW TABLES LIKE 'tour_itinerary_items'");
        if ($qT && $qT->rowCount() > 0 && !empty($schedule['tour_id'])) {
          $qStart = $pdo->query("SHOW COLUMNS FROM `tour_itinerary_items` LIKE 'start_time'");
          $qAct = $pdo->query("SHOW COLUMNS FROM `tour_itinerary_items` LIKE 'activity_time'");
          if ($qStart && $qStart->rowCount() > 0) {
            $stMin = $pdo->prepare("SELECT MIN(start_time) AS t FROM tour_itinerary_items WHERE tour_id = ? AND day_number = 1 AND start_time IS NOT NULL");
            $stMin->execute([(int)$schedule['tour_id']]);
            $rowMin = $stMin->fetch(PDO::FETCH_ASSOC);
            if (!empty($rowMin['t'])) { $timeStr = $rowMin['t']; }
          } elseif ($qAct && $qAct->rowCount() > 0) {
            $stMin2 = $pdo->prepare("SELECT MIN(activity_time) AS t FROM tour_itinerary_items WHERE tour_id = ? AND day_number = 1 AND activity_time IS NOT NULL");
            $stMin2->execute([(int)$schedule['tour_id']]);
            $rowMin2 = $stMin2->fetch(PDO::FETCH_ASSOC);
            if (!empty($rowMin2['t'])) { $timeStr = $rowMin2['t']; }
          }
        }
      } catch (Throwable $e) {}
      $cutoff = strtotime($schedule['start_date'] . ' ' . $timeStr);
      if ($cutoff && time() >= $cutoff && $role === 'guide') { $isLocked = true; }
    }
  ?>

  <div class="main-content">
    <div class="sticky-bar">
      <div class="container-fluid py-2">
        <div class="d-flex flex-wrap align-items-center gap-2">
          <div><strong>Chuyến:</strong> #<?= $departureId ?> <?= htmlspecialchars($schedule['tour_title'] ?? '') ?> <?= !empty($schedule['start_date']) ? '(' . date('d/m/Y', strtotime($schedule['start_date'])) . ')' : '' ?></div>
          <div class="badge bg-info">Tổng: <span id="st-total"><?= (int)$stats['total'] ?></span></div>
          <div class="badge bg-success">Đã đến: <span id="st-arrived"><?= (int)$stats['arrived'] ?></span></div>
          <div class="badge bg-secondary">Chưa đến: <span id="st-pending"><?= (int)$stats['pending'] ?></span></div>
          <div class="badge bg-danger">Vắng mặt: <span id="st-noshow"><?= (int)$stats['noshow'] ?></span></div>
          <div class="ms-auto d-flex gap-2">
            <a href="<?= BASE_URL ?>?r=schedules&tour_id=<?= (int)($schedule['tour_id'] ?? 0) ?>" class="btn btn-sm btn-outline-secondary">Lịch</a>
            <a href="<?= BASE_URL ?>?r=booking" class="btn btn-sm btn-outline-secondary">Danh sách Booking</a>
            <a href="<?= BASE_URL ?>?r=tour_manifest_export&type=csv&departure_id=<?= (int)$departureId ?>" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-file-csv me-1"></i> Xuất CSV</a>
            <a href="<?= BASE_URL ?>?r=tour_manifest_export&type=print&departure_id=<?= (int)$departureId ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-print me-1"></i> In</a>
            <button id="btn-checkin-all" class="btn btn-sm btn-success"><i class="fa-solid fa-user-check me-1"></i> Check‑in cả đoàn</button>
            <button id="btn-pending-all" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-clock me-1"></i> Chờ cả đoàn</button>
            <button id="btn-noshow-all" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-user-xmark me-1"></i> Vắng mặt cả đoàn</button>
            <a href="<?= BASE_URL ?>?r=qr_scan&departure_id=<?= (int)$departureId ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-qrcode me-1"></i> Quét QR</a>
          </div>
        </div>
        <div id="status-alert" class="alert alert-warning py-2 px-3 mb-0 d-flex align-items-center gap-3 d-none">
          <div><i class="fa-solid fa-triangle-exclamation me-1"></i> Còn khách <strong>Chờ</strong>: <span id="al-pending"><?= (int)$stats['pending'] ?></span> · <strong>Vắng</strong>: <span id="al-noshow"><?= (int)$stats['noshow'] ?></span></div>
          <div class="ms-auto d-flex gap-2">
            <button id="al-filter-pending" class="btn btn-sm btn-outline-secondary">Lọc Chờ</button>
            <button id="al-filter-noshow" class="btn btn-sm btn-outline-danger">Lọc Vắng</button>
            <button id="al-filter-reset" class="btn btn-sm btn-outline-primary">Hiện tất cả</button>
          </div>
        </div>
      </div>
  </div>

  <!-- Bộ lọc & tìm kiếm -->
  <div class="container-fluid mt-3">
    <div class="card shadow-sm">
      <div class="card-body d-flex flex-wrap gap-2 align-items-center">
        <div class="input-group" style="max-width:380px">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input id="filter-q" type="text" class="form-control" placeholder="Tìm tên/ghi chú/CMND">
        </div>
        <div class="btn-group" role="group" aria-label="Bộ lọc trạng thái">
          <button type="button" class="btn btn-outline-secondary filter-btn" data-state="all">Tất cả</button>
          <button type="button" class="btn btn-outline-secondary filter-btn" data-state="pending">Chờ</button>
          <button type="button" class="btn btn-outline-secondary filter-btn" data-state="arrived">Đã đến</button>
          <button type="button" class="btn btn-outline-secondary filter-btn" data-state="noshow">Vắng</button>
        </div>
      </div>
    </div>
  </div>

  <?php if ($departureId <= 0): ?>
      <div class="alert alert-warning mt-3">Thiếu departure_id</div>
    <?php elseif (empty($groups)): ?>
      <div class="alert alert-info mt-3">Không có khách cho lịch này</div>
    <?php else: ?>
      <div class="mt-3">
        <?php foreach ($groups as $g): ?>
          <div class="group-card mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div><strong>Booking #<?= (int)$g['booking_id'] ?></strong> — <?= htmlspecialchars($g['leader_name'] ?: 'Leader') ?></div>
              <?php if (!empty($g['leader_phone'])): ?>
                <a class="btn btn-sm btn-outline-primary" href="tel:<?= htmlspecialchars($g['leader_phone']) ?>"><i class="fa-solid fa-phone"></i></a>
              <?php endif; ?>
            </div>
            <div class="small text-muted mb-2"><?= htmlspecialchars($g['tour_title']) ?></div>

            <?php foreach ($g['guests'] as $guest): ?>
              <?php
                $gid = (int)$guest['id'];
                $state = !empty($guest['is_no_show']) ? 'noshow' : (!empty($guest['is_checked_in']) ? 'arrived' : 'pending');
                $note = trim($guest['notes'] ?? '');
              ?>
              <div class="guest-row" data-guest="<?= $gid ?>" data-booking="<?= (int)$g['booking_id'] ?>" data-state="<?= $state ?>">
                <div class="guest-left">
                  <div class="fw-semibold"><?= htmlspecialchars($guest['full_name'] ?? 'Khách') ?></div>
                  <?php
                    $checkinLink = BASE_URL . '?r=booking_guest_checkin&booking_id=' . (int)$g['booking_id'] . '&guest_id=' . (int)$gid . '&checked=1&stage=gather&schedule_id=' . (int)$departureId;
                    $qrUrlSmall = BASE_URL . '?r=qr&size=64&data=' . urlencode($checkinLink);
                    $qrUrlLarge = BASE_URL . '?r=qr&size=256&data=' . urlencode($checkinLink);
                  ?>
                  <a href="<?= $qrUrlLarge ?>" target="_blank" title="Mở QR lớn">
                    <img src="<?= $qrUrlSmall ?>" alt="QR" style="width:24px;height:24px">
                  </a>
                  <button type="button" class="btn btn-sm btn-outline-secondary btn-copy-link" data-link="<?= htmlspecialchars($checkinLink) ?>" title="Sao chép link">
                    <i class="fa-regular fa-copy"></i>
                  </button>
                  <?php if (!empty($g['leader_phone'])): ?>
                    <a class="btn btn-sm btn-outline-primary" href="tel:<?= htmlspecialchars($g['leader_phone']) ?>"><i class="fa-solid fa-phone"></i></a>
                  <?php endif; ?>
                  <?php if ($note !== ''): ?>
                    <span class="badge badge-note"><?= htmlspecialchars($note) ?></span>
                  <?php endif; ?>
                </div>
                <div class="seg">
                  <button class="btn btn-sm seg-btn pending <?= $state==='pending'?'active':'' ?>" data-status="pending" data-guest="<?= $gid ?>" data-booking="<?= (int)$g['booking_id'] ?>">Chờ</button>
                  <button class="btn btn-sm seg-btn arrived <?= $state==='arrived'?'active':'' ?>" data-status="arrived" data-guest="<?= $gid ?>" data-booking="<?= (int)$g['booking_id'] ?>">Đã đến</button>
                  <button class="btn btn-sm seg-btn noshow <?= $state==='noshow'?'active':'' ?>" data-status="noshow" data-guest="<?= $gid ?>" data-booking="<?= (int)$g['booking_id'] ?>">Vắng</button>
                  <button class="btn btn-sm btn-outline-info history-btn" data-guest="<?= $gid ?>"><i class="fa-regular fa-clock"></i></button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <script>
  const CSRF = '<?= htmlspecialchars(csrf_token()) ?>';
  const ROLE = '<?= htmlspecialchars($role) ?>';
  const LOCKED = <?= $isLocked ? 'true' : 'false' ?>;
  function showToast(msg, type){
    var t = document.getElementById('toast');
    if(!t){
      t = document.createElement('div');
      t.id='toast';
      t.style.position='fixed'; t.style.top='70px'; t.style.right='20px'; t.style.zIndex='2000';
      t.style.padding='10px 12px'; t.style.borderRadius='8px'; t.style.boxShadow='0 6px 20px rgba(0,0,0,.15)';
      document.body.appendChild(t);
    }
    t.className = type==='error' ? 'bg-danger text-white' : 'bg-success text-white';
    t.textContent = msg; t.style.display='block';
    setTimeout(function(){ t.style.display='none'; }, 1500);
  }
    document.addEventListener('click', function(e){
      var t = e.target.closest('.btn-copy-link');
      if (!t) return;
      var link = t.getAttribute('data-link') || '';
      if (!link) return;
      try { navigator.clipboard.writeText(link).then(function(){ showToast('Đã sao chép link', 'success'); }); }
      catch(_){
        var ta = document.createElement('textarea'); ta.value = link; document.body.appendChild(ta); ta.select(); try{ document.execCommand('copy'); showToast('Đã sao chép link', 'success'); } catch(e){}; document.body.removeChild(ta);
      }
    });
    function post(url, data) {
      return fetch(url, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams(data)
      }).then(r => r.json());
    }
    function setStatus(guestId, bookingId, status, el, extra) {
      const row = document.querySelector('.guest-row[data-guest="'+guestId+'"]');
      if (!row) return;
      const seg = row.querySelectorAll('.seg-btn');
      seg.forEach(b => b.classList.remove('active'));
      row.dataset.state = status;
      const targetBtn = row.querySelector('.seg-btn.'+status);
      if (targetBtn) { targetBtn.classList.add('active'); }
      row.classList.add('updated');
      recalcStats();
      if (el){ el.classList.add('loading'); el.disabled = true; }
      let p1, p2;
      const reason = extra?.reason || '';
      const note = extra?.note || '';
      if (status === 'arrived') {
        p1 = post('<?= BASE_URL ?>?r=booking_guest_checkin', {ajax:1, csrf_token: CSRF, booking_id: bookingId, guest_id: guestId, checked: 1, stage: 'gather', status: 'arrived', reason: reason, note: note});
        p2 = post('<?= BASE_URL ?>?r=booking_guest_noshow', {ajax:1, csrf_token: CSRF, booking_id: bookingId, guest_id: guestId, no_show: 0, status: 'arrived'});
      } else if (status === 'noshow') {
        p1 = post('<?= BASE_URL ?>?r=booking_guest_checkin', {ajax:1, csrf_token: CSRF, booking_id: bookingId, guest_id: guestId, checked: 0, status: 'noshow'});
        p2 = post('<?= BASE_URL ?>?r=booking_guest_noshow', {ajax:1, csrf_token: CSRF, booking_id: bookingId, guest_id: guestId, no_show: 1, status: 'noshow', reason: reason, note: note});
      } else {
        p1 = post('<?= BASE_URL ?>?r=booking_guest_checkin', {ajax:1, csrf_token: CSRF, booking_id: bookingId, guest_id: guestId, checked: 0, status: 'pending', reason: reason, note: note});
        p2 = post('<?= BASE_URL ?>?r=booking_guest_noshow', {ajax:1, csrf_token: CSRF, booking_id: bookingId, guest_id: guestId, no_show: 0, status: 'pending'});
      }
      Promise.all([p1, p2]).then(() => {
        if (el){ el.classList.remove('loading'); el.disabled = false; }
      }).catch(() => {
        if (el){ el.classList.remove('loading'); el.disabled = false; }
      });
    }
    function recalcStats() {
      const rows = document.querySelectorAll('.guest-row');
      let total = 0, arrived = 0, pending = 0, noshow = 0;
      rows.forEach(r => {
        total++;
        const s = r.dataset.state;
        if (s === 'arrived') arrived++;
        else if (s === 'noshow') noshow++;
        else pending++;
      });
      document.getElementById('st-total').textContent = total;
      document.getElementById('st-arrived').textContent = arrived;
      document.getElementById('st-pending').textContent = pending;
      document.getElementById('st-noshow').textContent = noshow;
      updateAlert();
    }
    function updateAlert(){
      const p = parseInt(document.getElementById('st-pending').textContent||'0',10);
      const n = parseInt(document.getElementById('st-noshow').textContent||'0',10);
      const box = document.getElementById('status-alert');
      if (!box) return;
      if (p>0 || n>0){
        box.classList.remove('d-none');
        box.querySelector('#al-pending').textContent = p;
        box.querySelector('#al-noshow').textContent = n;
      } else {
        box.classList.add('d-none');
      }
    }
    function applyFilters(){
      const q = (document.getElementById('filter-q').value || '').toLowerCase();
      const activeBtn = document.querySelector('.filter-btn.active');
      const want = activeBtn ? activeBtn.dataset.state : 'all';
      document.querySelectorAll('.guest-row').forEach(r => {
        const name = r.querySelector('.guest-left .fw-semibold')?.textContent.toLowerCase() || '';
        const badge = r.querySelector('.badge-note')?.textContent.toLowerCase() || '';
        const state = r.dataset.state;
        const matchText = !q || name.includes(q) || badge.includes(q);
        const matchState = (want==='all') || (state===want);
        r.style.display = (matchText && matchState) ? '' : 'none';
      });
    }
    document.addEventListener('DOMContentLoaded', function() {
      // filters
      document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function(){
          document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
          this.classList.add('active');
          applyFilters();
        });
      });
      document.getElementById('filter-q').addEventListener('input', applyFilters);
      document.getElementById('al-filter-pending')?.addEventListener('click', function(){ document.querySelector('.filter-btn[data-state="pending"]').click(); });
      document.getElementById('al-filter-noshow')?.addEventListener('click', function(){ document.querySelector('.filter-btn[data-state="noshow"]').click(); });
      document.getElementById('al-filter-reset')?.addEventListener('click', function(){ document.querySelector('.filter-btn[data-state="all"]').click(); });
      updateAlert();
      document.querySelectorAll('.seg-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          if (LOCKED && ROLE==='guide') { showToast('Đã quá giờ xuất phát. Liên hệ Quản lý.', 'error'); return; }
          const guestId = this.dataset.guest;
          const bookingId = this.dataset.booking;
          const status = this.dataset.status;
          if (status==='arrived') { setStatus(guestId, bookingId, status, this); showToast('Đã cập nhật', 'success'); return; }
          openReasonModal(guestId, bookingId, status, this);
        });
      });
      document.querySelectorAll('.history-btn').forEach(btn => {
        btn.addEventListener('click', function(){
          const guestId = this.dataset.guest;
          openHistoryModal(guestId);
        });
      });
      // Group actions
      const dep = <?= (int)$departureId ?>;
      const btnAll = document.getElementById('btn-checkin-all');
      const btnNoShow = document.getElementById('btn-noshow-all');
      if (btnAll) {
        btnAll.addEventListener('click', function(){
          if (LOCKED && ROLE==='guide') { showToast('Đã quá giờ xuất phát. Liên hệ Quản lý.', 'error'); return; }
          btnAll.classList.add('loading'); btnAll.disabled = true;
          post('<?= BASE_URL ?>?r=departure_group_checkin', {ajax:1, csrf_token: CSRF, departure_id: dep, checked: 1, status:'arrived'})
            .then(function(res){ if(!res||!res.success){ showToast('Không thể cập nhật', 'error'); return; } document.querySelectorAll('.guest-row').forEach(function(r){ r.dataset.state='arrived'; r.classList.add('updated'); r.querySelectorAll('.seg-btn').forEach(function(b){ b.classList.remove('active'); }); var btn=r.querySelector('.seg-btn.arrived'); if(btn) btn.classList.add('active'); }); recalcStats(); showToast('Đã check‑in cả đoàn', 'success'); })
            .finally(function(){ btnAll.classList.remove('loading'); btnAll.disabled = false; });
        });
      }
      const btnPending = document.getElementById('btn-pending-all');
      if (btnPending) {
        btnPending.addEventListener('click', function(){
          if (LOCKED && ROLE==='guide') { showToast('Đã quá giờ xuất phát. Liên hệ Quản lý.', 'error'); return; }
          openReasonModalGroup('pending');
        });
      }
      if (btnNoShow) {
        btnNoShow.addEventListener('click', function(){
          if (LOCKED && ROLE==='guide') { showToast('Đã quá giờ xuất phát. Liên hệ Quản lý.', 'error'); return; }
          openReasonModalGroup('noshow');
        });
      }
      var qrBtn = document.querySelector('a[href*="?r=qr_scan"]');
      if (qrBtn) {
        qrBtn.addEventListener('click', function(e){ if (LOCKED && ROLE==='guide') { e.preventDefault(); showToast('Đã quá giờ xuất phát. Liên hệ Quản lý.', 'error'); } });
      }
      // Reason modal
      const MODAL_HTML = `
      <div id="reason-modal" class="modal" style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.35)">
        <div class="card" style="max-width:480px;margin:10% auto 0;background:#fff;border-radius:12px">
          <div class="card-header bg-white"><strong>Lý do cập nhật trạng thái</strong></div>
          <div class="card-body">
            <div class="mb-2">
              <label class="form-label">Lý do</label>
              <select id="reason-select" class="form-select">
                <option value="">— Chọn —</option>
                <option value="late">Đến trễ</option>
                <option value="no_contact">Không liên lạc được</option>
                <option value="cancel_self">Tự hủy</option>
                <option value="other">Khác</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Ghi chú</label>
              <textarea id="reason-note" class="form-control" rows="2" placeholder="Nhập ghi chú"></textarea>
            </div>
            <div class="d-flex justify-content-end gap-2">
              <button id="reason-cancel" class="btn btn-outline-secondary">Hủy</button>
              <button id="reason-ok" class="btn btn-primary">Cập nhật</button>
            </div>
          </div>
        </div>
      </div>`;
      function ensureModal(){ if (!document.getElementById('reason-modal')) { const d=document.createElement('div'); d.innerHTML=MODAL_HTML; document.body.appendChild(d.firstElementChild); } }
      function openReasonModal(guestId, bookingId, status, el){ ensureModal(); const m=document.getElementById('reason-modal'); const sel=m.querySelector('#reason-select'); const note=m.querySelector('#reason-note'); const ok=m.querySelector('#reason-ok'); const cancel=m.querySelector('#reason-cancel'); sel.value=''; note.value=''; m.style.display='block'; function close(){ m.style.display='none'; ok.removeEventListener('click', submit); cancel.removeEventListener('click', onCancel); }
        function submit(){ const r=sel.value; const n=note.value.trim(); close(); setStatus(guestId, bookingId, status, el, {reason:r, note:n}); showToast('Đã cập nhật', 'success'); }
        function onCancel(){ close(); }
        ok.addEventListener('click', submit); cancel.addEventListener('click', onCancel);
      }
      function openHistoryModal(guestId){
        if (!document.getElementById('history-modal')){
          const wrap=document.createElement('div');
          wrap.id='history-modal'; wrap.style.position='fixed'; wrap.style.inset='0'; wrap.style.zIndex='2000'; wrap.style.background='rgba(0,0,0,.35)'; wrap.style.display='none';
          wrap.innerHTML = '<div class="card" style="max-width:560px;margin:8% auto 0"><div class="card-header bg-white d-flex justify-content-between align-items-center"><strong>Lịch sử trạng thái</strong><button id="hist-close" class="btn btn-sm btn-outline-secondary">Đóng</button></div><div class="card-body"><ul id="hist-list" class="list-group"></ul></div></div>';
          document.body.appendChild(wrap);
        }
        const modal=document.getElementById('history-modal'); const list=modal.querySelector('#hist-list'); list.innerHTML=''; modal.style.display='block';
        fetch('<?= BASE_URL ?>?r=guest_checkin_history&guest_id='+guestId).then(r=>r.json()).then(function(data){
          if (!data || !data.success) { list.innerHTML='<li class="list-group-item">Không có dữ liệu</li>'; return; }
          const rows = data.logs || []; if (rows.length===0){ list.innerHTML='<li class="list-group-item">Chưa có lịch sử</li>'; return; }
          rows.forEach(function(x){
            var t = document.createElement('li'); t.className='list-group-item d-flex justify-content-between align-items-center';
            var left = document.createElement('div'); left.innerHTML = '<div><strong>'+ (x.status||'') +'</strong> ' + (x.stage||'') + '</div><div class="text-muted small">'+ (x.reason||'') + (x.note?(' — '+x.note):'') +'</div>';
            var right = document.createElement('div'); right.className='text-muted small'; right.textContent = x.checked_at || '';
            t.appendChild(left); t.appendChild(right); list.appendChild(t);
          });
        }).catch(function(){ list.innerHTML='<li class="list-group-item">Không tải được dữ liệu</li>'; });
        modal.querySelector('#hist-close').onclick=function(){ modal.style.display='none'; };
      }
      function openReasonModalGroup(target){
        ensureModal(); const m=document.getElementById('reason-modal'); const sel=m.querySelector('#reason-select'); const note=m.querySelector('#reason-note'); const ok=m.querySelector('#reason-ok'); const cancel=m.querySelector('#reason-cancel'); sel.value=''; note.value=''; m.style.display='block';
        function close(){ m.style.display='none'; ok.removeEventListener('click', submit); cancel.removeEventListener('click', onCancel); }
        function submit(){ const r=sel.value; const n=note.value.trim(); close();
          if (target==='pending'){
            btnPending.classList.add('loading'); btnPending.disabled = true;
            post('<?= BASE_URL ?>?r=departure_group_pending', {ajax:1, csrf_token: CSRF, departure_id: dep, status:'pending', reason:r, note:n})
              .then(function(res){ if(!res||!res.success){ showToast('Không thể cập nhật', 'error'); return; } document.querySelectorAll('.guest-row').forEach(function(rw){ rw.dataset.state='pending'; rw.classList.add('updated'); rw.querySelectorAll('.seg-btn').forEach(function(b){ b.classList.remove('active'); }); var btn=rw.querySelector('.seg-btn.pending'); if(btn) btn.classList.add('active'); }); recalcStats(); showToast('Đã đặt trạng thái Chờ', 'success'); })
              .finally(function(){ btnPending.classList.remove('loading'); btnPending.disabled = false; });
          } else {
            btnNoShow.classList.add('loading'); btnNoShow.disabled = true;
            post('<?= BASE_URL ?>?r=departure_group_noshow', {ajax:1, csrf_token: CSRF, departure_id: dep, no_show: 1, status:'noshow', reason:r, note:n})
              .then(function(res){ if(!res||!res.success){ showToast('Không thể cập nhật', 'error'); return; } document.querySelectorAll('.guest-row').forEach(function(rw){ rw.dataset.state='noshow'; rw.classList.add('updated'); rw.querySelectorAll('.seg-btn').forEach(function(b){ b.classList.remove('active'); }); var btn=rw.querySelector('.seg-btn.noshow'); if(btn) btn.classList.add('active'); }); recalcStats(); showToast('Đã vắng mặt cả đoàn', 'success'); })
              .finally(function(){ btnNoShow.classList.remove('loading'); btnNoShow.disabled = false; });
          }
        }
        function onCancel(){ close(); }
        ok.addEventListener('click', submit); cancel.addEventListener('click', onCancel);
      }
    });
  </script>
</body>
</html>
