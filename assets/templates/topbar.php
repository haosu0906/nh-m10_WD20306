<?php $__topbar_style = (isset($topbar_mode) && $topbar_mode==='cyber')
  ? 'position:fixed;top:0;right:0;left:260px!important;width:calc(100% - 260px)!important;height:60px;background:transparent;z-index:1100;padding:0 16px;border-bottom:0'
  : 'position:fixed;top:0;right:0;left:260px!important;width:calc(100% - 260px)!important;height:60px;background:#ffffff;z-index:1100;padding:0 16px;border-bottom:1px solid #e5e7eb;'; ?>
<header class="topbar d-flex align-items-center justify-content-between shadow-sm" style="<?= $__topbar_style ?>">
  <style>
    .user-panel{width:420px;background:#0b1022;border:1px solid rgba(0,255,255,.2);box-shadow:0 0 12px rgba(0,255,255,.35),0 0 26px rgba(255,47,210,.25);border-radius:14px;overflow:hidden}
    .user-panel .panel-top{padding:16px;background:radial-gradient(120% 120% at 0% 0%,rgba(255,47,210,.12) 0%,rgba(0,255,255,.06) 100%),#0b1022;color:#c7e9ff}
    .user-panel .top-row{display:flex;align-items:center;gap:14px}
    .user-panel .avatar{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:radial-gradient(circle at 30% 30%,#ff2fd2 0%,#00f5ff 100%);box-shadow:0 0 16px rgba(255,47,210,.6);color:#0b1022}
    .user-panel .name{font-weight:800;letter-spacing:.6px;color:#ff3bd5;font-size:20px}
    .user-panel .sub{color:#86f1ff;font-size:12px}
    .user-panel .meta{color:#8ab6ff;font-size:12px}
    .user-panel .divider{height:1px;background:linear-gradient(90deg,rgba(255,47,210,.35),rgba(0,255,255,.35));}
    .user-panel .panel-header{display:flex;align-items:center;justify-content:space-between;background:#151233;border-top:1px solid rgba(255,47,210,.45);color:#18e1ff;padding:12px 14px;text-transform:uppercase;font-weight:800}
    .user-panel .panel-body{padding:4px 0;background:#0b0f24}
    .user-panel .panel-link{display:flex;align-items:center;gap:10px;color:#9feaff;text-decoration:none;padding:12px 14px;font-weight:600}
    .user-panel .panel-link:hover{background:rgba(24,225,255,.08)}
    .user-panel .panel-link .icon{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(24,225,255,.12);color:#18e1ff}
    .user-panel .panel-link.logout{color:#ff79e1}
    .user-panel .panel-link.logout .icon{background:rgba(255,47,210,.12);color:#ff3bd5}
  </style>
  <div class="input-group" style="max-width:520px">
    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span>
    <input class="form-control border-start-0" type="search" placeholder="Tìm bookings, customers, tours">
  </div>
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-light btn-sm"><i class="fa-regular fa-bell"></i></button>
    <div class="dropdown">
      <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
        <img style="width:28px;height:28px;border-radius:999px" src="https://i.pravatar.cc/40?img=12" alt=""> <span>Admin</span>
      </button>
      <div class="dropdown-menu dropdown-menu-end p-0 user-panel">
        <div class="panel-top">
          <div class="top-row">
            <div class="avatar"><i class="fa-solid fa-user-ninja" style="color:#0b1022"></i></div>
            <div>
              <?php $displayName = 'Admin'; if(isset($_SESSION['user_id'])){ $displayName = htmlspecialchars($_SESSION['username'] ?? $_SESSION['full_name'] ?? 'Admin'); } ?>
              <div class="name"><?= $displayName ?></div>
              <div class="sub">NETRUNNER CLASS | CYBER SECURITY LEVEL 9</div>
              <div class="meta">LAST LOGIN: <?= htmlspecialchars(date('H:i:s')) ?> | LOCATION: NIGHT CITY</div>
            </div>
          </div>
        </div>
        <div class="divider"></div>
        <div class="panel-header">
          <span>USER CONTROL PANEL</span>
          <i class="fa-solid fa-chevron-up" style="color:#ff3bd5"></i>
        </div>
        <div class="panel-body">
          <a class="panel-link" href="<?= BASE_URL ?>?r=admin_profile"><span class="icon"><i class="fa-regular fa-id-card"></i></span><span>Hồ Sơ CYBER</span></a>
          <a class="panel-link" href="#"><span class="icon"><i class="fa-solid fa-sliders"></i></span><span>Tùy chỉnh hệ thống</span></a>
          <a class="panel-link logout" href="<?= BASE_URL ?>?r=admin_logout"><span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span><span>Đăng xuất</span></a>
        </div>
      </div>
    </div>
  </div>
</header>
<?php
$__flash = function_exists('flash_get') ? flash_get() : null;
$__pmsg = $_SESSION['payment_message'] ?? null;
$__ptype = $_SESSION['payment_status'] ?? null;
if ($__pmsg) { unset($_SESSION['payment_message'], $_SESSION['payment_status']); }
if ($__flash || $__pmsg) {
  $__type = $__flash ? ($__flash['type'] ?? 'info') : ($__ptype === 'success' ? 'success' : 'danger');
  if ($__type === 'error') { $__type = 'danger'; }
  $__text = $__flash ? ($__flash['message'] ?? '') : $__pmsg;
  echo '<div class="global-flash-container"><div class="alert alert-' . htmlspecialchars($__type) . ' alert-dismissible fade show" role="alert">' . htmlspecialchars($__text) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>';
}
?>
<style>
.global-flash-container{position:fixed;top:68px;left:260px;right:20px;z-index:1200}
@media (max-width: 992px){.global-flash-container{left:0;right:0;margin:0 10px}}
</style>
