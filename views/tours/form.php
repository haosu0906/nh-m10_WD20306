<?php
$editing = isset($tour) && !empty($tour);
$title = $editing ? 'Cập nhật tour' : 'Thêm tour mới';
$formAction = $editing ? BASE_URL . '?r=tours_update&id=' . $tour['id'] : BASE_URL . '?r=tours_store';
$errors = $errors ?? [];
$old = $old ?? [];

$field = function($key, $default = '') use ($old, $tour) {
    if (isset($old[$key])) {
        return htmlspecialchars($old[$key]);
    }
    if (isset($tour[$key])) {
        return htmlspecialchars($tour[$key]);
    }
    return htmlspecialchars($default);
};

$selectedCategory = $old['category_id'] ?? ($tour['category_id'] ?? '');
$selectedType = $old['tour_type'] ?? ($tour['tour_type'] ?? 'domestic');
$selectedStatus = $old['status'] ?? ($tour['status'] ?? 'upcoming');
$selectedSupplier = $old['supplier_id'] ?? ($tour['supplier_id'] ?? '');

// Lịch trình tổng quan (mức ngày) – giữ để dùng cho mô tả ngắn
$itineraryDays = $old['itinerary_day'] ?? array_column($itineraries, 'day_number');
$itineraryLocations = $old['itinerary_location'] ?? array_column($itineraries, 'location');
$itineraryActivities = $old['itinerary_activity'] ?? array_column($itineraries, 'activities');
$itineraryRows = max(1, count($itineraryDays));

// Lịch trình chi tiết theo khung giờ cho form tour
// Ưu tiên dữ liệu old khi submit lỗi, nếu không dùng từ $itineraryItems được controller truyền xuống
$detailDays   = $old['it_item_day'] ?? array_column($itineraryItems ?? [], 'day_number');
$detailStarts = $old['it_item_start'] ?? array_map(fn($r) => substr($r['activity_time'], 0, 5), $itineraryItems ?? []);
$detailEnds   = $old['it_item_end'] ?? array_map(fn($r) => !empty($r['end_time']) ? substr($r['end_time'], 0, 5) : '', $itineraryItems ?? []);
$detailSlots  = $old['it_item_slot'] ?? array_column($itineraryItems ?? [], 'slot');
$detailTitles = $old['it_item_title'] ?? array_column($itineraryItems ?? [], 'title');
$detailNotes  = $old['it_item_details'] ?? array_column($itineraryItems ?? [], 'details');
$detailMeals = $old['it_item_meal'] ?? array_column($itineraryItems ?? [], 'meal_plan');
$detailRows   = max(1, count($detailDays));

