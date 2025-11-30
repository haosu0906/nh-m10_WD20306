<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Lịch khởi hành tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3;
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

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px
    }

    .cal-cell {
        min-height: 100px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 6px;
        font-size: 12px;
        background: #fff
    }

    .cal-cell .date {
        font-weight: 600;
        font-size: 12px;
        margin-bottom: 4px
    }

    .event-badge {
        display: block;
        font-size: 11px;
        border-radius: 4px;
        padding: 2px 4px;
        margin-bottom: 3px;
        background: rgba(102, 126, 234, .08);
        border: 1px solid rgba(102, 126, 234, .4);
        color: #333
    }

    .event-badge small {
        display: block;
        color: #666
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
            <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_schedules"><i class="fas fa-calendar-alt"></i> Lịch HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_ratings"><i class="fas fa-star"></i> Đánh giá HDV</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=schedules_calendar"><i class="fas fa-calendar"></i> Lịch
                khởi hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_login">
                <i class="fas fa-door-open"></i> Portal HDV
            </a>

        </nav>
    </div>

    <main class="main">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">Lịch khởi hành tour</h3>
                <p class="text-muted mb-0">Xem tất cả lịch tour trên calendar, click từng ô để xem chi tiết</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-list"></i> Danh
                    sách</a>
            </div>
        </div>

        <?php
      $today = new DateTime();
      $currentYear = (int)($_GET['year'] ?? $today->format('Y'));
      $currentMonth = (int)($_GET['month'] ?? $today->format('m'));
      $firstDay = new DateTime(sprintf('%04d-%02d-01', $currentYear, $currentMonth));
      $startWeekday = (int)$firstDay->format('N'); // 1=Mon
      $daysInMonth = (int)$firstDay->format('t');

      $eventsByDate = [];
      foreach ($schedules as $s) {
          $start = $s['start_date'];
          if (!$start) continue;
          if (!isset($eventsByDate[$start])) $eventsByDate[$start] = [];
          $eventsByDate[$start][] = $s;
      }
    ?>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <?php
        $prev = (clone $firstDay)->modify('-1 month');
        $next = (clone $firstDay)->modify('+1 month');
      ?>
            <a class="btn btn-sm btn-outline-secondary"
                href="<?= BASE_URL ?>?r=schedules_calendar&year=<?= $prev->format('Y') ?>&month=<?= $prev->format('m') ?>"><i
                    class="fas fa-chevron-left"></i></a>
            <h5 class="mb-0">Tháng <?= $firstDay->format('m / Y') ?></h5>
            <a class="btn btn-sm btn-outline-secondary"
                href="<?= BASE_URL ?>?r=schedules_calendar&year=<?= $next->format('Y') ?>&month=<?= $next->format('m') ?>"><i
                    class="fas fa-chevron-right"></i></a>
        </div>

        <div class="calendar-grid mb-2 fw-semibold text-center">
            <div>Th 2</div>
            <div>Th 3</div>
            <div>Th 4</div>
            <div>Th 5</div>
            <div>Th 6</div>
            <div>Th 7</div>
            <div>CN</div>
        </div>

        <div class="calendar-grid">
            <?php
        for ($i=1; $i<$startWeekday; $i++) {
            echo '<div class="cal-cell bg-light"></div>';
        }
        for ($day=1; $day<=$daysInMonth; $day++) {
            $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
            echo '<div class="cal-cell">';
            echo '<div class="date">'.$day.'</div>';
            if (!empty($eventsByDate[$dateStr])) {
                foreach ($eventsByDate[$dateStr] as $ev) {
                    $title = htmlspecialchars($ev['tour_title'] ?? ('#'.$ev['tour_id']), ENT_QUOTES, 'UTF-8');
                    $guide = htmlspecialchars($ev['guide_name'] ?? 'HDV chưa gán', ENT_QUOTES, 'UTF-8');
                    echo '<span class="event-badge">'.$title.'<small>'.$guide.'</small></span>';
                }
            }
            echo '</div>';
        }
      ?>
        </div>
    </main>
</body>

</html>