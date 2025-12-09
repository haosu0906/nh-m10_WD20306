<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Quét QR – Check‑in khách</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
  <style>
    .scanner{display:grid;grid-template-columns:1fr 360px;gap:20px}
    @media (max-width:768px){.scanner{grid-template-columns:1fr}}
    #preview{width:100%;max-height:60vh;background:#000;border-radius:12px}
    .log{max-height:60vh;overflow:auto}
    .toast-lite{position:fixed;top:70px;right:20px;z-index:2000;padding:10px 12px;border-radius:8px;box-shadow:0 6px 20px rgba(0,0,0,.15);display:none}
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <?php require_once __DIR__ . '/../../assets/configs/db.php'; ?>
  <?php $current_page='booking'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>

  <?php $departureId = (int)($_GET['departure_id'] ?? 0); ?>

  <div class="main-content container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="mb-1">Quét QR – Check‑in khách</h4>
        <div class="text-muted">Dùng camera để quét mã QR trên thẻ khách. Lịch: #<?= $departureId ?></div>
      </div>
      <div class="d-flex gap-2">
        <?php if ($departureId>0): ?>
          <a href="<?= BASE_URL ?>?r=tour_manifest&departure_id=<?= (int)$departureId ?>" class="btn btn-outline-secondary">Danh sách đoàn</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body scanner">
        <div>
          <video id="preview" playsinline></video>
          <canvas id="qr-canvas" style="display:none"></canvas>
          <div class="mt-2 d-flex gap-2">
            <button id="btn-start" class="btn btn-success">Bật camera</button>
            <button id="btn-stop" class="btn btn-outline-secondary">Tắt camera</button>
            <button type="button" onclick="simulateScan()" class="btn btn-warning fw-bold"><i class="fas fa-bolt"></i> Demo Quét (Test)</button>
          </div>
        </div>
        <div>
          <div class="mb-2">
            <label class="form-label">Nhập/ dán nội dung QR (dự phòng)</label>
            <div class="input-group">
              <input id="qrInput" type="text" class="form-control" placeholder="Dán link check‑in hoặc mã" />
              <button id="btnSubmit" class="btn btn-primary">Xử lý</button>
            </div>
          </div>
          <div class="mb-2">
            <span id="scan-status" class="badge bg-secondary d-none">Sẵn sàng</span>
          </div>
          <div class="log border rounded p-2">
            <div class="text-muted mb-2">Lịch sử quét</div>
            <ul id="logs" class="list-unstyled mb-0"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="toast" class="toast-lite"></div>

  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
  <script>
    const CSRF = '<?= htmlspecialchars(csrf_token()) ?>';
    const BASE = '<?= rtrim(BASE_URL, '/') ?>';
    const BASE_ORIGIN = (()=>{ try { return new URL(BASE).origin; } catch(_){ return ''; } })();
    let stream = null, detector = null, running = false, cooldownUntil = 0, lastPayload = '';
    let useJsQR = false, canvas = null, ctx = null;
    const preview = document.getElementById('preview');
    const btnStart = document.getElementById('btn-start');
    const btnStop = document.getElementById('btn-stop');
    const logs = document.getElementById('logs');
    const depId = <?= (int)$departureId ?>;

    function showToast(msg, ok){
      const t = document.getElementById('toast');
      t.className = 'toast-lite ' + (ok ? 'bg-success text-white' : 'bg-danger text-white');
      t.textContent = msg; t.style.display='block';
      setTimeout(()=>{t.style.display='none';}, 1500);
    }

    function updateStatusBadge(ok, text){
      const b = document.getElementById('scan-status');
      if (!b) return;
      b.classList.remove('d-none','bg-secondary','bg-success','bg-danger');
      b.classList.add(ok ? 'bg-success' : 'bg-danger');
      b.textContent = text || (ok ? 'Thành công' : 'Thất bại');
    }

    function appendLog(text){
      const li = document.createElement('li');
      li.className = 'border-bottom py-2';
      li.textContent = text;
      try {
        if (logs.firstChild) { logs.insertBefore(li, logs.firstChild); }
        else { logs.appendChild(li); }
      } catch(e) { logs.appendChild(li); }
    }

    function feedback(ok){
      try {
        if (navigator.vibrate) { navigator.vibrate(ok ? 50 : 120); }
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator(); const gain = ctx.createGain();
        osc.type = 'sine'; osc.frequency.value = ok ? 880 : 220; gain.gain.value = 0.05;
        osc.connect(gain).connect(ctx.destination); osc.start();
        setTimeout(()=>{ osc.stop(); ctx.close(); }, ok ? 120 : 200);
      } catch(e) {}
    }

    function post(url, data){
      return fetch(url, {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams(data), credentials: 'same-origin'
      }).then(async r=>{
        const ct = (r.headers.get('content-type') || '').toLowerCase();
        if (ct.includes('application/json')) {
          try { return await r.json(); }
          catch(e){ return { success:false, error:'invalid_json' }; }
        }
        try { const t = await r.text(); return { success:false, raw:t, contentType: ct }; }
        catch(e){ return { success:false, error:'read_failed', message: (e && e.message) ? e.message : String(e) }; }
      });
    }

    async function handlePayload(payload){
      try{
        const now = Date.now();
        if (payload === lastPayload && now < cooldownUntil) { return; }
        lastPayload = payload; cooldownUntil = now + 2000;
        let url;
        try{ url = new URL(payload); } catch(_){ url = null; }
        // Accept same-origin URLs even if path differs; also accept relative check-in strings
        if (!url && /^\??r=/.test(payload)) { try { url = new URL(BASE + (payload.startsWith('?') ? payload : ('?' + payload))); } catch(_){} }
        if (url && (payload.indexOf(BASE) === 0 || url.origin === BASE_ORIGIN)){
          const rParam = url.searchParams.get('r');
          const dataParam = url.searchParams.get('data');
          if (rParam === 'qr' && dataParam) {
            const inner = decodeURIComponent(dataParam);
            await handlePayload(inner);
            return;
          }
          const params = url.searchParams;
          const guestId = parseInt(params.get('guest_id')||'0',10);
          const bookingId = parseInt(params.get('booking_id')||'0',10);
          const checked = parseInt(params.get('checked')||'1',10);
          const stage = params.get('stage') || 'gather';
          const sched = parseInt(params.get('schedule_id')||'0',10);
          if (guestId>0 && bookingId>0){
            const r1 = await post(BASE+'?r=booking_guest_checkin', {ajax:1, csrf_token:CSRF, booking_id:bookingId, guest_id:guestId, checked:checked, stage:stage, schedule_id:sched});
            try { appendLog('resp: ' + JSON.stringify(r1).slice(0,200)); } catch(e) { }
            const ok = !!(r1 && r1.success);
            showToast(ok?'Đã check‑in khách #'+guestId:('Không thể cập nhật'+(r1 && r1.error ? ': '+r1.error : '')), ok);
            updateStatusBadge(ok, ok ? ('Thành công: KH #'+guestId) : 'Thất bại');
            feedback(ok);
            appendLog((ok?'✅ ':'❌ ')+payload + (ok ? '' : (' [' + (r1 && r1.error ? r1.error : (r1 && r1.raw ? r1.raw.substring(0,120) : '')) + ']')));
            return;
          }
        }
        showToast('QR không hợp lệ', false);
        updateStatusBadge(false, 'QR không hợp lệ');
        feedback(false);
        appendLog('⚠️ '+payload);
      }catch(e){
        try { appendLog('err: ' + (e && e.message ? e.message : String(e))); } catch(_) {}
        showToast('Lỗi xử lý', false);
        updateStatusBadge(false, 'Lỗi xử lý');
      }
    }

    async function start(){
      if (running) return;
      try{
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        preview.srcObject = stream; preview.play(); running = true;
        if ('BarcodeDetector' in window){
          detector = new window.BarcodeDetector({ formats: ['qr_code'] });
          tick();
        } else {
          showToast('Thiếu BarcodeDetector, dùng quét dự phòng.', false);
          useJsQR = true;
          canvas = document.getElementById('qr-canvas');
          ctx = canvas.getContext('2d');
          tickFallback();
        }
      }catch(e){ showToast('Không truy cập được camera', false); }
    }

    async function stop(){
      running = false;
      if (preview) { preview.pause(); preview.srcObject = null; }
      if (stream){ stream.getTracks().forEach(t=>t.stop()); stream=null; }
    }

    async function tick(){
      if (!running || !detector) return;
      if (Date.now() < cooldownUntil) { requestAnimationFrame(tick); return; }
      try{
        const codes = await detector.detect(preview);
        if (codes && codes.length){
          for (const c of codes){ if (c.rawValue) { await handlePayload(c.rawValue); break; } }
        }
      }catch(e){ /* ignore */ }
      requestAnimationFrame(tick);
    }

    async function tickFallback(){
      if (!running || !useJsQR) return;
      if (Date.now() < cooldownUntil) { requestAnimationFrame(tickFallback); return; }
      try{
        if (preview.readyState >= 2) {
          const vw = preview.videoWidth || 640; const vh = preview.videoHeight || 480;
          if (canvas.width !== vw) { canvas.width = vw; }
          if (canvas.height !== vh) { canvas.height = vh; }
          ctx.drawImage(preview, 0, 0, canvas.width, canvas.height);
          const img = ctx.getImageData(0, 0, canvas.width, canvas.height);
          if (window.jsQR) {
            const code = window.jsQR(img.data, img.width, img.height);
            if (code && code.data) { await handlePayload(code.data); }
          }
        }
      }catch(e){ /* ignore */ }
      requestAnimationFrame(tickFallback);
    }

    document.getElementById('btnSubmit').addEventListener('click', function(){
      const v = document.getElementById('qrInput').value.trim();
      if (v) { appendLog('➡ ' + v); handlePayload(v); }
    });
    btnStart.addEventListener('click', start);
    btnStop.addEventListener('click', stop);
  </script>
  <script>
    function simulateScan(){
      const input = document.getElementById('qrInput');
      const btn = document.getElementById('btnSubmit');
      if (!input || !btn) return;
      const demoCode = 'BK-2024-TEST';
      input.value = demoCode;
      const oldBg = input.style.backgroundColor;
      input.style.transition = 'background-color .2s ease';
      input.style.backgroundColor = '#ffe08a';
      setTimeout(()=>{ input.style.backgroundColor = oldBg || ''; }, 250);
      setTimeout(()=>{ btn.click(); }, 300);
    }
  </script>
</body>
</html>
