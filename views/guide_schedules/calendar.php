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
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <main class="main">
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

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 bg-gradient-primary text-white slide-in-left">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Tổng HDV</h6>
                                <h3 class="mb-0"><?= count($guides ?? []) ?></h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-arrow-up me-1"></i>
                                +2 HDV mới
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-gradient-success text-white slide-in-left" style="animation-delay: 0.1s;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Đang làm việc</h6>
                                <h3 class="mb-0">8</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-briefcase fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-check me-1"></i>
                                Hoạt động
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-gradient-warning text-white slide-in-left" style="animation-delay: 0.2s;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Rảnh rỗi</h6>
                                <h3 class="mb-0">5</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Sẵn sàng
                            </small>
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
                        <div class="alert alert-info mb-0">
                            <div class="d-flex align-items-center">
                                <div class="icon-box-primary me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user-tie text-info"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($guideInfo['full_name']) ?></div>
                                    <small class="text-muted">Email: <?= htmlspecialchars($guideInfo['email'] ?? '') ?> | SĐT: <?= htmlspecialchars($guideInfo['phone'] ?? '') ?></small>
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
    </main>
</body>
</html>
