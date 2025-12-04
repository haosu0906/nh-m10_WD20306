<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Chi tiết Danh mục Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    .page-wrapper { display:flex; min-height:100vh; }
    .sidebar { position: static; width:250px; min-width:250px; flex:0 0 250px; height:auto; }
    .main { margin-left:0; flex:1; padding:22px; }
    .card { border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.05); }
    .value { font-weight:600; color:#1f2937; }
    .muted { color:#6b7280; }
    </style>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
</head>
<body>
    <div class="page-wrapper">
        <?php $current_page='tour_categories'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
        <main class="main">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Chi tiết Danh mục</h3>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>?r=tour_categories_edit&id=<?= (int)$item['id'] ?>" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i> Sửa
                </a>
                <a href="<?= BASE_URL ?>?r=tour_categories" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="card border-0">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="muted">ID</div>
                        <div class="value"><?= htmlspecialchars($item['id']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="muted">Tên danh mục</div>
                        <div class="value"><?= htmlspecialchars($item['name']) ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="muted">Loại tour</div>
                        <?php
                            $typeKey = $item['category_type'] ?? 'domestic';
                            $typeName = $types[$typeKey] ?? 'Tour trong nước';
                        ?>
                        <div class="value"><?= htmlspecialchars($typeName) ?></div>
                    </div>
                    <div class="col-12">
                        <div class="muted">Mô tả</div>
                        <div class="value"><?= nl2br(htmlspecialchars($item['description'] ?? '')) ?></div>
                    </div>
                </div>
            </div>
        </div>
        </main>
    </div>
</body>
</html>
