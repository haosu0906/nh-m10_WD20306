<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Quản trị — Hệ thống Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: Inter, Segoe UI, Arial;
        background: #f5f7fb;
        margin: 0;
        color: #222;
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
        overflow: auto;
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
        color: #fff;
    }

    .main {
        margin-left: 200px;
        padding: 22px;
    }

    .card {
        box-shadow: 0 6px 18px rgba(20, 20, 30, .06);
    }

    @media (max-width:900px) {
        .sidebar {
            position: relative;
            width: 100%;
        }

        .main {
            margin-left: 0;
        }
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
        <nav class="nav flex-column">
            <a class="nav-link active" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=suppliers"><i class="fas fa-handshake"></i> Nhà cung cấp</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=booking"><i class="fas fa-book"></i> Booking</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=schedules"><i class="fas fa-calendar"></i> Lịch khởi hành</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=guide_assignments"><i class="fas fa-user-check"></i> Phân công HDV</a>
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
        <?php if(function_exists('flash_get')): $flash = flash_get(); if($flash): ?>
        <div class="container mb-3">
            <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info') ?>">
                <?= htmlspecialchars($flash['message'] ?? '') ?>
            </div>
        </div>
        <?php endif; endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h1 class="mb-2">🏠 Bảng Quản Trị Tripmate</h1>
                <p class="text-muted mb-0">Chào mừng đến với hệ thống quản lý tour hiện đại</p>
            </div>
            <div class="text-end">
                <div class="badge bg-success px-3 py-2">
                    <i class="fas fa-circle text-success me-2" style="font-size: 0.5rem;"></i>
                    Hệ thống hoạt động
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 bg-gradient-primary text-white slide-in-left">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Tổng Tours</h6>
                                <h3 class="mb-0">24</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-route fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-arrow-up me-1"></i>
                                +12% so với tháng trước
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 bg-gradient-success text-white slide-in-left" style="animation-delay: 0.1s;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Đặt Tour</h6>
                                <h3 class="mb-0">156</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-book fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-arrow-up me-1"></i>
                                +8% so với tháng trước
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 bg-gradient-info text-white slide-in-left" style="animation-delay: 0.2s;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">HDV</h6>
                                <h3 class="mb-0">18</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-user-tie fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-arrow-up me-1"></i>
                                +3 HDV mới
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 bg-gradient-warning text-white slide-in-left" style="animation-delay: 0.3s;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Doanh Thu</h6>
                                <h3 class="mb-0">₫124M</h3>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="opacity-75">
                                <i class="fas fa-arrow-up me-1"></i>
                                +15% so với tháng trước
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Module Cards -->
        <div class="card border-0 shadow-sm mb-4 fade-in">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">📋 Mô-đun Quản Lý</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-map fa-3x text-primary"></i>
                                </div>
                                <h6 class="card-title">Danh mục tour</h6>
                                <p class="card-text text-muted small">Tạo và phân loại tour</p>
                                <a href="<?= BASE_URL ?>?r=tour_categories" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-route fa-3x text-info"></i>
                                </div>
                                <h6 class="card-title">Tours</h6>
                                <p class="card-text text-muted small">Quản lý thông tin chi tiết tour</p>
                                <a href="<?= BASE_URL ?>?r=tours" class="btn btn-info btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-handshake fa-3x text-success"></i>
                                </div>
                                <h6 class="card-title">Nhà cung cấp</h6>
                                <p class="card-text text-muted small">Quản lý đối tác dịch vụ</p>
                                <a href="<?= BASE_URL ?>?r=suppliers" class="btn btn-success btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-book fa-3x text-warning"></i>
                                </div>
                                <h6 class="card-title">Booking</h6>
                                <p class="card-text text-muted small">Quản lý đặt tour của khách</p>
                                <a href="<?= BASE_URL ?>?r=booking" class="btn btn-warning btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-user-tie fa-3x text-secondary"></i>
                                </div>
                                <h6 class="card-title">HDV</h6>
                                <p class="card-text text-muted small">Quản lý HDV</p>
                                <a href="<?= BASE_URL ?>?r=guides" class="btn btn-secondary btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-calendar fa-3x text-primary"></i>
                                </div>
                                <h6 class="card-title">Lịch khởi hành</h6>
                                <p class="card-text text-muted small">Tạo lịch và gán HDV</p>
                                <a href="<?= BASE_URL ?>?r=schedules" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-users fa-3x text-info"></i>
                                </div>
                                <h6 class="card-title">Nhân Sự</h6>
                                <p class="card-text text-muted small">Quản lý nhân sự</p>
                                <a href="<?= BASE_URL ?>?r=staff" class="btn btn-info btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-credit-card fa-3x text-success"></i>
                                </div>
                                <h6 class="card-title">Thanh toán</h6>
                                <p class="card-text text-muted small">Quản lý thanh toán booking</p>
                                <a href="<?= BASE_URL ?>?r=payments" class="btn btn-success btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guide Management Section -->
        <div class="card border-0 shadow-sm fade-in">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">👔 Quản Lý HDV</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-ban fa-3x text-danger"></i>
                                </div>
                                <h6 class="card-title">Chính sách hủy</h6>
                                <p class="card-text text-muted small">Thiết lập điều kiện hoàn tiền</p>
                                <a href="<?= BASE_URL ?>?r=cancellation_policies" class="btn btn-danger btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-user-check fa-3x text-primary"></i>
                                </div>
                                <h6 class="card-title">Phân công HDV</h6>
                                <p class="card-text text-muted small">Quản lý phân công HDV</p>
                                <a href="<?= BASE_URL ?>?r=guide_assignments" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-calendar-alt fa-3x text-info"></i>
                                </div>
                                <h6 class="card-title">Lịch HDV</h6>
                                <p class="card-text text-muted small">Lịch làm việc và nghỉ phép</p>
                                <a href="<?= BASE_URL ?>?r=guide_schedules" class="btn btn-info btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-star fa-3x text-warning"></i>
                                </div>
                                <h6 class="card-title">Đánh giá HDV</h6>
                                <p class="card-text text-muted small">Xem và quản lý đánh giá</p>
                                <a href="<?= BASE_URL ?>?r=guide_ratings" class="btn btn-warning btn-sm mt-2">
                                    <i class="fas fa-arrow-right me-1"></i> Vào
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Portal Section -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-door-open fa-3x text-secondary"></i>
                                </div>
                                <h6 class="card-title">Portal HDV</h6>
                                <p class="card-text text-muted small">Đăng nhập cho HDV</p>
                                <a href="<?= BASE_URL ?>?r=guide_login" class="btn btn-outline-secondary btn-sm mt-2">
                                    <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center p-4">
                                <div class="icon-box-primary mb-3">
                                    <i class="fas fa-user-shield fa-3x text-dark"></i>
                                </div>
                                <h6 class="card-title">Đăng nhập Admin</h6>
                                <p class="card-text text-muted small">Đăng nhập quản trị hệ thống</p>
                                <a href="<?= BASE_URL ?>?r=admin_login" class="btn btn-outline-dark btn-sm mt-2">
                                    <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>