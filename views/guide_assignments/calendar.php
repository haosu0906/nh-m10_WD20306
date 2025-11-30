<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Lịch Phân công HDV</title>
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

    .calendar-container {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .calendar-header {
        background: var(--accent);
        color: white;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }

    .calendar-weekday {
        background: #f8f9fa;
        padding: 0.75rem;
        text-align: center;
        font-weight: 600;
        border-right: 1px solid #dee2e6;
    }

    .calendar-day {
        min-height: 100px;
        padding: 0.5rem;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        position: relative;
    }

    .calendar-day.other-month {
        background: #f8f9fa;
        color: #6c757d;
    }

    .calendar-day.today {
        background: #e7f3ff;
    }

    .day-number {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .assignment-item {
        font-size: 0.75rem;
        padding: 0.25rem;
        margin-bottom: 0.25rem;
        border-radius: 4px;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .assignment-primary {
        background: #d4edda;
        color: #155724;
    }

    .assignment-secondary {
        background: #cce5ff;
        color: #004085;
    }

    .assignment-assistant {
        background: #e2e3e5;
        color: #383d41;
    }

    .month-nav {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .month-nav a {
        color: white;
        text-decoration: none;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .month-nav a:hover {
        background: rgba(255,255,255,0.2);
    }

    .legend {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 4px;
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
        border-radius: 4px;
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-layer-group"></i> Danh mục tour</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=payments"><i class="fas fa-credit-card"></i> Thanh toán</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=cancellation_policies"><i class="fas fa-ban"></i> Chính sách hủy</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
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
                <h3>Lịch Phân công HDV</h3>
                <p class="text-muted mb-0">Xem lịch phân công hướng dẫn viên theo tháng</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>?r=guide_assignments" class="btn btn-outline-secondary">
                    <i class="fas fa-list"></i> Danh sách
                </a>
                <a href="<?= BASE_URL ?>?r=guide_assignments_create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm phân công
                </a>
            </div>
        </div>

        <div class="calendar-container">
            <div class="calendar-header">
                <div class="month-nav">
                    <a href="<?= BASE_URL ?>?r=guide_assignments_calendar&month=<?= $month == 1 ? 12 : $month - 1 ?>&year=<?= $month == 1 ? $year - 1 : $year ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <h4 class="mb-0"><?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?></h4>
                    <a href="<?= BASE_URL ?>?r=guide_assignments_calendar&month=<?= $month == 12 ? 1 : $month + 1 ?>&year=<?= $month == 12 ? $year + 1 : $year ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <a href="<?= BASE_URL ?>?r=guide_assignments_calendar&month=<?= date('n') ?>&year=<?= date('Y') ?>" class="btn btn-sm btn-light">
                    <i class="fas fa-calendar-day"></i> Tháng hiện tại
                </a>
            </div>

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
                $startWeekday = date('w', $firstDay);
                
                // Previous month days
                $prevMonth = $month == 1 ? 12 : $month - 1;
                $prevYear = $month == 1 ? $year - 1 : $year;
                $daysInPrevMonth = date('t', mktime(0, 0, 0, $prevMonth, 1, $prevYear));
                
                // Next month days
                $nextMonth = $month == 12 ? 1 : $month + 1;
                $nextYear = $month == 12 ? $year + 1 : $year;

                // Group assignments by date
                $assignmentsByDate = [];
                foreach ($assignments as $assignment) {
                    $startDate = new DateTime($assignment['start_date']);
                    $endDate = new DateTime($assignment['end_date']);
                    
                    $current = clone $startDate;
                    while ($current <= $endDate) {
                        $dateKey = $current->format('Y-m-d');
                        if (!isset($assignmentsByDate[$dateKey])) {
                            $assignmentsByDate[$dateKey] = [];
                        }
                        $assignmentsByDate[$dateKey][] = $assignment;
                        $current->add(new DateInterval('P1D'));
                    }
                }

                // Previous month filler
                for ($i = $startWeekday - 1; $i >= 0; $i--) {
                    $day = $daysInPrevMonth - $i;
                    $dateKey = sprintf('%04d-%02d-%02d', $prevYear, $prevMonth, $day);
                    echo '<div class="calendar-day other-month">';
                    echo '<div class="day-number">' . $day . '</div>';
                    $this->renderDayAssignments($assignmentsByDate[$dateKey] ?? []);
                    echo '</div>';
                }

                // Current month days
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $isToday = ($year == date('Y') && $month == date('n') && $day == date('j'));
                    
                    echo '<div class="calendar-day' . ($isToday ? ' today' : '') . '">';
                    echo '<div class="day-number">' . $day . '</div>';
                    $this->renderDayAssignments($assignmentsByDate[$dateKey] ?? []);
                    echo '</div>';
                }

                // Next month filler
                $totalCells = $startWeekday + $daysInMonth;
                $nextMonthDays = $totalCells % 7 == 0 ? 0 : 7 - ($totalCells % 7);
                for ($day = 1; $day <= $nextMonthDays; $day++) {
                    $dateKey = sprintf('%04d-%02d-%02d', $nextYear, $nextMonth, $day);
                    echo '<div class="calendar-day other-month">';
                    echo '<div class="day-number">' . $day . '</div>';
                    $this->renderDayAssignments($assignmentsByDate[$dateKey] ?? []);
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <div class="legend">
            <div class="legend-item">
                <div class="legend-color assignment-primary"></div>
                <span>HDV Chính</span>
            </div>
            <div class="legend-item">
                <div class="legend-color assignment-secondary"></div>
                <span>HDV Phụ</span>
            </div>
            <div class="legend-item">
                <div class="legend-color assignment-assistant"></div>
                <span>HDV Trợ giúp</span>
            </div>
        </div>
    </main>

    <?php
    // Helper function to render assignments for a day
    function renderDayAssignments($dayAssignments) {
        if (empty($dayAssignments)) return;
        
        foreach ($dayAssignments as $assignment) {
            $type = $assignment['assignment_type'] ?? 'primary';
            $class = 'assignment-' . $type;
            $guideName = htmlspecialchars($assignment['guide_name'] ?? 'Unknown');
            $tourTitle = htmlspecialchars($assignment['tour_title'] ?? 'Tour');
            
            echo '<div class="assignment-item ' . $class . '" title="' . $guideName . ' - ' . $tourTitle . '">';
            echo $guideName;
            echo '</div>';
        }
    }
    ?>

    <script>
    // Add click handlers for assignment items
    document.addEventListener('DOMContentLoaded', function() {
        const assignmentItems = document.querySelectorAll('.assignment-item');
        assignmentItems.forEach(item => {
            item.addEventListener('click', function() {
                // Could open modal with assignment details
                console.log('Assignment clicked:', this.title);
            });
        });
    });
    </script>
</body>

</html>
