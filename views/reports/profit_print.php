<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>In báo cáo lợi nhuận</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;color:#222}
    h1{font-size:18px;margin:8px 0}
    table{width:100%;border-collapse:collapse}
    th,td{border:1px solid #ccc;padding:6px;text-align:left}
    th{text-align:center;background:#f8fafc}
    .text-end{text-align:right}
  </style>
</head>
<body>
  <h1>Báo cáo lợi nhuận theo tour</h1>
  <table>
    <thead>
      <tr>
        <th>Tour</th>
        <th>Doanh thu</th>
        <th>Chi phí</th>
        <th>Lợi nhuận</th>
        <th>Biên lợi nhuận</th>
        <th>Số booking</th>
      </tr>
    </thead>
    <tbody>
      <?php $totalRev=0;$totalCost=0;$totalProfit=0;$totalBookings=0; foreach(($rows??[]) as $r): ?>
      <?php $totalRev+=$r['revenue'];$totalCost+=$r['cost'];$totalProfit+=$r['profit'];$totalBookings+=$r['booking_count']; ?>
      <tr>
        <td><?= htmlspecialchars($r['tour_title']) ?></td>
        <td class="text-end"><?= number_format($r['revenue'],0,',','.') ?></td>
        <td class="text-end"><?= number_format($r['cost'],0,',','.') ?></td>
        <td class="text-end"><?= number_format($r['profit'],0,',','.') ?></td>
        <td class="text-end"><?= number_format($r['margin'],2,',','.') ?>%</td>
        <td class="text-end"><?= (int)$r['booking_count'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th>Tổng</th>
        <th class="text-end"><?= number_format($totalRev,0,',','.') ?></th>
        <th class="text-end"><?= number_format($totalCost,0,',','.') ?></th>
        <th class="text-end"><?= number_format($totalProfit,0,',','.') ?></th>
        <th class="text-end"><?= $totalRev>0?number_format(($totalProfit/$totalRev)*100,2,',','.'):'0.00' ?>%</th>
        <th class="text-end"><?= (int)$totalBookings ?></th>
      </tr>
    </tfoot>
  </table>
</body>
</html>
