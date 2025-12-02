<?php
// Edit booking form
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Chỉnh sửa Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>.main{padding:22px}</style>
</head>
<body>
<main class="main">
  <div class="card">
    <div class="card-header"><h5>Chỉnh sửa Booking #<?= htmlspecialchars($item['id'] ?? '') ?></h5></div>
    <div class="card-body">
      <form method="post" action="<?= BASE_URL ?>?r=booking_update">
        <input type="hidden" name="id" value="<?= htmlspecialchars($item['id'] ?? '') ?>" />
        <div class="mb-3">
          <label>Tour</label>
          <select name="tour_id" class="form-select">
            <?php foreach($tours as $t): ?>
              <option value="<?= $t['id'] ?>" <?= ($t['id']==($item['tour_id']??0))? 'selected' : '' ?>><?= htmlspecialchars($t['title'] ?? $t['name'] ?? '') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Số khách</label>
          <input type="number" name="number_of_guests" class="form-control" value="<?= htmlspecialchars($item['total_guests'] ?? 1) ?>" />
        </div>
        <div class="mb-3">
          <label>Tổng tiền</label>
          <input type="text" name="total_amount" class="form-control" value="<?= htmlspecialchars($item['total_price'] ?? '') ?>" />
        </div>
        <div class="mb-3">
          <label>Trạng thái</label>
          <select name="status" class="form-select">
            <option value="pending" <?= ($item['booking_status']=='pending')? 'selected':'' ?>>Chờ xác nhận</option>
            <option value="deposit" <?= ($item['booking_status']=='deposit')? 'selected':'' ?>>Đã cọc</option>
            <option value="completed" <?= ($item['booking_status']=='completed')? 'selected':'' ?>>Hoàn tất</option>
            <option value="canceled" <?= ($item['booking_status']=='canceled')? 'selected':'' ?>>Hủy</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Ghi chú</label>
          <textarea name="special_requests" class="form-control"><?= htmlspecialchars($item['special_requests'] ?? '') ?></textarea>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-primary">Lưu thay đổi</button>
          <a class="btn btn-secondary" href="<?= BASE_URL ?>?r=booking_detail&id=<?= $item['id'] ?>">Huỷ</a>
        </div>
      </form>
    </div>
  </div>
</main>
</body>
</html>
