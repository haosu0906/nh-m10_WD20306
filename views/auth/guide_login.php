<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Đăng nhập HDV</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="mb-3 text-center">Đăng nhập Hướng dẫn viên</h4>
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <?php foreach ($errors as $msg): ?>
                  <div><?= htmlspecialchars((string)$msg, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
            <form method="post" action="<?= BASE_URL ?>?r=guide_login_post">
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars((string)($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button class="btn btn-primary w-100" type="submit">Đăng nhập</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
