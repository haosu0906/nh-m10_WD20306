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
$detailLocations = $old['it_item_location'] ?? array_column($itineraryItems ?? [], 'location');
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
    <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
    <?php $current_page='tours'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>

    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
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
          <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">1. Thông tin cơ bản</div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label fw-bold">Tên tour</label>
                  <input type="text" name="title" class="form-control form-control-lg" required value="<?= $field('title') ?>">
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label fw-bold">Mã tour</label>
                    <input type="text" name="tour_code" class="form-control form-control-lg" value="<?= $field('tour_code') ?>">
                  </div>
                  <div>
                    <label class="form-label fw-bold">Danh mục</label>
                    <select name="category_id" class="form-select form-select-lg" required>
                      <option value="">-- Chọn danh mục --</option>
                      <?php foreach ($categories as $cat): ?>
                      <option value="<?= $cat['id'] ?>" <?= (string)$selectedCategory === (string)$cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Loại tour</label>
                  <select name="tour_type" class="form-select form-select-lg">
                    <?php foreach ($types as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $selectedType === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Nhà cung cấp</label>
                  <select name="supplier_id" class="form-select form-select-lg" required>
                    <option value="">-- Chọn nhà cung cấp --</option>
                    <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['id'] ?>" <?= (string)$selectedSupplier === (string)$supplier['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($supplier['name']) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Trạng thái</label>
                  <select name="status" class="form-select form-select-lg">
                    <?php foreach ($statuses as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $selectedStatus === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Số ngày</label>
                  <?php $durationDays = (int)($old['duration_days'] ?? 3); if ($durationDays < 1) { $durationDays = 3; } ?>
                  <input type="number" min="1" id="duration_days" name="duration_days" class="form-control form-control-lg"
                         value="<?= $durationDays ?>"
                         oninput="(function(i){var n=document.getElementById('duration_nights'); if(n){var v=parseInt(i.value||'0',10); n.value=Math.max(0,v-1);} })(this)">
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Số đêm</label>
                  <input type="number" id="duration_nights" class="form-control form-control-lg" value="<?= max(0, $durationDays - 1) ?>" readonly>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Giá niêm yết</label>
                  <input type="number" name="price_listed" class="form-control form-control-lg" min="0" step="1000"
                         value="<?= htmlspecialchars($adultPrice) ?>">
                </div>
                <div class="col-12">
                  <label class="form-label fw-bold">Ảnh Cover</label>
                  <div style="border: 2px dashed #ccc; padding: 20px; text-align: center;">
                    <div class="mb-2">Kéo thả ảnh vào đây hoặc chọn từ máy</div>
                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                    <div class="mt-2">Hoặc nhập URL ảnh</div>
                    <input type="url" name="cover_url" class="form-control"
                           value="<?= htmlspecialchars($old['cover_url'] ?? ($tour['cover_image'] ?? '')) ?>"
                           placeholder="https://...">
                    <?php $currentCover = $tour['cover_image'] ?? ($tour['image'] ?? ''); $isExternal = preg_match('/^https?:\/\//i', (string)$currentCover); $coverSrc = $currentCover ? ($isExternal ? $currentCover : (BASE_ASSETS_UPLOADS . $currentCover)) : ''; ?>
                    <?php if ($editing && $coverSrc !== ''): ?>
                      <div class="mt-3">
                        <img src="<?= $coverSrc ?>" alt="Cover hiện tại" class="img-fluid rounded" style="max-height:220px; object-fit:cover; border:1px solid #e5e7eb;">
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label fw-bold">Mô tả tour</label>
                  <textarea name="description" rows="4" class="form-control form-control-lg"><?= $field('description') ?></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
              <span>2. Lịch trình</span>
              <button type="button" class="btn btn-sm btn-outline-primary" id="add-it-row"><i class="fas fa-plus"></i> Thêm lịch trình</button>
            </div>
            <div class="card-body" id="it-container">
              <div class="row g-3 align-items-end it-row">
                <div class="col-md-1">
                  <label class="form-label fw-bold">Ngày</label>
                  <input type="number" name="it_item_day[]" class="form-control form-control-lg" min="1" value="1">
                </div>
                <div class="col-md-2">
                  <label class="form-label fw-bold">Bắt đầu</label>
                  <input type="time" name="it_item_start[]" class="form-control form-control-lg" value="08:00">
                </div>
                <div class="col-md-2">
                  <label class="form-label fw-bold">Kết thúc</label>
                  <input type="time" name="it_item_end[]" class="form-control form-control-lg" value="">
                </div>
                <div class="col-md-2">
                  <label class="form-label fw-bold">Buổi</label>
                  <select name="it_item_slot[]" class="form-select form-select-lg">
                    <option value="">-- Chọn buổi --</option>
                    <option value="morning">Sáng</option>
                    <option value="noon">Trưa</option>
                    <option value="afternoon">Chiều</option>
                    <option value="evening">Tối</option>
                  </select>
                </div>
                <div class="col-md-5">
                  <label class="form-label fw-bold">Tiêu đề</label>
                  <input type="text" name="it_item_title[]" class="form-control form-control-lg" placeholder="VD: Tham quan điểm A, di chuyển đến B">
                </div>
                <div class="col-12">
                  <label class="form-label fw-bold">Chi tiết</label>
                  <textarea name="it_item_details[]" rows="3" class="form-control form-control-lg" placeholder="Mô tả hoạt động, thời lượng, ghi chú..."></textarea>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Địa điểm</label>
                  <input type="text" name="it_item_location[]" class="form-control form-control-lg" placeholder="Tên địa điểm tham quan">
                </div>
                <div class="col-md-1 align-self-end d-none">
                  <button type="button" class="btn btn-outline-danger w-100 it-remove">&times;</button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">3. Giá</div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label fw-bold">Giá người lớn</label>
                  <input type="number" name="adult_price" class="form-control form-control-lg" min="0" step="1000"
                         value="<?= htmlspecialchars($adultPrice) ?>" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Giá trẻ em</label>
                  <input type="number" name="child_price" class="form-control form-control-lg" min="0" step="1000"
                         value="<?= htmlspecialchars($childPrice) ?>">
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Giá em bé</label>
                  <input type="number" name="infant_price" class="form-control form-control-lg" min="0" step="1000"
                         value="<?= htmlspecialchars($infantPrice) ?>">
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">4. Hình ảnh</div>
            <div class="card-body">
              <?php if (!empty($gallery)): ?>
                <div class="row g-3 align-items-center">
                  <?php foreach ($gallery as $img): ?>
                    <div class="col-auto">
                      <img src="<?= BASE_ASSETS_UPLOADS . $img['image_path'] ?>" alt="image" class="gallery-thumb border">
                    </div>
                    <div class="col-auto">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remove_images[]" value="<?= (int)$img['id'] ?>" id="img<?= (int)$img['id'] ?>">
                        <label class="form-check-label small" for="img<?= (int)$img['id'] ?>">Xóa ảnh này</label>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="text-muted">Chưa có hình ảnh.</div>
              <?php endif; ?>
              <div class="mt-3">
                <label class="form-label fw-bold">Thêm ảnh mới</label>
                <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple>
              </div>
            </div>
          </div>

          <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
              <span>5. Nhà cung cấp dịch vụ cho Tour</span>
              <button type="button" class="btn btn-sm btn-outline-primary" id="add-ts-row"><i class="fas fa-plus"></i> Thêm dịch vụ</button>
            </div>
            <div class="card-body" id="ts-container">
              <?php 
                $allowedServiceTypes = ['hotel'=>'Khách sạn','restaurant'=>'Nhà hàng','transport'=>'Vận chuyển','ticket'=>'Vé tham quan','insurance'=>'Bảo hiểm'];
                $prefill = $tourSuppliers ?? [];
                if (empty($prefill)) { $prefill = [['supplier_id'=>'','service_type'=>'','description'=>'']]; }
                foreach ($prefill as $row):
              ?>
              <div class="row g-3 align-items-end ts-row mb-2">
                <div class="col-md-4">
                  <label class="form-label">Nhà cung cấp</label>
                  <select name="ts_supplier_id[]" class="form-select">
                    <option value="">-- Chọn NCC --</option>
                    <?php foreach (($supplierMaster ?? []) as $s): ?>
                      <option value="<?= (int)$s['id'] ?>" <?= ((string)($row['supplier_id'] ?? '') === (string)$s['id'])?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Loại dịch vụ</label>
                  <select name="ts_service_type[]" class="form-select">
                    <option value="">-- Chọn loại --</option>
                    <?php foreach ($allowedServiceTypes as $k=>$v): ?>
                      <option value="<?= $k ?>" <?= (($row['service_type'] ?? '')===$k)?'selected':'' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Mô tả</label>
                  <input type="text" name="ts_description[]" class="form-control" value="<?= htmlspecialchars($row['description'] ?? '') ?>" placeholder="VD: khách sạn 3*, xe 45 chỗ...">
                </div>
                <div class="col-md-1">
                  <button type="button" class="btn btn-outline-danger w-100 ts-remove">&times;</button>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="position-sticky bottom-0 bg-white border-top py-3">
            <div class="text-end">
              <button type="submit" class="btn btn-primary btn-lg">Lưu lại</button>
              <a href="<?= BASE_URL ?>?r=tours" class="btn btn-outline-secondary btn-lg">Hủy</a>
            </div>
          </div>
        </form>
    </div>

    <script>
    (function(){
      var durInput = document.getElementById('duration_days');
      var nights = document.getElementById('duration_nights');
      if(durInput && nights){
        durInput.addEventListener('input', function(){
          var v = parseInt(durInput.value||'0',10);
          nights.value = Math.max(0, v-1);
        });
      }
    })();

    // NCC dịch vụ động
    (function(){
      const container = document.getElementById('ts-container');
      const addBtn = document.getElementById('add-ts-row');
      if (!container || !addBtn) return;
      addBtn.addEventListener('click', function(){
        const rows = container.querySelectorAll('.ts-row');
        const last = rows[rows.length - 1];
        const clone = last.cloneNode(true);
        // Reset inputs
        clone.querySelectorAll('select, input').forEach(function(el){ if (el.tagName==='SELECT') { el.selectedIndex = 0; } else { el.value=''; } });
        container.appendChild(clone);
        attachRemove();
      });
      function attachRemove(){
        container.querySelectorAll('.ts-remove').forEach(function(btn){
          btn.onclick = function(){
            const row = this.closest('.ts-row');
            const count = container.querySelectorAll('.ts-row').length;
            if (count > 1) { row.remove(); }
          }
        });
      }
      attachRemove();
    })();
    // Itinerary dynamic rows
    (function(){
      const container = document.getElementById('it-container');
      const addBtn = document.getElementById('add-it-row');
      if (!container || !addBtn) return;
      addBtn.addEventListener('click', function(){
        const rows = container.querySelectorAll('.it-row');
        const last = rows[rows.length - 1];
        const clone = last.cloneNode(true);
        clone.querySelectorAll('select, input, textarea').forEach(function(el){
          if (el.tagName === 'SELECT') { el.selectedIndex = 0; }
          else { el.value = ''; }
        });
        const removeBtnCol = clone.querySelector('.it-remove');
        if (removeBtnCol) { removeBtnCol.closest('.col-md-1').classList.remove('d-none'); }
        container.appendChild(clone);
        attachRemove();
      });
      function attachRemove(){
        container.querySelectorAll('.it-remove').forEach(function(btn){
          btn.onclick = function(){
            const row = this.closest('.it-row');
            const count = container.querySelectorAll('.it-row').length;
            if (count > 1) { row.remove(); }
          };
        });
      }
      attachRemove();
    })();
    </script>
</body>

</html>
