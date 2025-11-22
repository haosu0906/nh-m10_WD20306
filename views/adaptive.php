<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hypmatic - Quản lý Tour Du lịch</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/adaptive.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background-color: #2c3e50;
            color: white;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
        }
        
        .sidebar h3 {
            color: white;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: 0.3s;
        }
        
        .nav-link:hover,
        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .dashboard-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .card-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .stat-number {
            font-weight: 700;
            margin: 10px 0;
        }
        
        .tour-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-upcoming {
            background-color: #ffc107;
            color: #333;
        }
        
        .status-ongoing {
            background-color: #28a745;
            color: white;
        }
        
        .status-finished {
            background-color: #6c757d;
            color: white;
        }
        
        .table-actions {
            white-space: nowrap;
        }
        
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: 0.2s;
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item.unread {
            background-color: #e7f3ff;
            border-left: 3px solid #007bff;
        }
        
        .notification-time {
            color: #6c757d;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                min-height: auto;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4 px-3">
                        <h3><i class="fas fa-map-marked-alt"></i> Hypmatic</h3>
                        <p class="text-muted">Quản lý Tour Du lịch</p>
                    </div>
                    
                    <ul class="nav flex-column px-2">
                        <li class="nav-item">
                            <a class="nav-link active" href="#dashboard">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tours">
                                <i class="fas fa-map me-2"></i> Quản lý Tour
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#bookings">
                                <i class="fas fa-calendar-check me-2"></i> Đặt Tour
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#users">
                                <i class="fas fa-users me-2"></i> Người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#notifications">
                                <i class="fas fa-bell me-2"></i> Thông báo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#itineraries">
                                <i class="fas fa-route me-2"></i> Lịch trình
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#status-history">
                                <i class="fas fa-history me-2"></i> Lịch sử trạng thái
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#blogs">
                                <i class="fas fa-blog me-2"></i> Bài viết
                            </a>
                        </li>
                    </ul>
                    
                    <div class="mt-4 mx-2 p-3 bg-dark rounded">
                        <h6 class="text-white">Thống kê hệ thống</h6>
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Tours: 24</span>
                            <span>Đặt tour: 156</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-2">
                            <span>Người dùng: 89</span>
                            <span>Thông báo: 12</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group" style="width: 300px;">
                            <input type="text" class="form-control" placeholder="Tìm kiếm...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-2"></i> Admin
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Cài đặt</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Dashboard Stats -->
                <div class="row mb-4 g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="card dashboard-card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Tổng Tour</h5>
                                        <h2 class="stat-number">24</h2>
                                    </div>
                                    <div class="card-icon">
                                        <i class="fas fa-map"></i>
                                    </div>
                                </div>
                                <p class="card-text">+2 so với tháng trước</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card dashboard-card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Đặt Tour</h5>
                                        <h2 class="stat-number">156</h2>
                                    </div>
                                    <div class="card-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                </div>
                                <p class="card-text">+15 so với tháng trước</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card dashboard-card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Người dùng</h5>
                                        <h2 class="stat-number">89</h2>
                                    </div>
                                    <div class="card-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <p class="card-text">+8 so với tháng trước</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card dashboard-card text-white bg-danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Doanh thu</h5>
                                        <h2 class="stat-number">$24,580</h2>
                                    </div>
                                    <div class="card-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                                <p class="card-text">+12% so với tháng trước</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Tours -->
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Tour gần đây</h5>
                                <a href="#tours" class="btn btn-sm btn-primary">Xem tất cả</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Hình ảnh</th>
                                                <th>Tên Tour</th>
                                                <th>Điểm đến</th>
                                                <th>Ngày bắt đầu</th>
                                                <th>Giá</th>
                                                <th>Trạng thái</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><img src="https://via.placeholder.com/80x60?text=Tour1" alt="Tour image" class="tour-image"></td>
                                                <td>Khám phá Sapa</td>
                                                <td>Sapa, Lào Cai</td>
                                                <td>15/06/2023</td>
                                                <td>$350</td>
                                                <td><span class="status-badge status-upcoming">Sắp diễn ra</span></td>
                                                <td class="table-actions">
                                                    <button class="btn btn-sm btn-outline-primary" title="Chỉnh sửa"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://via.placeholder.com/80x60?text=Tour2" alt="Tour image" class="tour-image"></td>
                                                <td>Hạ Long kỳ vĩ</td>
                                                <td>Vịnh Hạ Long, Quảng Ninh</td>
                                                <td>10/06/2023</td>
                                                <td>$280</td>
                                                <td><span class="status-badge status-ongoing">Đang diễn ra</span></td>
                                                <td class="table-actions">
                                                    <button class="btn btn-sm btn-outline-primary" title="Chỉnh sửa"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://via.placeholder.com/80x60?text=Tour3" alt="Tour image" class="tour-image"></td>
                                                <td>Phố cổ Hội An</td>
                                                <td>Hội An, Quảng Nam</td>
                                                <td>05/06/2023</td>
                                                <td>$220</td>
                                                <td><span class="status-badge status-finished">Đã kết thúc</span></td>
                                                <td class="table-actions">
                                                    <button class="btn btn-sm btn-outline-primary" title="Chỉnh sửa"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Notifications -->
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Thông báo gần đây</h5>
                                <a href="#notifications" class="btn btn-sm btn-primary">Xem tất cả</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="notification-item">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">Đặt tour mới</h6>
                                        <small class="notification-time">2 giờ trước</small>
                                    </div>
                                    <p class="mb-0">Nguyễn Văn A đã đặt tour Khám phá Sapa</p>
                                </div>
                                <div class="notification-item unread">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">Thanh toán thành công</h6>
                                        <small class="notification-time">5 giờ trước</small>
                                    </div>
                                    <p class="mb-0">Trần Thị B đã thanh toán tour Hạ Long kỳ vĩ</p>
                                </div>
                                <div class="notification-item">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">Hủy tour</h6>
                                        <small class="notification-time">1 ngày trước</small>
                                    </div>
                                    <p class="mb-0">Lê Văn C đã hủy tour Phố cổ Hội An</p>
                                </div>
                                <div class="notification-item">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">Đánh giá mới</h6>
                                        <small class="notification-time">2 ngày trước</small>
                                    </div>
                                    <p class="mb-0">Phạm Thị D đã đánh giá 5 sao cho tour Sapa</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Thao tác nhanh</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary"><i class="fas fa-plus me-2"></i> Thêm Tour mới</button>
                                    <button class="btn btn-outline-primary"><i class="fas fa-user-plus me-2"></i> Thêm Người dùng</button>
                                    <button class="btn btn-outline-primary"><i class="fas fa-bell me-2"></i> Gửi Thông báo</button>
                                    <button class="btn btn-outline-primary"><i class="fas fa-chart-bar me-2"></i> Xem Báo cáo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>