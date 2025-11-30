<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Booking #<?= htmlspecialchars($item['id'] ?? '') ?> - In</title>
  <style>
    body{font-family: Arial, Helvetica, sans-serif; color:#222}
    h1{font-size:18px}
    .section{margin-bottom:12px}
    table{width:100%;border-collapse:collapse}
    table th, table td{border:1px solid #ccc;padding:6px;text-align:left}
  </style>
</head>
<body>
  <h1>Booking #<?= htmlspecialchars($item['id'] ?? '') ?></h1>
  <div class="section">
    <strong>Khách hàng:</strong> <?= htmlspecialchars($customer['full_name'] ?? '') ?><br>
    <strong>Phone:</strong> <?= htmlspecialchars($customer['phone'] ?? '') ?><br>
    <strong>Email:</strong> <?= htmlspecialchars($customer['email'] ?? '') ?><br>
  </div>

  <div class="section">
    <strong>Tour:</strong> <?= htmlspecialchars($item['tour_name'] ?? '') ?><br>
    <strong>Ngày đặt:</strong> <?= !empty($item['date_booked']) ? date('d/m/Y H:i', strtotime($item['date_booked'])) : '' ?><br>
    <strong>Tổng tiền:</strong> <?= number_format($item['total_price'] ?? 0,0,',','.') ?> đ
  </div>

  <div class="section">
    <strong>Danh sách hành khách (<?= count($guests) ?>):</strong>
    <table>
      <thead>
        <tr><th>STT</th><th>Họ tên</th><th>Năm sinh</th><th>Loại</th></tr>
      </thead>
      <tbody>
        <?php foreach($guests as $i=>$g): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($g['full_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($g['year_of_birth'] ?? '') ?></td>
          <td><?= htmlspecialchars($g['type'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="section">
    <strong>Nhà cung cấp:</strong>
    <table>
      <thead><tr><th>Tên</th><th>Loại</th><th>Điện thoại</th></tr></thead>
      <tbody>
        <?php foreach($suppliers as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['name'] ?? '') ?></td>
          <td><?= htmlspecialchars($s['type'] ?? '') ?></td>
          <td><?= htmlspecialchars($s['phone'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="margin-top:20px">
    <em>In từ hệ thống Tripmate</em>
  </div>
</body>
</html>