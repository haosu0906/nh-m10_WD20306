<?php
$editing = isset($guide) && !empty($guide);
$title = $editing ? 'Cập nhật HDV' : 'Thêm HDV mới';
$formAction = $editing ? BASE_URL . '?r=guides_update&id=' . $guide['id'] : BASE_URL . '?r=guides_store';
$errors = $errors ?? [];
$old = $old ?? [];

$value = function($key, $default = '') use ($old, $guide) {
    if (isset($old[$key])) {
        return htmlspecialchars($old[$key]);
    }
    if (isset($guide[$key])) {
        return htmlspecialchars($guide[$key]);
    }
    return htmlspecialchars($default);
};
$selectedType = $old['guide_type'] ?? ($guide['guide_type'] ?? 'domestic');
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3
    }

    

    .sidebar h3 {
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        font-size: 16px
    }

    .nav-link {
        color: rgba(255, 255, 255, .95);
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .6rem;
        border-radius: .5rem;
        text-decoration: none
    }

    

    .avatar-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover
    }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='guides'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
  <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0"><?= $title ?></h3>
            <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=guides">Quay lại</a>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $message): ?>
                <li><?= htmlspecialchars($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="post" action="<?= $formAction ?>" enctype="multipart/form-data">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Thông tin HDV</div>
                <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control form-control-lg" required
                        value="<?= $value('full_name') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control form-control-lg" required value="<?= $value('phone') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control form-control-lg" required value="<?= $value('email') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">CMND/CCCD</label>
                    <input type="text" name="identity_no" class="form-control form-control-lg" value="<?= $value('identity_no') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Số chứng chỉ</label>
                    <input type="text" name="certificate_no" class="form-control form-control-lg"
                        value="<?= $value('certificate_no') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Loại HDV</label>
                    <select name="guide_type" class="form-select form-select-lg">
                        <?php foreach ($types as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $selectedType === $key ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ảnh đại diện</label>
                    <div style="border: 2px dashed #ccc; padding: 20px; text-align: center;">
                        <div class="mb-2">Kéo thả ảnh vào đây hoặc chọn từ máy</div>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                    </div>
                    <?php if ($editing && !empty($guide['avatar'])): ?>
                    <div class="mt-2">
                        <img src="<?= BASE_ASSETS_UPLOADS . $guide['avatar'] ?>" class="avatar-preview" alt="avatar">
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Ghi chú</label>
                    <textarea name="notes" rows="4" class="form-control form-control-lg"><?= $value('notes') ?></textarea>
                </div>
            </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary btn-lg">Lưu thông tin</button>
            </div>
        </form>
  </div>
</body>

</html>
