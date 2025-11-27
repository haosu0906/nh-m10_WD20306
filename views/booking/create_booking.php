<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Tạo Booking mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3;
    }

    body {
        background: #f8f9fa;
    }

    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        width: 200px;
        padding: 20px;
        background: linear-gradient(180deg, var(--accent), #764ba2);
        color: #fff;
        overflow: auto;
    }

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
        padding: 22px;
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân sự</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_login">
                <i class="fas fa-door-open"></i> Portal HDV
            </a>

        </nav>
    </div>

    <main class="main">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Tạo Booking mới</h3>
                <p class="text-muted mb-0">Chọn tour, ngày khởi hành và nhập danh sách khách</p>
            </div>
            <a href="<?= BASE_URL ?>?r=booking" class="btn btn-outline-secondary">Quay lại danh sách</a>
        </div>

        <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>?r=booking_store">
            <div class="card mb-4">
                <div class="card-header fw-semibold">Thông tin booking</div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tour / Lịch khởi hành</label>
                        <select class="form-select" name="schedule_id" id="schedule-select" required>
                            <option value="">-- Chọn tour và ngày khởi hành --</option>
                            <?php foreach ($schedules as $s): ?>
                            <option value="<?= $s['id'] ?>" data-tour-id="<?= $s['tour_id'] ?>">
                                <?= htmlspecialchars($s['tour_title']) ?> -
                                <?= htmlspecialchars($s['start_date']) ?> (Tối đa: <?= (int)$s['max_capacity'] ?> khách)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="tour_id" id="tour-id-hidden" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Số lượng khách</label>
                        <input type="number" min="1" class="form-control" name="total_guests" id="total-guests"
                            value="1" required />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Khách hàng</label>
                        <select class="form-select" name="customer_user_id">
                            <option value="">-- Chọn khách hàng (users.role = traveler) --</option>
                            <?php if (!empty($customers)): foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>">
                                <?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['email']) ?>)
                            </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Thông tin khách hàng</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-guest-btn">
                        <i class="fas fa-user-plus"></i> Thêm khách
                    </button>
                </div>
                <div class="card-body" id="guests-container">
                    <div class="row g-2 guest-row mb-3">
                        <div class="col-md-3">
                            <input type="text" name="guest_full_name[]" class="form-control" placeholder="Họ tên"
                                required />
                        </div>
                        <div class="col-md-2">
                            <select name="guest_gender[]" class="form-select">
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="guest_dob[]" class="form-control" />
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="guest_id_document_no[]" class="form-control"
                                placeholder="CMND/CCCD" />
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <input type="text" name="guest_notes[]" class="form-control" placeholder="Ghi chú" />
                            <button type="button" class="btn btn-outline-danger btn-remove-guest">&times;</button>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Tạo booking</button>
                </div>
            </div>
        </form>
    </main>

    <script>
    // Gán tour_id theo schedule được chọn
    const scheduleSelect = document.getElementById('schedule-select');
    const tourHidden = document.getElementById('tour-id-hidden');
    if (scheduleSelect && tourHidden) {
        scheduleSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            tourHidden.value = opt.getAttribute('data-tour-id') || '';
        });
    }

    // Form khách dynamic
    const guestsContainer = document.getElementById('guests-container');
    const addGuestBtn = document.getElementById('add-guest-btn');

    function attachRemoveHandlers() {
        document.querySelectorAll('.btn-remove-guest').forEach(function(btn) {
            btn.onclick = function() {
                const row = this.closest('.guest-row');
                if (document.querySelectorAll('.guest-row').length > 1) {
                    row.remove();
                }
            };
        });
    }

    if (addGuestBtn && guestsContainer) {
        addGuestBtn.addEventListener('click', function() {
            const firstRow = document.querySelector('.guest-row');
            const clone = firstRow.cloneNode(true);
            clone.querySelectorAll('input').forEach(function(input) {
                input.value = '';
            });
            guestsContainer.appendChild(clone);
            attachRemoveHandlers();
        });
        attachRemoveHandlers();
    }

    // Validate cơ bản trước khi submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!scheduleSelect.value) {
                alert('Vui lòng chọn tour / lịch khởi hành');
                e.preventDefault();
                return;
            }

            const totalGuestsInput = document.getElementById('total-guests');
            const total = parseInt(totalGuestsInput.value || '0', 10);
            if (isNaN(total) || total <= 0) {
                alert('Số lượng khách phải lớn hơn 0');
                e.preventDefault();
                return;
            }

            const names = Array.from(document.querySelectorAll('input[name="guest_full_name[]"]'))
                .map(function(i) {
                    return i.value.trim();
                })
                .filter(function(v) {
                    return v !== '';
                });

            if (names.length !== total) {
                if (!confirm('Số lượng khách nhập (' + names.length + ') khác với tổng số khách (' + total +
                        '). Bạn vẫn muốn tiếp tục?')) {
                    e.preventDefault();
                    return;
                }
            }
        });
    }
    </script>
</body>

</html>