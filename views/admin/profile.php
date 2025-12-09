<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Hồ sơ admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet">
  <style>
    :root{--bg:#0a0a16;--cyan:#00f5ff;--pink:#ff2fd2;--pink2:#ff3bd5;--vio:#8b5cf6;--mint:#9ff7ff}
    body{background:var(--bg)}
    .cyber-bg{position:relative;min-height:100vh;background:radial-gradient(120% 120% at 10% 10%,rgba(255,47,210,.06) 0%,rgba(0,245,255,.06) 45%,rgba(10,10,22,1) 100%)}
    .cyber-bg::before{content:"";position:absolute;inset:-10% -20%;background:radial-gradient(60% 60% at 80% 20%,rgba(0,245,255,.08),transparent 60%),radial-gradient(50% 50% at 20% 80%,rgba(255,47,210,.08),transparent 60%);filter:blur(40px)}
    .scanlines{position:absolute;inset:0;background:repeating-linear-gradient(180deg,rgba(255,255,255,.04) 0 2px,transparent 2px 4px);mix-blend-mode:overlay;pointer-events:none}
    .page-wrap{position:relative;z-index:1;padding:96px 22px 26px;margin-left:260px}
    .header{text-align:center;margin-bottom:22px}
    .neon-text{font-weight:800;font-size:42px;background:linear-gradient(90deg,var(--pink),var(--cyan),var(--pink));-webkit-background-clip:text;background-clip:text;color:transparent;background-size:200% 200%;animation:neonMove 8s linear infinite}
    .glow-sub{color:#9de6ff;text-shadow:0 0 8px rgba(0,245,255,.6),0 0 18px rgba(0,245,255,.35);letter-spacing:.8px}
    .slide-line{height:2px;background:linear-gradient(90deg,transparent,rgba(255,47,210,.8),transparent);width:240px;margin:12px auto 0;animation:slideLine 3s ease-in-out infinite}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    @media(max-width:992px){.grid{grid-template-columns:1fr}}
    .cyber-card{position:relative;background:#0b1022;border:1px solid rgba(0,245,255,.25);box-shadow:0 0 14px rgba(0,245,255,.25),0 0 24px rgba(255,47,210,.15);border-radius:14px}
    .cyber-card.pink{border-color:rgba(255,47,210,.45);box-shadow:0 0 14px rgba(255,47,210,.35),0 0 24px rgba(0,245,255,.15)}
    .card-body{padding:18px}
    .corner{position:absolute;width:16px;height:16px;border-color:rgba(0,245,255,.55);}
    .c1{top:-1px;left:-1px;border-left:2px solid;border-top:2px solid}
    .c2{top:-1px;right:-1px;border-right:2px solid;border-top:2px solid}
    .c3{bottom:-1px;left:-1px;border-left:2px solid;border-bottom:2px solid}
    .c4{bottom:-1px;right:-1px;border-right:2px solid;border-bottom:2px solid}
    .avatar-wrap{display:flex;align-items:center;gap:14px}
    .avatar-lg{width:96px;height:96px;border-radius:999px;display:flex;align-items:center;justify-content:center;background:radial-gradient(circle at 30% 30%,var(--pink) 0%,var(--cyan) 100%);box-shadow:0 0 18px rgba(255,47,210,.6);color:#0b1022;animation:rotate360 10s linear infinite}
    .name{font-weight:800;color:var(--pink2);font-size:20px;text-shadow:0 0 10px rgba(255,47,210,.6)}
    .muted{color:#86f1ff}
    .badge-role{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border-radius:999px;padding:.45rem .7rem;text-decoration:none}
    .section-title{display:flex;align-items:center;gap:8px;color:#18e1ff;font-weight:800;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px}
    .info-row{display:flex;align-items:center;gap:10px;color:#9feaff;padding:8px 0}
    .info-row .icon{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(24,225,255,.12);color:#18e1ff}
    .neon-actions{display:flex;gap:12px;justify-content:center;margin-top:16px}
    .neon-btn{display:inline-flex;align-items:center;gap:8px;border:2px solid;border-radius:10px;padding:.65rem 1rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;transition:.25s}
    .neon-btn.logout{border-color:#ff5252;color:#ff8585;box-shadow:0 0 12px rgba(255,82,82,.4)}
    .neon-btn.logout:hover{transform:translateY(-2px);box-shadow:0 0 18px rgba(255,82,82,.7)}
    .neon-btn.edit{border-color:var(--cyan);color:#9de6ff;box-shadow:0 0 12px rgba(0,245,255,.35)}
    .neon-btn.edit:hover{transform:translateY(-2px);box-shadow:0 0 18px rgba(0,245,255,.65)}
    .footer{color:#7aa6c7;text-align:center;margin-top:16px}
    .cyber-modal .modal-content{background:#0b1022;border:1px solid rgba(0,245,255,.35);box-shadow:0 0 18px rgba(0,245,255,.35)}
    .cyber-modal .modal-header{border-bottom:1px solid rgba(255,47,210,.35);color:#18e1ff}
    .cyber-modal .modal-footer{border-top:1px solid rgba(255,47,210,.35)}
    .pulse-glow{box-shadow:0 0 18px rgba(0,245,255,.7)!important;border-color:rgba(0,245,255,.7)!important}
    @keyframes neonMove{0%{background-position:0% 50%}100%{background-position:200% 50%}}
    @keyframes slideLine{0%,100%{transform:translateX(-40px)}50%{transform:translateX(40px)}}
    @keyframes rotate360{to{transform:rotate(360deg)}}
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <?php $current_page='home'; $topbar_mode='cyber'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
  <div class="cyber-bg">
    <div class="scanlines"></div>
    <div class="page-wrap">
      <div class="header">
        <div class="neon-text">HỒ SƠ ADMIN</div>
        <div class="glow-sub">HỆ THỐNG QUẢN TRỊ CYBERPUNK v2.0</div>
        <div class="slide-line"></div>
      </div>
      <div class="grid">
        <div class="cyber-card">
          <span class="corner c1"></span><span class="corner c2"></span><span class="corner c3"></span><span class="corner c4"></span>
          <div class="card-body">
            <div class="avatar-wrap">
              <div class="avatar-lg"><i class="fa-solid fa-user-secret"></i></div>
              <div>
                <div class="name"><?= htmlspecialchars($user['full_name'] ?? ($user['username'] ?? 'Admin')) ?></div>
                <div class="muted"><i class="fa-regular fa-calendar-days me-1"></i> Tham gia: <?= !empty($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '---' ?></div>
                <div class="mt-2"><a class="badge-role" href="<?= BASE_URL ?>?r=staff"><i class="fa-solid fa-shield"></i> QUẢN TRỊ HỆ THỐNG</a></div>
              </div>
            </div>
            <div class="mt-3">
              <div class="section-title"><i class="fa-solid fa-database"></i> Thông tin đăng nhập</div>
              <div class="info-row"><div class="icon"><i class="fa-solid fa-user"></i></div><div>Tên đăng nhập</div><div class="ms-auto text-end" style="color:#9de6ff"><?= htmlspecialchars($user['username'] ?? '') ?></div></div>
              <div class="info-row"><div class="icon"><i class="fa-solid fa-barcode"></i></div><div>Mã hồ sơ</div><div class="ms-auto text-end" style="color:#9de6ff">PH59999</div></div>
              <?php $active = (bool)($user['is_active'] ?? $user['status'] ?? 1); ?>
              <div class="info-row"><div class="icon"><i class="fa-solid fa-power-off"></i></div><div>Trạng thái</div><div class="ms-auto text-end" style="color:<?= $active? '#32e39b':'#ff6b6b' ?>"><?= $active? 'HOẠT ĐỘNG':'NGHỈ' ?></div></div>
            </div>
          </div>
        </div>
        <div class="cyber-card pink">
          <span class="corner c1"></span><span class="corner c2"></span><span class="corner c3"></span><span class="corner c4"></span>
          <div class="card-body">
            <div class="section-title"><i class="fa-solid fa-circle-info"></i> Thông tin liên hệ</div>
            <div class="info-row"><div class="icon"><i class="fa-regular fa-envelope"></i></div><div>Email</div><div class="ms-auto" style="color:#9de6ff"><?= htmlspecialchars($user['email'] ?? '') ?></div></div>
            <div class="info-row"><div class="icon"><i class="fa-solid fa-phone"></i></div><div>Số điện thoại</div><div class="ms-auto" style="color:#9de6ff"><?= htmlspecialchars($user['phone'] ?? '') ?></div></div>
            <div class="section-title mt-3"><i class="fa-solid fa-shield-halved"></i> Bảo mật hệ thống</div>
            <div class="info-row"><div class="icon"><i class="fa-solid fa-key"></i></div><div>Đổi mật khẩu</div><div class="ms-auto" style="color:#9de6ff">Liên hệ quản trị hệ thống</div></div>
            <div class="info-row"><div class="icon"><i class="fa-solid fa-user-shield"></i></div><div>Phiên đăng nhập</div><div class="ms-auto" style="color:#9de6ff">Đang đăng nhập quyền admin</div></div>
          </div>
        </div>
      </div>
      <div class="neon-actions">
        <button class="neon-btn logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</button>
        <button class="neon-btn edit" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa-regular fa-pen-to-square"></i> Sửa thông tin</button>
      </div>
      <div class="footer">CYBERPUNK ADMIN PANEL v2.0 | NIGHT CITY CORP | Hệ thống đang hoạt động ổn định</div>
    </div>
  </div>

  <div class="modal fade cyber-modal" id="logoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header"><div class="neon-text" style="font-size:20px">Xác nhận</div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body" style="color:#9de6ff">Bạn chắc chắn muốn đăng xuất?</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="button" id="confirmLogout" class="btn btn-outline-danger">Đăng xuất</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade cyber-modal" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header"><div class="neon-text" style="font-size:20px">Sửa thông tin</div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="editForm" class="modal-body" style="color:#9de6ff">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Họ tên</label>
              <input class="form-control" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">SĐT</label>
              <input class="form-control" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <?php if (!empty($user['id'])): ?>
              <a class="btn btn-outline-info w-100" href="<?= BASE_URL ?>?r=staff_edit&id=<?= (int)$user['id'] ?>">Mở trang chỉnh sửa nâng cao</a>
              <?php endif; ?>
            </div>
          </div>
        </form>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="button" id="saveEdit" class="btn btn-outline-primary">Lưu</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function(){
      var cards=document.querySelectorAll('.cyber-card,.neon-btn');
      setInterval(function(){
        var i=Math.floor(Math.random()*cards.length);
        var el=cards[i]; if(!el) return; el.classList.add('pulse-glow');
        setTimeout(function(){el.classList.remove('pulse-glow');},800);
      },2500);
      var confirm=document.getElementById('confirmLogout');
      if(confirm){confirm.addEventListener('click',function(){window.location.href='<?= BASE_URL ?>?r=admin_logout';});}
      var save=document.getElementById('saveEdit');
      if(save){save.addEventListener('click',function(){
        var m=new bootstrap.Modal(document.getElementById('editModal'));
        m.hide();
      });}
    })();
  </script>
</body>
</html>
