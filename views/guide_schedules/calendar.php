<?php
require_once __DIR__ . '/../../assets/configs/env.php';
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Lịch HDV - Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
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

    .main-content {}

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background: #dee2e6;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .calendar-weekday {
        background: #f8f9fa;
        padding: 0.75rem;
        text-align: center;
        font-weight: 600;
        font-size: 0.875rem;
        color: #6c757d;
        border-bottom: 1px solid #dee2e6;
    }

    .calendar-day {
        background: white;
        min-height: 100px;
        padding: 0.5rem;
        position: relative;
        transition: all 0.2s ease;
    }

    .calendar-day:hover {
        background: #f8f9fa;
        z-index: 1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .calendar-day.today {
        background: #e7f3ff;
    }

    .calendar-day.other-month {
        background: #f8f9fa;
        color: #adb5bd;
    }

    .day-number {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .schedule-item {
        font-size: 0.75rem;
        padding: 0.125rem 0.25rem;
        margin: 0.125rem 0;
        border-radius: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .schedule-tour {
        background: #d1ecf1;
        color: #0c5460;
    }

    .schedule-available {
        background: #d4edda;
        color: #155724;
    }

    .schedule-busy {
        background: #f8d7da;
        color: #721c24;
    }

    .schedule-on-leave {
        background: #fff3cd;
        color: #856404;
    }

    .legend {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 0.25rem;
    }

    .status-available { background: #d4edda; }
    .status-busy { background: #f8d7da; }
    .status-on_leave { background: #fff3cd; }
    .status-unavailable { background: #e2e3e5; }
    </style>
</head>

<body>
    <?php $current_page='guide_schedules'; require_once __DIR__ . '/../../assets/templates/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h1 class="mb-2">📅 Lịch HDV</h1>
                <p class="text-muted mb-0">Quản lý lịch làm việc của HDV</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>?r=guide_assignments" class="btn btn-outline-secondary">
                    <i class="fas fa-user-check me-2"></i>Phân công
                </a>
                <a href="<?= BASE_URL ?>?r=guide_schedules_create" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Thêm lịch
                </a>
            </div>
        </div>

        <?php $activeCount=0; $availableCount=0; foreach(($schedules ?? []) as $sc){ $st=strtolower((string)($sc['status'] ?? '')); if($st==='available'){ $availableCount++; } if($st==='busy' || !empty($sc['tour_title'])){ $activeCount++; } } ?>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100" style="border-left:4px solid #6f42c1;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light p-3" style="color:#6f42c1;">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted">Tổng HDV</div>
                            <div class="h4 mb-0" style="color:#6f42c1;"><?= number_format(count($guides ?? [])) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100" style="border-left:4px solid #198754;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light p-3" style="color:#198754;">
                            <i class="fas fa-briefcase fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted">Đang làm việc</div>
                            <div class="h4 mb-0" style="color:#198754;"><?= number_format($activeCount) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100" style="border-left:4px solid #fd7e14;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light p-3" style="color:#fd7e14;">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted">Sẵn sàng</div>
                            <div class="h4 mb-0" style="color:#fd7e14;"><?= number_format($availableCount) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guide Selector -->
        <div class="card border-0 shadow-sm mb-4 fade-in">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">👤 Chọn HDV</h6>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">HDV</label>
                        <select name="guide_id" class="form-select" onchange="window.location.href='?r=guide_schedules&guide_id=' + this.value + '&month=<?= $month ?>&year=<?= $year ?>'">
                            <?php foreach ($guides as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= ($guideId == $g['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['full_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if ($guideId > 0 && !empty($guideInfo)): ?>
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <?php $avatar = $guideInfo['avatar'] ?? ''; ?>
                                    <?php if (!empty($avatar)): ?>
                                        <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-light p-3" style="color:#0d6efd;">
                                            <i class="fas fa-id-badge"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold fs-6"><?= htmlspecialchars($guideInfo['full_name']) ?></div>
                                        <div class="text-muted d-flex flex-wrap gap-3">
                                            <span><i class="fas fa-phone me-1"></i><?= htmlspecialchars($guideInfo['phone'] ?? '—') ?></span>
                                            <span><i class="fas fa-envelope me-1"></i><?= htmlspecialchars($guideInfo['email'] ?? '—') ?></span>
                                            <span><i class="fas fa-globe me-1"></i><?= htmlspecialchars($guideInfo['guide_type'] ?? '—') ?></span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="<?= BASE_URL ?>?r=guide_schedules_create&guide_id=<?= (int)$guideId ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Thêm lịch</a>
                                        <a href="<?= BASE_URL ?>?r=guides" class="btn btn-outline-secondary btn-sm"><i class="fas fa-user-tie me-1"></i>DS HDV</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($guideId > 0): ?>
        <div class="card border-0 shadow-sm fade-in">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="month-nav">
                        <a href="<?= BASE_URL ?>?r=guide_schedules&guide_id=<?= $guideId ?>&month=<?= $month == 1 ? 12 : $month - 1 ?>&year=<?= $month == 1 ? $year - 1 : $year ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <h4 class="mb-0 mx-3"><?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?></h4>
                        <a href="<?= BASE_URL ?>?r=guide_schedules&guide_id=<?= $guideId ?>&month=<?= $month == 12 ? 1 : $month + 1 ?>&year=<?= $month == 12 ? $year + 1 : $year ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                    <a href="<?= BASE_URL ?>?r=guide_schedules&guide_id=<?= $guideId ?>&month=<?= date('n') ?>&year=<?= date('Y') ?>" class="btn btn-sm btn-secondary">
                        <i class="fas fa-calendar-day me-2"></i>Tháng hiện tại
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="calendar-grid">
                    <!-- Days of week -->
                    <div class="calendar-weekday">CN</div>
                    <div class="calendar-weekday">T2</div>
                    <div class="calendar-weekday">T3</div>
                    <div class="calendar-weekday">T4</div>
                    <div class="calendar-weekday">T5</div>
                    <div class="calendar-weekday">T6</div>
                    <div class="calendar-weekday">T7</div>

                    <?php
                    // Calendar logic
                    $firstDay = mktime(0, 0, 0, $month, 1, $year);
                    $daysInMonth = date('t', $firstDay);
                    $startWeekday = (int)date('w', $firstDay);
                    
                    // Previous month filler
                    $prevMonth = $month == 1 ? 12 : $month - 1;
                    $prevYear = $month == 1 ? $year - 1 : $year;
                    $prevDaysInMonth = date('t', mktime(0, 0, 0, $prevMonth, 1, $prevYear));
                    
                    // Next month
                    $nextMonth = $month == 12 ? 1 : $month + 1;
                    $nextYear = $month == 12 ? $year + 1 : $year;

                    // Group schedules by date
                    $schedulesByDate = [];
                    foreach ($schedules as $schedule) {
                        $date = date('Y-m-d', strtotime($schedule['schedule_date']));
                        $schedulesByDate[$date] = $schedule;
                    }

                    // Previous month filler
                    for ($day = $prevDaysInMonth - $startWeekday + 1; $day <= $prevDaysInMonth; $day++) {
                        $dateKey = sprintf('%04d-%02d-%02d', $prevYear, $prevMonth, $day);
                        echo '<div class="calendar-day other-month">';
                        echo '<div class="day-number">' . $day . '</div>';
                        if (isset($schedulesByDate[$dateKey])) {
                            $schedule = $schedulesByDate[$dateKey];
                            echo '<div class="schedule-item schedule-' . $schedule['status'] . '">';
                            echo htmlspecialchars($schedule['tour_title'] ?? 'Lịch làm việc');
                            echo '</div>';
                        }
                        echo '</div>';
                    }

                    // Current month days
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $isToday = ($year == date('Y') && $month == date('n') && $day == date('j'));
                        
                        echo '<div class="calendar-day' . ($isToday ? ' today' : '') . '">';
                        echo '<div class="day-number">' . $day . '</div>';
                        if (isset($schedulesByDate[$dateKey])) {
                            $schedule = $schedulesByDate[$dateKey];
                            echo '<div class="schedule-item schedule-tour">';
                            echo htmlspecialchars($schedule['tour_title'] ?? 'Lịch làm việc');
                            echo '</div>';
                        }
                        echo '</div>';
                    }

                    // Next month filler
                    $totalCells = $startWeekday + $daysInMonth;
                    $nextMonthDays = $totalCells % 7 == 0 ? 0 : 7 - ($totalCells % 7);
                    for ($day = 1; $day <= $nextMonthDays; $day++) {
                        $dateKey = sprintf('%04d-%02d-%02d', $nextYear, $nextMonth, $day);
                        echo '<div class="calendar-day other-month">';
                        echo '<div class="day-number">' . $day . '</div>';
                        if (isset($schedulesByDate[$dateKey])) {
                            $schedule = $schedulesByDate[$dateKey];
                            echo '<div class="schedule-item schedule-' . $schedule['status'] . '">';
                            echo htmlspecialchars($schedule['tour_title'] ?? 'Lịch làm việc');
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="card border-0 shadow-sm fade-in mt-4">
            <div class="card-body">
                <h6 class="mb-3">📖 Chú thích</h6>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color status-available"></div>
                        <span>Sẵn sàng</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color schedule-tour"></div>
                        <span>Đang làm tour</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color status-on_leave"></div>
                        <span>Nghỉ phép</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color status-unavailable"></div>
                        <span>Không khả dụng</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
