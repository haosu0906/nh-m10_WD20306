<aside class="position-fixed top-0 start-0 bottom-0 d-flex flex-column flex-shrink-0 p-3 text-white" style="width:260px;z-index:1000;background:#1e293b;">
    <div class="d-flex align-items-center mb-3">
        <i class="fas fa-map-marked-alt me-2"></i>
        <span class="fw-semibold">Quản trị TripMate</span>
    </div>
    <nav class="nav nav-pills flex-column mb-auto">
        <a class="nav-link text-white <?= $current_page === 'home' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt me-2"></i> Tổng quan</a>
        <a class="nav-link text-white <?= $current_page === 'tour_categories' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map me-2"></i> Danh mục tour</a>
        <a class="nav-link text-white <?= $current_page === 'tours' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route me-2"></i> Tours</a>
        <a class="nav-link text-white <?= $current_page === 'suppliers' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake me-2"></i> Nhà cung cấp</a>
        <a class="nav-link text-white <?= $current_page === 'booking' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book me-2"></i> Booking</a>
        <a class="nav-link text-white <?= $current_page === 'schedules' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar me-2"></i> Lịch khởi hành</a>
        <a class="nav-link text-white <?= $current_page === 'guides' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie me-2"></i> HDV</a>
        <a class="nav-link text-white <?= $current_page === 'guide_assignments' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check me-2"></i> Phân công HDV</a>
        <a class="nav-link text-white <?= $current_page === 'guide_schedules' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=guide_schedules"><i class="fas fa-calendar-alt me-2"></i> Lịch HDV</a>
        <a class="nav-link text-white <?= $current_page === 'guide_ratings' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=guide_ratings"><i class="fas fa-star me-2"></i> Đánh giá HDV</a>
        <a class="nav-link text-white <?= $current_page === 'guide_login' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=guide_login"><i class="fas fa-door-open me-2"></i> Portal HDV</a>
        <a class="nav-link text-white <?= $current_page === 'admin_login' ? 'active' : '' ?>" href="<?= BASE_URL ?>?r=admin_login"><i class="fas fa-user-shield me-2"></i> Đăng nhập Admin</a>
    </nav>
</aside>
