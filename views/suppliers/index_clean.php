<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Nhà cung cấp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .badge {
            display: inline-block;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            line-height: 1.2;
        }
        .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Nhà cung cấp Dịch vụ</h2>
        
        <div class="mb-3">
            <a href="<?= BASE_URL ?>?r=suppliers_create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm nhà cung cấp
            </a>
        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Tên nhà cung cấp</th>
                    <th>Người liên hệ</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Loại dịch vụ</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($suppliers)): foreach ($suppliers as $s): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$s['name']) ?></td>
                    <td><?= htmlspecialchars((string)$s['contact_person']) ?></td>
                    <td><?= htmlspecialchars((string)($s['email'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)$s['phone']) ?></td>
                    <td>
                        <?php
                        $map = [
                            'hotel' => ['Khách sạn', 'bg-primary'],
                            'restaurant' => ['Nhà hàng', 'bg-success'],
                            'transport' => ['Vận chuyển', 'bg-warning'],
                            'ticket' => ['Vé tham quan', 'bg-info'],
                            'insurance' => ['Bảo hiểm', 'bg-secondary'],
                            'guide' => ['HDV', 'bg-dark'],
                            'meal' => ['Ăn uống', 'bg-danger'],
                            'entertain' => ['Giải trí', 'bg-purple'],
                            'other' => ['Dịch vụ khác', 'bg-light']
                        ];
                        $st = $s['service_type'] ?? 'other';
                        $serviceInfo = $map[$st] ?? $map['other'];
                        ?>
                        <span class="badge <?= $serviceInfo[1] ?>"><?= $serviceInfo[0] ?></span>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>?r=suppliers_show&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-info">Xem</a>
                        <a href="<?= BASE_URL ?>?r=suppliers_edit&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <a href="<?= BASE_URL ?>?r=suppliers_delete&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
