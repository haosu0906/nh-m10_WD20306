<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Chi tiết Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3;
    }

    body {
        background: #f8f9fa;
    }

    .sidebar {}

    .sidebar h3 {
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        font-size: 16px;
    }

    .nav-link {
        color: rgba(255, 255, 255, .95);
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .6rem;
        border-radius: .5rem;
        text-decoration: none;
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, .1);
    }

    .main {
        margin-left: 200px;
        padding: 86px 22px 22px; /* account for sticky topbar height */
    }

    .badge-status {
        font-size: 0.9rem;
        padding: 6px 12px;
    }

    .debt-warning {
        background-color: #fff3cd;
        border-left: 4px solid #ff6b6b;
        padding: 12px;
        border-radius: 4px;
    }

    .payment-table {
        font-size: 0.9rem;
    }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page = 'booking'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>

    <!-- Main content -->
        <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Chi tiết Booking #<?= htmlspecialchars($item['id']) ?></h2>
                <p class="text-muted mb-0">Thông tin chi tiết và quản lý booking tour</p>
            </div>
            <a href="<?= BASE_URL ?>?r=booking" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        <?php if ($item): 
            $customer = isset($customer) ? $customer : null;
            $guests = isset($guests) ? $guests : [];
            $total_paid = isset($total_paid) ? $total_paid : 0;
            $payment_history = isset($payment_history) ? $payment_history : [];
            $remaining = ($item['total_price'] ?? 0) - $total_paid;
        ?>

        <!-- 1. THÔNG TIN KHÁCH HÀNG -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin Khách hàng</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Họ tên:</strong> <?= htmlspecialchars($customer['full_name'] ?? 'N/A') ?></p>
                        <p><strong>Số điện thoại:</strong> 
                            <a href="tel:<?= htmlspecialchars($customer['phone'] ?? '') ?>">
                                <?= htmlspecialchars($customer['phone'] ?? 'N/A') ?>
                            </a>
                        </p>
                        <p><strong>Email:</strong> 
                            <a href="mailto:<?= htmlspecialchars($customer['email'] ?? '') ?>">
                                <?= htmlspecialchars($customer['email'] ?? 'N/A') ?>
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($customer['address'] ?? 'N/A') ?></p>
                        <p><strong>Ngày đặt:</strong> <?= !empty($item['date_booked']) ? date('d/m/Y H:i', strtotime($item['date_booked'])) : 'N/A' ?></p>
                        <p><strong>Trạng thái:</strong> 
                            <?php
                                $statusLabels = [
                                    'pending' => '<span class="badge bg-warning badge-status">Chờ xác nhận</span>',
                                    'deposit' => '<span class="badge bg-info badge-status">Đã cọc</span>',
                                    'completed' => '<span class="badge bg-success badge-status">Hoàn tất</span>',
                                    'canceled' => '<span class="badge bg-danger badge-status">Hủy</span>'
                                ];
                                echo $statusLabels[$item['booking_status'] ?? 'pending'] ?? '<span class="badge bg-secondary badge-status">N/A</span>';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.5. THÔNG TIN NHÀ CUNG CẤP -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Danh sách Nhà cung cấp Dịch vụ</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($suppliers)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Nhà cung cấp</th>
                                <th>Loại</th>
                                <th>Dịch vụ</th>
                                <th>Người liên hệ</th>
                                <th>Điện thoại</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $supplier_ids = [];
                                foreach ($suppliers as $supp): 
                                    // Group by supplier to avoid duplicates
                                    $key = $supp['id'];
                                    if (!isset($supplier_ids[$key])):
                                        $supplier_ids[$key] = true;
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($supp['name'] ?? 'N/A') ?></strong></td>
                                <td><?= htmlspecialchars($supp['type'] ?? 'N/A') ?></td>
                                <td>
                                    <?php 
                                        // Lấy tất cả dịch vụ của supplier này
                                        $services = [];
                                        foreach ($suppliers as $s) {
                                            if ($s['id'] == $supp['id']) {
                                                $services[] = $s['service_type'] . ': ' . $s['service_description'];
                                            }
                                        }
                                        echo implode('<br>', $services);
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($supp['contact_person'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="tel:<?= htmlspecialchars($supp['phone'] ?? '') ?>">
                                        <?= htmlspecialchars($supp['phone'] ?? 'N/A') ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($supp['email'] ?? '') ?>">
                                        <?= htmlspecialchars($supp['email'] ?? 'N/A') ?>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                    endif; 
                                endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Không có thông tin nhà cung cấp</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 2. DANH SÁCH HÀNH KHÁCH -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Danh sách Hành khách (<?= count($guests) ?> người)</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($guests)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped payment-table">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Họ tên</th>
                                <th>Năm sinh</th>
                                <th>Giới tính</th>
                                <th>Loại khách</th>
                                <th>Hộ chiếu</th>
                                <th>Phòng</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guests as $idx => $guest): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><?= htmlspecialchars($guest['full_name'] ?? '') ?></td>
                                <td>
                                    <?php 
                                        $dob = $guest['dob'] ?? null; 
                                        $yob = $guest['year_of_birth'] ?? null; 
                                        echo htmlspecialchars($dob ? date('Y', strtotime($dob)) : ($yob ?? ''));
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($guest['gender'] ?? '') ?></td>
                                <td><?= htmlspecialchars($guest['type'] ?? 'Người lớn') ?></td>
                                <td><?= htmlspecialchars($guest['id_document_no'] ?? ($guest['passport'] ?? '')) ?></td>
                                <td>
                                    <?php $currentAssign = !empty($assignmentsByGuest[$guest['id'] ?? 0]) ? $assignmentsByGuest[$guest['id']] : null; ?>
                                    <?php if ($currentAssign): ?>
                                        <?= htmlspecialchars(($currentAssign['hotel_name'] ?? '') . ' - Phòng ' . ($currentAssign['room_number'] ?? '')) ?> (<?= htmlspecialchars($currentAssign['room_type'] ?? '') ?>)
                                    <?php else: ?>
                                        <span class="text-muted">Chưa gán</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($guest['is_checked_in'])): ?>
                                        <span class="badge bg-success">Đã đến</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Chưa đến</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <details>
                                        <summary class="btn btn-sm btn-outline-primary">Thao tác</summary>
                                        <div class="mt-2 p-2 border rounded bg-white">
                                            <div class="mb-2">
                                                <strong>Gán phòng</strong>
                                                <form method="post" action="<?= BASE_URL ?>?r=booking_assign_room" class="mt-1">
                                                    <input type="hidden" name="booking_id" value="<?= (int)$item['id'] ?>">
                                                    <input type="hidden" name="guest_id" value="<?= (int)($guest['id'] ?? 0) ?>">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <select name="room_id" class="form-select form-select-sm" style="min-width:200px">
                                                            <?php foreach(($availableRooms ?? []) as $r): ?>
                                                                <option value="<?= (int)$r['id'] ?>">
                                                                    <?= htmlspecialchars(($r['hotel_name'] ?? 'Khách sạn') . ' - Phòng ' . ($r['room_number'] ?? '')) ?> (<?= htmlspecialchars($r['room_type'] ?? 'Loại phòng') ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <input type="date" name="check_in_date" class="form-control form-control-sm" style="max-width:150px">
                                                        <input type="date" name="check_out_date" class="form-control form-control-sm" style="max-width:150px">
                                                        <button type="submit" class="btn btn-sm btn-primary">Gán</button>
                                                        <?php if ($currentAssign): ?>
                                                            <a class="btn btn-sm btn-outline-danger" href="<?= BASE_URL ?>?r=booking_unassign_room&assignment_id=<?= (int)$currentAssign['id'] ?>&booking_id=<?= (int)$item['id'] ?>" onclick="return confirm('Gỡ phân phòng khách này?')">Gỡ</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </form>
                                            </div>
                                            <div>
                                                <strong>Check‑in</strong>
                                                <form method="post" action="<?= BASE_URL ?>?r=booking_guest_checkin" class="mt-1">
                                                    <input type="hidden" name="booking_id" value="<?= (int)$item['id'] ?>">
                                                    <input type="hidden" name="guest_id" value="<?= (int)($guest['id'] ?? 0) ?>">
                                                    <input type="hidden" name="checked" value="<?= !empty($guest['is_checked_in']) ? 0 : 1 ?>">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <select name="stage" class="form-select form-select-sm" style="max-width:160px">
                                                            <option value="gather">Điểm tập trung</option>
                                                            <option value="bus">Lên xe</option>
                                                        </select>
                                                        <input name="location" class="form-control form-control-sm" style="max-width:180px" placeholder="Vị trí (VD: Cổng A)">
                                                        <button type="submit" class="btn btn-sm <?= !empty($guest['is_checked_in']) ? 'btn-success' : 'btn-outline-secondary' ?>">
                                                            <?= !empty($guest['is_checked_in']) ? 'Đánh dấu chưa đến' : 'Đánh dấu đã đến' ?>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </details>
                                </td>
                                <td><?= htmlspecialchars($guest['notes'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Chưa có danh sách hành khách</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 3. THÔNG TIN TÀI CHÍNH & THANH TOÁN -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Thông tin Tài chính</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p><strong>Tổng tiền tour:</strong></p>
                        <h5><?= number_format($item['total_price'] ?? 0, 0, ',', '.') ?> đ</h5>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Đã thanh toán:</strong></p>
                        <h5 class="text-success"><?= number_format($total_paid, 0, ',', '.') ?> đ</h5>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Còn thiếu:</strong></p>
                        <h5 class="<?= $remaining > 0 ? 'text-danger' : 'text-success' ?>">
                            <?= number_format($remaining, 0, ',', '.') ?> đ
                        </h5>
                    </div>
                </div>

                <?php if ($remaining > 0): ?>
                <div class="debt-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>⚠️ Còn nợ:</strong> <?= number_format($remaining, 0, ',', '.') ?> đ
                </div>
                <?php else: ?>
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>✓ Đã thanh toán đủ</strong>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 4. LỊCH SỬ THANH TOÁN -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử Giao dịch</h5>
                    <a href="<?= BASE_URL ?>?r=booking_add_payment&id=<?= $item['id'] ?>" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i>Thêm thanh toán
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($payment_history)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped payment-table">
                        <thead class="table-light">
                            <tr>
                                <th>Ngày</th>
                                <th>Số tiền</th>
                                <th>Hình thức</th>
                                <th>Người xác nhận</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payment_history as $payment): ?>
                            <tr>
                                <td><?= !empty($payment['payment_date']) ? date('d/m/Y H:i', strtotime($payment['payment_date'])) : 'N/A' ?></td>
                                <td><?= number_format($payment['amount'] ?? 0, 0, ',', '.') ?> đ</td>
                                <td>
                                    <?php
                                        $method = $payment['method'] ?? 'N/A';
                                        $methodLabels = [
                                            'bank_transfer' => 'Chuyển khoản',
                                            'cash' => 'Tiền mặt'
                                        ];
                                        echo htmlspecialchars($methodLabels[$method] ?? ucfirst(str_replace('_', ' ', $method)));
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($payment['confirmed_by'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if (($payment['status'] ?? '') === 'completed'): ?>
                                        <span class="badge bg-success">Hoàn thành</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Chờ xác nhận</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Chưa có giao dịch nào</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 4.5. LỊCH SỬ TRẠNG THÁI -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-stream me-2"></i>Lịch sử Trạng thái</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($statusHistory)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Thời gian</th>
                                <th>Trạng thái cũ</th>
                                <th>Trạng thái mới</th>
                                <th>Người thay đổi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $viStatus = [
                                'pending' => 'Chờ xác nhận',
                                'deposit' => 'Đã cọc',
                                'completed' => 'Hoàn tất',
                                'canceled' => 'Hủy'
                            ];
                            foreach ($statusHistory as $st): 
                        ?>
                            <tr>
                                <td><?= !empty($st['changed_at']) ? date('d/m/Y H:i', strtotime($st['changed_at'])) : 'N/A' ?></td>
                                <td><?= htmlspecialchars($viStatus[$st['old_status'] ?? ''] ?? ($st['old_status'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($viStatus[$st['new_status'] ?? ''] ?? ($st['new_status'] ?? '')) ?></td>
                                <td><?= htmlspecialchars(($st['changed_by_name'] ?? '') ?: ($st['changed_by'] ?? '')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted mb-0">Chưa có lịch sử trạng thái</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 5. PHÂN PHÒNG KHÁCH SẠN -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bed me-2"></i>Phân phòng khách sạn</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Phòng khả dụng</label>
                        <form method="post" action="<?= BASE_URL ?>?r=booking_assign_room" class="d-flex flex-wrap gap-2">
                            <input type="hidden" name="booking_id" value="<?= (int)$item['id'] ?>">
                            <select name="room_id" class="form-select" style="min-width:220px" required>
                                <?php foreach(($availableRooms ?? []) as $r): ?>
                                    <option value="<?= (int)$r['id'] ?>">
                                        <?= htmlspecialchars(($r['hotel_name'] ?? 'Khách sạn') . ' - Phòng ' . ($r['room_number'] ?? '')) ?>
                                        (<?= htmlspecialchars($r['room_type'] ?? 'Loại phòng') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="d-flex flex-wrap gap-2">
                                <div>
                                    <label class="form-label">Nhận phòng</label>
                                    <input type="date" name="check_in_date" class="form-control">
                                </div>
                                <div>
                                    <label class="form-label">Trả phòng</label>
                                    <input type="date" name="check_out_date" class="form-control">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Phân phòng</button>
                        </form>
                    </div>
                </div>

                <hr>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Khách sạn</th>
                                <th>Phòng</th>
                                <th>Loại phòng</th>
                                <th>Nhận</th>
                                <th>Trả</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($roomAssignments)): foreach ($roomAssignments as $ra): ?>
                                <tr>
                                    <td><?= htmlspecialchars($ra['hotel_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($ra['room_number'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($ra['room_type'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($ra['check_in_date'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($ra['check_out_date'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($ra['status'] ?? 'reserved') ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-danger" href="<?= BASE_URL ?>?r=booking_unassign_room&assignment_id=<?= (int)$ra['id'] ?>&booking_id=<?= (int)$item['id'] ?>" onclick="return confirm('Gỡ phân phòng này?')">Gỡ</a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="7" class="text-muted">Chưa có phân phòng</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5. YÊU CẦU ĐẶC BIỆT & GHI CHÚ -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clipboard me-2"></i>Yêu cầu đặc biệt & Ghi chú</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($item['special_requests'])): ?>
                    <div class="alert alert-info mb-0">
                        <?php echo nl2br(htmlspecialchars($item['special_requests'])); ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Không có ghi chú nào</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 6. CÁC NÚT CHỨC NĂNG -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Hành động</h5>
            </div>
            <div class="card-body d-flex gap-2">
                <a href="<?= BASE_URL ?>?r=booking_edit&id=<?= $item['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Sửa Booking
                </a>
                <a href="<?= BASE_URL ?>?r=booking_cancel&id=<?= $item['id'] ?>" class="btn btn-danger" onclick="return confirm('Xác nhận hủy booking này?');">
                    <i class="fas fa-times me-2"></i>Hủy Booking
                </a>
                <a href="<?= BASE_URL ?>?r=booking_send_email&id=<?= $item['id'] ?>" class="btn btn-info" onclick="return confirm('Gửi email tóm tắt booking cho khách hàng?');">
                    <i class="fas fa-envelope me-2"></i>Gửi Email
                </a>
                <a href="<?= BASE_URL ?>?r=booking_pdf&id=<?= $item['id'] ?>" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i>Xuất PDF
                </a>
                <a href="<?= BASE_URL ?>?r=booking" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>

        <?php else: ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            Không tìm thấy booking này
        </div>
        <a href="<?= BASE_URL ?>?r=booking" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
        <?php endif; ?>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
