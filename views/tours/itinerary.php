<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Lịch trình: <?= htmlspecialchars($tour['title']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
  <style>
    .timeline-day{border-left:4px solid #6366f1;padding-left:12px;margin-left:6px}
    .slot{display:flex;gap:12px;align-items:flex-start;padding:10px;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:8px}
    .slot-time{min-width:120px;font-weight:600}
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../../assets/configs/env.php'; ?>
  <?php $current_page='tours'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
  <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Lịch trình: <?= htmlspecialchars($tour['title']) ?></h3>
      <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=tours">Quay lại</a>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <form class="row g-2" method="post" action="<?= BASE_URL ?>?r=tours_itinerary_add">
          <input type="hidden" name="tour_id" value="<?= (int)$tour['id'] ?>">
          <div class="col-md-1">
            <label class="form-label">Ngày</label>
            <input type="number" min="1" class="form-control" name="day_number" value="1">
          </div>
          <div class="col-md-2">
            <label class="form-label">Bắt đầu</label>
            <input type="time" class="form-control" name="activity_time" value="08:00">
          </div>
          <div class="col-md-2">
            <label class="form-label">Kết thúc</label>
            <input type="time" class="form-control" name="end_time" value="">
          </div>
          <div class="col-md-2">
            <label class="form-label">Buổi</label>
            <select name="slot" class="form-select">
              <option value="morning">Sáng</option>
              <option value="noon">Trưa</option>
              <option value="afternoon">Chiều</option>
              <option value="evening">Tối</option>
            </select>
          </div>
          <div class="col-md-5">
            <label class="form-label">Tiêu đề</label>
            <input type="text" class="form-control" name="title" placeholder="VD: Tham quan điểm A, di chuyển đến B">
          </div>
          <div class="col-12">
            <label class="form-label">Chi tiết</label>
            <textarea class="form-control" rows="2" name="details" placeholder="Mô tả hoạt động, thời lượng, ghi chú..."></textarea>
          </div>
          <div class="col-md-2">
            <label class="form-label">Địa điểm</label>
            <input type="text" class="form-control" name="meal_plan" placeholder="VD: Vinpearl, Nhà hàng A, Điểm tham quan B">
          </div>
          <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Thêm hoạt động</button>
          </div>
        </form>
      </div>
    </div>

    <?php
      // nhóm items theo day_number
      $grouped = [];
      foreach (($items ?? []) as $it){
        $d = (int)$it['day_number'];
        if(!isset($grouped[$d])) $grouped[$d] = [];
        $grouped[$d][] = $it;
      }
    ?>

    <?php if(empty($grouped)): ?>
      <div class="alert alert-info">Chưa có lịch trình chi tiết. Hệ thống sẽ tự tạo hoặc bạn có thể chỉnh trong trang Sửa tour.</div>
    <?php endif; ?>

    <?php foreach ($grouped as $day => $rows): ?>
      <div class="mb-4 timeline-day">
        <h5>Ngày <?= (int)$day ?></h5>
        <?php foreach ($rows as $r): ?>
          <div class="slot">
            <div class="slot-time">
              <?php $st = !empty($r['activity_time']) ? $r['activity_time'] : (!empty($r['start_time']) ? $r['start_time'] : ''); ?>
              <?= htmlspecialchars($st ? substr($st,0,5) : '') ?>
              <?php if(!empty($r['end_time'])): ?> - <?= htmlspecialchars(substr($r['end_time'],0,5)) ?><?php endif; ?>
              <div class="text-muted small"><?= htmlspecialchars($r['slot'] ?? '') ?></div>
            </div>
            <div class="flex-grow-1">
              <div class="fw-semibold d-flex justify-content-between align-items-start">
                <span><?= htmlspecialchars($r['title'] ?? '') ?></span>
                <div class="ms-2">
                  <form method="post" action="<?= BASE_URL ?>?r=tours_itinerary_delete" onsubmit="return confirm('Xóa hoạt động này?')" class="d-inline">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <input type="hidden" name="tour_id" value="<?= (int)$tour['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                  </form>
                </div>
              </div>
              <div class="text-muted mb-2"><?= htmlspecialchars($r['details'] ?? '') ?></div>
              <?php if(!empty($r['meal_plan'])): ?>
                <div class="badge bg-light text-dark border mb-2">Địa điểm: <?= htmlspecialchars($r['meal_plan']) ?></div>
              <?php endif; ?>

              <form method="post" action="<?= BASE_URL ?>?r=tours_itinerary_update" class="row g-2">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <input type="hidden" name="tour_id" value="<?= (int)$tour['id'] ?>">
                <div class="col-md-1"><input type="number" class="form-control form-control-sm" name="day_number" value="<?= (int)$r['day_number'] ?>"/></div>
                <?php $st2 = !empty($r['activity_time']) ? $r['activity_time'] : (!empty($r['start_time']) ? $r['start_time'] : ''); ?>
                <div class="col-md-2"><input type="time" class="form-control form-control-sm" name="activity_time" value="<?= htmlspecialchars($st2 ? substr($st2,0,5) : '') ?>"/></div>
                <div class="col-md-2"><input type="time" class="form-control form-control-sm" name="end_time" value="<?= !empty($r['end_time'])?htmlspecialchars(substr($r['end_time'],0,5)):'' ?>"/></div>
                <div class="col-md-2">
                  <select name="slot" class="form-select form-select-sm">
                    <?php $slots = ['morning'=>'Sáng','noon'=>'Trưa','afternoon'=>'Chiều','evening'=>'Tối']; foreach($slots as $k=>$v): ?>
                      <option value="<?= $k ?>" <?= ($r['slot']??'')===$k?'selected':'' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="title" value="<?= htmlspecialchars($r['title'] ?? '') ?>"/></div>
                <div class="col-12"><textarea name="details" rows="2" class="form-control form-control-sm"><?= htmlspecialchars($r['details'] ?? '') ?></textarea></div>
                <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="meal_plan" placeholder="Địa điểm" value="<?= htmlspecialchars($r['meal_plan'] ?? '') ?>"/></div>
                <div class="col-md-2"><button class="btn btn-sm btn-outline-primary w-100">Lưu</button></div>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
