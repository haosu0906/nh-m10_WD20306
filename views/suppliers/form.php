<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <title><?= isset($supplier) ? 'Sửa' : 'Thêm' ?> Nhà cung cấp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    .page-wrapper{ display:flex; min-height:100vh }
    .sidebar{ position:static; width:200px; min-width:200px; flex:0 0 200px }
    .main{ flex:1; padding:22px }
    .card{ border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.05) }
    </style>
</head>
<body>
    <?php $current_page='suppliers'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <div class="page-wrapper">
        <main class="main">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0"><?= isset($supplier) ? 'Sửa' : 'Thêm' ?> Nhà cung cấp</h3>
                <a href="<?= BASE_URL ?>?r=suppliers" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
            </div>

            <div class="card border-0">
                <div class="card-body">
                    <form method="post" action="<?= BASE_URL ?>?r=<?= isset($supplier) ? 'suppliers_update&id=' . (int)$supplier['id'] : 'suppliers_store' ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tên nhà cung cấp</label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($supplier['name'] ?? ($_SESSION['old']['name'] ?? '')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Loại dịch vụ</label>
                                <select class="form-select" name="service_type" required>
                                    <?php foreach(($serviceTypes ?? []) as $key=>$label): ?>
                                        <option value="<?= $key ?>" <?= (($supplier['service_type'] ?? ($_SESSION['old']['service_type'] ?? ''))===$key)?'selected':'' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Người liên hệ</label>
                                <input type="text" class="form-control" name="contact_person" value="<?= htmlspecialchars($supplier['contact_person'] ?? ($_SESSION['old']['contact_person'] ?? '')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Điện thoại</label>
                                <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($supplier['phone'] ?? ($_SESSION['old']['phone'] ?? '')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($supplier['email'] ?? ($_SESSION['old']['email'] ?? '')) ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($supplier['address'] ?? ($_SESSION['old']['address'] ?? '')) ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Mô tả</label>
                                <textarea class="form-control" rows="3" name="description"><?= htmlspecialchars($supplier['description'] ?? ($_SESSION['old']['description'] ?? '')) ?></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" name="is_active">
                                    <option value="1" <?= ((int)($supplier['is_active'] ?? 1)===1)?'selected':'' ?>>Hoạt động</option>
                                    <option value="0" <?= ((int)($supplier['is_active'] ?? 1)===0)?'selected':'' ?>>Ngưng</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Lưu</button>
                            <a href="<?= BASE_URL ?>?r=suppliers" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
