<!-- Modern Sidebar Template -->
<div class="sidebar">
    <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
    <nav class="nav flex-column">
        <a class="nav-link <?= $current_page === 'home' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
        <a class="nav-link <?= $current_page === 'tour_categories' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
        <a class="nav-link <?= $current_page === 'tours' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
        <a class="nav-link <?= $current_page === 'suppliers' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
        <a class="nav-link <?= $current_page === 'booking' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
        <a class="nav-link <?= $current_page === 'schedules' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
        <a class="nav-link <?= $current_page === 'staff' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
        <a class="nav-link <?= $current_page === 'guides' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
        <a class="nav-link <?= $current_page === 'guide_assignments' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
        <a class="nav-link <?= $current_page === 'guide_schedules' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=guide_schedules"><i class="fas fa-calendar-alt"></i> Lịch HDV</a>
        <a class="nav-link <?= $current_page === 'guide_ratings' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=guide_ratings"><i class="fas fa-star"></i> Đánh giá HDV</a>
        <a class="nav-link <?= $current_page === 'guide_login' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=guide_login">
            <i class="fas fa-door-open"></i> Portal HDV
        </a>
        <a class="nav-link <?= $current_page === 'admin_login' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=admin_login">
            <i class="fas fa-user-shield"></i> Đăng nhập Admin
        </a>
    </nav>
</div>
