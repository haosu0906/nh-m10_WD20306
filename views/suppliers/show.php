<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Nhà cung cấp: <?= htmlspecialchars((string)$supplier['name'], ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3
    }

    .sidebar {}

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

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, .1)
    }

    .main-content {}
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='suppliers'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>

    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Nhà cung cấp: <?= htmlspecialchars((string)$supplier['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="text-muted mb-0">Người liên hệ:
                    <?= htmlspecialchars((string)$supplier['contact_person'], ENT_QUOTES, 'UTF-8') ?> | ĐT:
                    <?= htmlspecialchars((string)$supplier['phone'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=suppliers">Quay lại</a>
        </div>

        <?php
      $mapService = [
        'hotel'       => 'Khách sạn',
        'restaurant'  => 'Nhà hàng',
        'transport'   => 'Vận chuyển',
        'attraction'  => 'Điểm tham quan',
        'ticket'      => 'Vé tham quan',
        'insurance'   => 'Bảo hiểm',
        'guide'       => 'HDV',
        'meal'        => 'Ăn uống',
        'entertain'   => 'Giải trí',
        'other'       => 'Dịch vụ khác',
      ];
      $serviceLabel = $mapService[$supplier['service_type']] ?? $supplier['service_type'];
    ?>

        <div class="mb-3">
            <span class="badge bg-primary">Dịch vụ chính:
                <?= htmlspecialchars((string)$serviceLabel, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-semibold">Các dịch vụ / chi phí đã dùng trong tour</div>
            <div class="card-body p-0">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tour</th>
                            <th>Loại chi phí</th>
                            <th>Mô tả</th>
                            <th>Số tiền</th>
                            <th>Ngày phát sinh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($supplier['expenses'])): foreach ($supplier['expenses'] as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars((string)($e['tour_title'] ?? ('#'.$e['tour_id'])), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td><?= htmlspecialchars((string)$e['expense_type'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)$e['description'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= number_format((float)$e['amount'], 0, ',', '.') ?> VND</td>
                            <td><?= htmlspecialchars((string)$e['date_incurred'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Chưa có dịch vụ/chi phí nào ghi nhận với
                                nhà cung cấp này</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