$adultPrice = $old['adult_price'] ?? ($price['adult_price'] ?? 0);
$childPrice = $old['child_price'] ?? ($price['child_price'] ?? 0);
$infantPrice = $old['infant_price'] ?? ($price['infant_price'] ?? 0);
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3
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
        overflow: auto
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

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, .1)
    }

    .main {
        margin-left: 200px;
        padding: 22px
    }

    .itinerary-row {
        border: 1px solid #e5e7eb;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 12px
    }

    .gallery-thumb {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px
    }

    .section-collapsible .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer
    }

    .section-collapsible .chev {
        transition: transform .2s
    }

    .section-collapsible.collapsed .chev {
        transform: rotate(-90deg)
    }

    .section-body {
        display: block
    }

    .section-collapsible.collapsed .section-body {
        display: none
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=payments"><i class="fas fa-credit-card"></i> Thanh toán</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=cancellation_policies"><i class="fas fa-ban"></i> Chính sách hủy</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_schedules"><i class="fas fa-calendar-alt"></i> Lịch HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_ratings"><i class="fas fa-star"></i> Đánh giá HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_login">
                <i class="fas fa-door-open"></i> Portal HDV
            </a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=admin_login">
                <i class="fas fa-user-shield"></i> Đăng nhập Admin
            </a>
        </nav>
    </div>

    <main class="main">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3><?= $title ?></h3>
                <p class="text-muted mb-0">Nhập đầy đủ thông tin tour, lịch trình, giá và hình ảnh</p>
            </div>
            <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=tours">Quay lại</a>
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
            <div class="card mb-4">
                <div class="card-header fw-semibold">1. Thông tin cơ bản</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên tour <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required
                                value="<?= $field('title') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= (string)$selectedCategory === (string)$cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Loại tour</label>
                            <select name="tour_type" class="form-select">
                                <?php foreach ($types as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $selectedType === $key ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $selectedStatus === $key ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">-- Chọn nhà cung cấp --</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>"
                                    <?= (string)$selectedSupplier === (string)$supplier['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($supplier['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Chính sách hủy</label>
                            <select name="cancellation_policy_id" class="form-select">
                                <option value="">-- Chọn chính sách --</option>
                                <?php if (!empty($cancellationPolicies)): foreach ($cancellationPolicies as $cp): ?>
                                <option value="<?= $cp['id'] ?>" <?= (isset($tour['cancellation_policy_id']) && (int)$tour['cancellation_policy_id'] === (int)$cp['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cp['name']) ?> (<?= $cp['refund_percentage'] ?>%)
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số ngày tour</label>
                            <?php $durationDays = (int)($old['duration_days'] ?? 3); if($durationDays<1){$durationDays=3;} ?>
                            <input type="number" min="1" class="form-control" name="duration_days" id="duration_days"
                                value="<?= $durationDays ?>">
                            <small class="text-muted" id="nights_note">Mặc định: <span
                                    id="nights_num"><?= max(0,$durationDays-1) ?></span> đêm</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mô tả tour</label>
                            <textarea name="description" rows="4"
                                class="form-control"><?= $field('description') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ảnh cover</label>
                            <input type="file" class="form-control" name="cover_image" accept="image/*">
                            <small class="text-muted">Có thể nhập Link ảnh cover bên cạnh hoặc tải file tại đây. Ít nhất
                                cần có 1 ảnh khi tạo mới.</small>
                            <?php if ($editing && !empty($tour['cover_image'])): ?>
                            <div class="mt-2">
                                <?php $isExternal = preg_match('/^https?:\/\//i', (string)$tour['cover_image']); ?>
                                <img src="<?= $isExternal ? $tour['cover_image'] : (BASE_ASSETS_UPLOADS . $tour['cover_image']) ?>"
                                    class="gallery-thumb" alt="cover">
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Link ảnh cover</label>
                            <input type="url" class="form-control" name="cover_url"
                                value="<?= htmlspecialchars($old['cover_url'] ?? ($tour['cover_image'] ?? '')) ?>"
                                placeholder="https://...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 section-collapsible collapsed" id="sec-itinerary">
                <div class="card-header fw-semibold" onclick="toggleSec('sec-itinerary')">2. Lịch trình tour <span
                        class="chev">▾</span></div>
                <div class="card-body section-body">
                    <p class="text-muted">Nhập lịch trình chi tiết theo từng ngày, từng khung giờ. Thông tin này sẽ dùng
                        cho cả trang Lịch trình và Portal HDV.</p>

                    <div id="itinerary-wrapper">
                        <?php for ($i = 0; $i < $detailRows; $i++): ?>
                        <div class="itinerary-row">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-1">
                                    <label class="form-label">Ngày</label>
                                    <input type="number" min="1" class="form-control" name="it_item_day[]"
                                        value="<?= htmlspecialchars($detailDays[$i] ?? ($i+1)) ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Bắt đầu</label>
                                    <input type="time" class="form-control" name="it_item_start[]"
                                        value="<?= htmlspecialchars($detailStarts[$i] ?? '08:00') ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Kết thúc</label>
                                    <input type="time" class="form-control" name="it_item_end[]"
                                        value="<?= htmlspecialchars($detailEnds[$i] ?? '') ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Buổi</label>
                                    <?php $slotsMap = ['morning'=>'Sáng','noon'=>'Trưa','afternoon'=>'Chiều','evening'=>'Tối']; $curSlot = $detailSlots[$i] ?? ''; ?>
                                    <select name="it_item_slot[]" class="form-select">
                                        <option value="">-- Chọn buổi --</option>
                                        <?php foreach($slotsMap as $k=>$v): ?>
                                        <option value="<?= $k ?>" <?= $curSlot===$k?'selected':'' ?>><?= $v ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Tiêu đề</label>
                                    <input type="text" class="form-control" name="it_item_title[]"
                                        value="<?= htmlspecialchars($detailTitles[$i] ?? '') ?>"
                                        placeholder="VD: Tham quan điểm A, di chuyển đến B">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Chi tiết</label>
                                    <textarea class="form-control" rows="2" name="it_item_details[]"
                                        placeholder="Mô tả hoạt động, thời lượng, ghi chú..."><?= htmlspecialchars($detailNotes[$i] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Địa điểm</label>
                                    <input type="text" class="form-control" name="it_item_meal[]"
                                        value="<?= htmlspecialchars($detailMeals[$i] ?? '') ?>"
                                        placeholder="Tên địa điểm tham quan">
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-itinerary"><i
                            class="fas fa-plus"></i> Thêm hoạt động</button>
                </div>
            </div>

            <div class="card mb-4 section-collapsible collapsed" id="sec-prices">
                <div class="card-header fw-semibold" onclick="toggleSec('sec-prices')">3. Giá bán <span
                        class="chev">▾</span></div>
                <div class="card-body section-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Giá người lớn <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="adult_price" min="0" step="1000"
                                value="<?= htmlspecialchars($adultPrice) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giá trẻ em</label>
                            <input type="number" class="form-control" name="child_price"
                                value="<?= htmlspecialchars($childPrice) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giá em bé</label>
                            <input type="number" class="form-control" name="infant_price"
                                value="<?= htmlspecialchars($infantPrice) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 section-collapsible collapsed" id="sec-gallery">
                <div class="card-header fw-semibold" onclick="toggleSec('sec-gallery')">4. Hình ảnh tour <span
                        class="chev">▾</span></div>
                <div class="card-body section-body">
                    <div class="mb-3">
                        <label class="form-label">Tải lên hình ảnh (có thể chọn nhiều)</label>
                        <input type="file" class="form-control" name="gallery[]" accept="image/*" multiple>
                    </div>
                    <?php if ($editing && !empty($gallery)): ?>
                    <div class="d-flex flex-wrap gap-3">
                        <?php foreach ($gallery as $image): ?>
                        <label class="text-center">
                            <img src="<?= BASE_ASSETS_UPLOADS . $image['image_path'] ?>" class="gallery-thumb mb-2"
                                alt="gallery">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_images[]"
                                    value="<?= $image['id'] ?>" id="img<?= $image['id'] ?>">
                                <label class="form-check-label" for="img<?= $image['id'] ?>">Xóa</label>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4 section-collapsible collapsed" id="sec-policy">
                <div class="card-header fw-semibold" onclick="toggleSec('sec-policy')">5. Chính sách <span
                        class="chev">▾</span></div>
                <div class="card-body section-body">
                    <label class="form-label">Điều khoản & chính sách hủy tour</label>
                    <textarea name="policy" rows="5" class="form-control"><?= $field('policy') ?></textarea>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary btn-lg">Lưu tour</button>
            </div>
        </form>
    </main>

    <script>
    function toggleSec(id) {
        const sec = document.getElementById(id);
        sec.classList.toggle('collapsed');
    }
    const durInput = document.getElementById('duration_days');
    if (durInput) {
        const nightsNum = document.getElementById('nights_num');
        const sync = () => {
            const d = parseInt(durInput.value || '1', 10);
            nightsNum.textContent = Math.max(0, d - 1)
        };
        durInput.addEventListener('input', sync);
        sync();
    }
    const addBtn = document.getElementById('add-itinerary');
    const wrapper = document.getElementById('itinerary-wrapper');
    addBtn.addEventListener('click', () => {
        const div = document.createElement('div');
        div.className = 'itinerary-row';
        div.innerHTML = `
        <div class="row g-3 align-items-end">
          <div class="col-md-1">
            <label class="form-label">Ngày</label>
            <input type="number" class="form-control" name="it_item_day[]" value="1">
          </div>
          <div class="col-md-2">
            <label class="form-label">Bắt đầu</label>
            <input type="time" class="form-control" name="it_item_start[]" value="08:00">
          </div>
          <div class="col-md-2">
            <label class="form-label">Kết thúc</label>
            <input type="time" class="form-control" name="it_item_end[]" value="">
          </div>
          <div class="col-md-2">
            <label class="form-label">Buổi</label>
            <select name="it_item_slot[]" class="form-select">
              <option value="">-- Chọn buổi --</option>
              <option value="morning">Sáng</option>
              <option value="noon">Trưa</option>
              <option value="afternoon">Chiều</option>
              <option value="evening">Tối</option>
            </select>
          </div>
          <div class="col-md-5">
            <label class="form-label">Tiêu đề</label>
            <input type="text" class="form-control" name="it_item_title[]" value="" placeholder="VD: Tham quan điểm A, di chuyển đến B">
          </div>
          <div class="col-12">
            <label class="form-label">Chi tiết</label>
            <textarea class="form-control" rows="2" name="it_item_details[]" placeholder="Mô tả hoạt động, thời lượng, ghi chú..."></textarea>
          </div>
          <div class="col-md-3">
            <label class="form-label">Địa điểm</label>
            <input type="text" class="form-control" name="it_item_meal[]" value="" placeholder="Tên địa điểm tham quan">
          </div>
        </div>
      `;
        wrapper.appendChild(div);
    });
    </script>
</body>

</html>
