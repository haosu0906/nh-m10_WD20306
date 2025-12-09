<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Đăng nhập Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
  :root{--bg1:#0a0a12;--bg2:#1a1530;--bg3:#0f0e1a;--purple:#8b5cf6;--purple2:#a78bfa;--pink:#f472b6;--neon:#c084fc;--text:#e9d5ff}
  body{min-height:100vh;background:linear-gradient(180deg,var(--bg1),var(--bg2) 50%,var(--bg3));overflow:hidden;position:relative}
  .hex-layer{position:fixed;inset:0;opacity:.16;z-index:0}
  .scan-line{position:fixed;left:0;right:0;top:-10%;height:2px;background:linear-gradient(90deg,transparent,rgba(167,139,250,.9),transparent);box-shadow:0 0 18px rgba(167,139,250,.8),0 0 40px rgba(244,114,182,.35);animation:scan 4s linear infinite;z-index:1}
  @keyframes scan{0%{transform:translateY(-10vh)}100%{transform:translateY(110vh)}}
  .lightning{position:fixed;top:0;height:100vh;width:2px;background:linear-gradient(to bottom,rgba(167,139,250,0),rgba(167,139,250,1) 40%,rgba(167,139,250,0));filter:drop-shadow(0 0 12px rgba(167,139,250,.9));opacity:0;z-index:1}
  .lt1{left:20%;animation:strike 3s infinite}
  .lt2{left:55%;animation:strike 3s .6s infinite}
  .lt3{left:80%;animation:strike 3s 1.2s infinite}
  @keyframes strike{0%,86%,100%{opacity:0}7%{opacity:1}9%{opacity:.2}11%{opacity:1}14%{opacity:0}}
  .particle{position:fixed;bottom:-6vh;width:6px;height:6px;border-radius:50%;background:radial-gradient(circle,rgba(200,150,255,.95),transparent 60%);filter:blur(.6px);opacity:.75;z-index:0}
  .p1{left:12%;animation:float 15s linear infinite}
  .p2{left:28%;animation:float 15s .8s linear infinite}
  .p3{left:46%;animation:float 15s 1.6s linear infinite}
  .p4{left:62%;animation:float 15s 2.4s linear infinite}
  .p5{left:76%;animation:float 15s 3.2s linear infinite}
  .p6{left:88%;animation:float 15s 4s linear infinite}
  @keyframes float{0%{transform:translate(0,0)}25%{transform:translate(12px,-25vh)}50%{transform:translate(-6px,-55vh)}75%{transform:translate(10px,-85vh)}100%{transform:translate(0,-120vh)}}
  .admin-card{position:relative;background:rgba(10,10,20,.75);border-radius:24px;color:var(--text);border:1px solid rgba(139,92,246,.45);backdrop-filter:blur(10px);box-shadow:0 0 0 2px rgba(139,92,246,.25) inset,0 0 60px rgba(139,92,246,.35),0 20px 80px rgba(139,92,246,.25);animation:breathe 3s ease-in-out infinite}
  @keyframes breathe{0%,100%{box-shadow:0 0 0 2px rgba(139,92,246,.25) inset,0 0 40px rgba(139,92,246,.28),0 16px 60px rgba(139,92,246,.2);transform:scale(1)}50%{box-shadow:0 0 0 2px rgba(139,92,246,.35) inset,0 0 80px rgba(139,92,246,.45),0 26px 100px rgba(139,92,246,.3);transform:scale(1.01)}}
  .corner{position:absolute;width:16px;height:16px;border:2px solid var(--purple);border-radius:2px;box-shadow:0 0 16px rgba(139,92,246,.8)}
  .c1{top:-8px;left:-8px}
  .c2{top:-8px;right:-8px}
  .c3{bottom:-8px;left:-8px}
  .c4{bottom:-8px;right:-8px}
  .shield{width:76px;height:76px;margin:0 auto 12px;border-radius:50%;border:1px solid rgba(139,92,246,.45);background:radial-gradient(circle at 50% 50%, rgba(139,92,246,.5), rgba(139,92,246,0));box-shadow:0 0 40px rgba(139,92,246,.45);display:flex;align-items:center;justify-content:center;animation:pulseIcon 2s ease-in-out infinite}
  .shield i{font-size:30px;color:var(--neon)}
  @keyframes pulseIcon{0%,100%{transform:scale(1)}50%{transform:scale(1.06)}}
  .badge-pill{background:rgba(139,92,246,.18);color:var(--text);border:1px solid rgba(139,92,246,.45);padding:.45rem 1rem;border-radius:999px;letter-spacing:.14em;text-transform:uppercase;font-weight:700;display:inline-flex;align-items:center;gap:.5rem;box-shadow:0 0 20px rgba(139,92,246,.35)}
  .title-glitch{position:relative;font-size:2rem;font-weight:800;letter-spacing:.02em;text-transform:uppercase;color:#fff}
  .title-glitch::before,.title-glitch::after{content:attr(data-text);position:absolute;left:0;top:0;opacity:0}
  .title-glitch::before{color:#f472b6}
  .title-glitch::after{color:#8b5cf6}
  .title-glitch{animation:glitchCore 5s infinite}
  @keyframes glitchCore{0%,96%,100%{transform:none}97%{transform:translate(-1px,1px)}98%{transform:translate(2px,-2px)}99%{transform:translate(-2px,2px)}}
  .title-glitch::before{animation:glitchSlice1 5s infinite}
  .title-glitch::after{animation:glitchSlice2 5s infinite}
  @keyframes glitchSlice1{0%,96%,100%{opacity:0}97%{opacity:1;transform:translate(-2px,-2px)}98%{opacity:.8;transform:translate(2px,1px)}99%{opacity:0}}
  @keyframes glitchSlice2{0%,96%,100%{opacity:0}97%{opacity:1;transform:translate(2px,-1px)}98%{opacity:.8;transform:translate(-1px,2px)}99%{opacity:0}}
  .subtitle{color:rgba(233,213,255,.75)}
  .label-strong{color:rgba(233,213,255,.85);text-transform:uppercase;letter-spacing:.14em;font-weight:700}
  .input-group-text{background:transparent;border:1px solid rgba(139,92,246,.45);color:var(--neon)}
  .form-control{background:transparent;border:1px solid rgba(139,92,246,.45);color:var(--text)}
  .form-control::placeholder{color:rgba(233,213,255,.7)}
  .form-control:focus{background:transparent;border-color:var(--neon);box-shadow:0 0 0 .25rem rgba(139,92,246,.35),0 0 40px rgba(139,92,246,.4),inset 0 0 16px rgba(139,92,246,.25);color:var(--text)}
  .btn-login{background-image:linear-gradient(90deg,var(--purple),var(--pink));color:#1b1329;border:none;border-radius:999px;font-weight:800;text-transform:uppercase;letter-spacing:.14em;box-shadow:0 14px 36px rgba(139,92,246,.45),0 18px 60px rgba(244,114,182,.35);position:relative;overflow:hidden}
  .btn-login i{transition:transform .3s ease}
  .btn-login:hover{transform:translateY(-4px);filter:brightness(1.08);box-shadow:0 18px 48px rgba(139,92,246,.6),0 24px 70px rgba(244,114,182,.5)}
  .btn-login:hover i{transform:rotate(90deg)}
  .btn-login:active i{transform:translateX(4px)}
  .ripple{position:absolute;border-radius:50%;background:rgba(255,255,255,.5);transform:scale(0);animation:ripple .6s ease-out;pointer-events:none}
  @keyframes ripple{to{transform:scale(7);opacity:0}}
  .demo-alert{background:rgba(139,92,246,.16);border:1px solid rgba(139,92,246,.45);color:var(--text);border-radius:12px}
  .alert-status{background:rgba(139,92,246,.12);color:var(--text);border:1px dashed rgba(139,92,246,.45);border-radius:12px}
  .footer-note{border-top:1px solid rgba(139,92,246,.25);color:rgba(233,213,255,.7)}
  @media (prefers-reduced-motion: reduce){.scan-line,.lightning,.particle,.admin-card,.shield,.title-glitch,.btn-login,.ripple{animation:none}}
  </style>
</head>
<body>
  <svg class="hex-layer" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <pattern id="hex" x="0" y="0" width="60" height="52" patternUnits="userSpaceOnUse">
        <path d="M30 2 L56 15 L56 41 L30 54 L4 41 L4 15 Z" fill="none" stroke="#8b5cf6" stroke-opacity="0.18" stroke-width="1" />
      </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#hex)" />
  </svg>
  <div class="scan-line"></div>
  <span class="lightning lt1"></span><span class="lightning lt2"></span><span class="lightning lt3"></span>
  <span class="particle p1"></span><span class="particle p2"></span><span class="particle p3"></span><span class="particle p4"></span><span class="particle p5"></span><span class="particle p6"></span>

  <div class="d-flex align-items-center justify-content-center min-vh-100 position-relative" style="z-index:2">
    <div class="admin-card p-4 p-md-5" style="width:460px;max-width:92vw">
      <span class="corner c1"></span><span class="corner c2"></span><span class="corner c3"></span><span class="corner c4"></span>
      <div class="shield"><i class="fa-solid fa-shield-halved"></i></div>
      <div class="d-flex justify-content-center mb-3"><span class="badge-pill"><i class="fa-solid fa-bolt"></i> ADMINISTRATOR ACCESS</span></div>
      <h1 class="text-center mb-2 title-glitch" data-text="CỔNG QUẢN TRỊ">CỔNG QUẢN TRỊ</h1>
      <p class="text-center subtitle mb-4">Truy cập hệ thống quản lý toàn quyền</p>

      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $msg): ?>
        <div><?= htmlspecialchars((string)$msg, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <form method="post" action="<?= BASE_URL ?>?r=admin_login_post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

        <div class="mb-2 label-strong">Tài khoản Admin</div>
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
          <input type="email" name="email" class="form-control form-control-lg" placeholder="Nhập tên đăng nhập quản trị" value="<?= htmlspecialchars((string)($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="mb-2 label-strong">Mật khẩu bảo mật</div>
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
          <input type="password" name="password" class="form-control form-control-lg" placeholder="Nhập mật khẩu cấp cao" required>
        </div>

        
        <div id="statusAlert" class="alert-status p-3 mb-3 d-none">Đang xác thực...</div>

        <button id="adminLoginBtn" type="submit" class="btn btn-login btn-lg w-100">Đăng nhập hệ thống <i class="fa-solid fa-bolt ms-2"></i></button>
      </form>

      <div class="text-center mt-3 footer-note pt-3"><i class="fa-solid fa-shield-halved me-2"></i>Hệ thống bảo mật cao cấp – Chỉ dành cho quản trị viên được ủy quyền</div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded',function(){
    var btn=document.getElementById('adminLoginBtn');
    var status=document.getElementById('statusAlert');
    if(btn){
      btn.addEventListener('click',function(e){
        var r=document.createElement('span');
        var rect=btn.getBoundingClientRect();
        var x=e.clientX-rect.left;var y=e.clientY-rect.top;
        r.className='ripple';r.style.left=x+'px';r.style.top=y+'px';r.style.width='20px';r.style.height='20px';
        btn.appendChild(r);setTimeout(function(){r.remove();},600);
        if(status){status.classList.remove('d-none');status.textContent='Đang xác thực...';}
      });
    }
  });
  </script>
</body>
</html>
