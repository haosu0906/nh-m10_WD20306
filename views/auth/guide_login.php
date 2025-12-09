<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Đăng nhập Hướng dẫn viên</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
  :root{--bg:#062a21;--bg2:#0a342a;--card:#0e332a;--accent:#36d399;--accent2:#22c55e;--ring:#2da77b;--text:#d1fae5;--yellow:#f1de63}
  body{min-height:100vh;background:radial-gradient(600px 300px at 80% 20%, rgba(79,240,168,.15), transparent),radial-gradient(600px 300px at 20% 80%, rgba(79,240,168,.12), transparent),linear-gradient(180deg,var(--bg),var(--bg2));overflow:hidden}
  .orb{position:absolute;border-radius:50%;filter:blur(60px) saturate(130%);opacity:.7;pointer-events:none}
  .orb1{width:220px;height:220px;background:radial-gradient(circle at 30% 30%, var(--accent) 0%, transparent 60%);left:78%;top:18%;animation:float1 8s ease-in-out infinite}
  .orb2{width:280px;height:280px;background:radial-gradient(circle at 30% 30%, var(--yellow) 0%, transparent 60%);left:18%;top:72%;animation:float2 9s ease-in-out infinite}
  .orb3{width:180px;height:180px;background:radial-gradient(circle at 30% 30%, var(--accent) 0%, transparent 60%);left:35%;top:30%;animation:float3 7s ease-in-out infinite}
  @keyframes float1{0%{transform:translate(0,0)}50%{transform:translate(-30px,40px)}100%{transform:translate(0,0)}}
  @keyframes float2{0%{transform:translate(0,0)}50%{transform:translate(30px,-40px)}100%{transform:translate(0,0)}}
  @keyframes float3{0%{transform:translate(0,0)}50%{transform:translate(-20px,30px)}100%{transform:translate(0,0)}}
  .star{position:absolute;width:6px;height:6px;border-radius:50%;background:radial-gradient(circle, #e5ffee 0%, transparent 70%);opacity:.6;filter:blur(1px)}
  .tw1{left:12%;top:18%;animation:twinkle 3s linear infinite}
  .tw2{left:28%;top:12%;animation:twinkle 3s .3s linear infinite}
  .tw3{left:64%;top:10%;animation:twinkle 3s .6s linear infinite}
  .tw4{left:82%;top:28%;animation:twinkle 3s .9s linear infinite}
  .tw5{left:16%;top:84%;animation:twinkle 3s 1.2s linear infinite}
  .tw6{left:46%;top:76%;animation:twinkle 3s 1.5s linear infinite}
  .tw7{left:72%;top:82%;animation:twinkle 3s 1.8s linear infinite}
  .tw8{left:88%;top:60%;animation:twinkle 3s 2.1s linear infinite}
  @keyframes twinkle{0%,100%{opacity:.2}50%{opacity:1}}
  .card-login{background:rgba(14,51,42,.8);border-radius:24px;color:var(--text);border:1px solid rgba(79,240,168,.35);backdrop-filter:blur(12px);box-shadow:0 20px 60px rgba(79,240,168,.12),0 0 60px rgba(79,240,168,.25);animation:pulse 4s ease-in-out infinite}
  @keyframes pulse{0%,100%{box-shadow:0 20px 60px rgba(79,240,168,.12),0 0 60px rgba(79,240,168,.25);transform:scale(1)}50%{box-shadow:0 30px 80px rgba(79,240,168,.2),0 0 80px rgba(79,240,168,.35);transform:scale(1.01)}}
  .badge-pill{background:rgba(79,240,168,.12);color:var(--text);border:1px solid rgba(79,240,168,.35);padding:.4rem .8rem;border-radius:999px}
  .title-gradient{background-image:linear-gradient(90deg,#ffffff,#b7ffd6,var(--accent),var(--yellow));background-size:200% auto;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;animation:flow 5s linear infinite}
  @keyframes flow{0%{background-position:0% 50%}100%{background-position:200% 50%}}
  .form-control{background:transparent;border:1px solid rgba(79,240,168,.35);color:var(--text)}
  .form-control::placeholder{color:rgba(209,250,229,.8)}
  .form-control:focus{background:transparent;border-color:var(--ring);box-shadow:0 0 0 .25rem rgba(45,167,123,.25);color:var(--text);animation:pulseFocus 2s ease-in-out infinite}
  @keyframes pulseFocus{0%,100%{box-shadow:0 0 0 .25rem rgba(45,167,123,.25)}50%{box-shadow:0 0 0 .35rem rgba(45,167,123,.35)}}
  .input-group-text{background:transparent;border:1px solid rgba(79,240,168,.35);color:var(--accent)}
  .alert-soft{background:rgba(79,240,168,.12);color:var(--text);border:1px dashed rgba(79,240,168,.35);border-radius:12px}
  .btn-login{background-image:linear-gradient(90deg,var(--accent),var(--accent2));color:#082c1b;border:none;border-radius:999px;box-shadow:0 10px 30px rgba(79,240,168,.25);position:relative;overflow:hidden}
  .btn-login::before{content:"";position:absolute;left:-30%;top:0;height:100%;width:22%;background:linear-gradient(90deg,rgba(255,255,255,.0),rgba(255,255,255,.35),rgba(255,255,255,.0));transform:skewX(20deg);animation:shimmer 3s linear infinite}
  @keyframes shimmer{0%{left:-30%}100%{left:130%}}
  .btn-login:hover{transform:translateY(-2px);box-shadow:0 14px 36px rgba(79,240,168,.35)}
  .btn-login:active i{transform:translateX(4px)}
  .footer-note{color:rgba(209,250,229,.8)}
  @media (prefers-reduced-motion: reduce){.orb,.card-login,.title-gradient,.form-control:focus,.btn-login::before{animation:none}}
  </style>
</head>
<body>
  <span class="orb orb1"></span>
  <span class="orb orb2"></span>
  <span class="orb orb3"></span>
  <span class="star tw1"></span><span class="star tw2"></span><span class="star tw3"></span><span class="star tw4"></span>
  <span class="star tw5"></span><span class="star tw6"></span><span class="star tw7"></span><span class="star tw8"></span>

  <div class="d-flex align-items-center justify-content-center min-vh-100 position-relative">
    <div class="card-login p-4 p-md-5" style="width:420px;max-width:90vw">
      <div class="d-flex justify-content-center mb-3">
        <span class="badge-pill d-inline-flex align-items-center gap-2">
          <i class="fa-solid fa-globe"></i> Hướng dẫn viên du lịch
        </span>
      </div>
      <h1 class="h3 fw-bold text-center mb-2 title-gradient">Cổng vào hướng dẫn viên</h1>
      <p class="text-center mb-4" style="color:rgba(209,250,229,.85)">Đăng nhập để bắt đầu dẫn khách chinh phục những hành trình kỳ diệu.</p>

      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $msg): ?>
        <div><?= htmlspecialchars((string)$msg, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <form method="post" action="<?= BASE_URL ?>?r=guide_login_post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

        <div class="mb-2 text-muted" style="color:rgba(209,250,229,.85)">Tài khoản hướng dẫn viên</div>
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="fa-solid fa-compass"></i></span>
          <input type="email" name="email" class="form-control form-control-lg" placeholder="Nhập mã HDV hoặc email công ty" value="<?= htmlspecialchars((string)($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="mb-2 text-muted" style="color:rgba(209,250,229,.85)">Mã truy cập</div>
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
          <input type="password" name="password" class="form-control form-control-lg" placeholder="Nhập mã truy cập bí mật" required>
        </div>

        <button type="submit" class="btn btn-login btn-lg w-100">Đăng nhập <i class="fa-solid fa-arrow-right ms-1"></i></button>
      </form>

      <div class="text-center mt-3 footer-note">Tỏa sáng trên từng hành trình – chỉ dành cho hướng dẫn viên được ủy quyền.</div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
