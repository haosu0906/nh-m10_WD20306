<?php
require_once 'models/BookingModel.php';
require_once 'models/TourModel.php';

class BookingController {
    private $bookingModel;
    private $tourModel;
    
    public function __construct() {
        $this->bookingModel = new BookingModel();
        $this->tourModel = new TourModel();
    }
    
    public function index() {
        $statusFilter = $_GET['status'] ?? null;
        $bookings = $this->bookingModel->all($statusFilter);
        include 'views/bookings/index.php';
    }
    
    public function create() {
        $tours = $this->tourModel->all();
        include 'views/bookings/create.php';
    }
    
    public function store() {
        $errors = [];
        
        // Validate tour
        if (empty($_POST['tour_id'])) {
            $errors[] = 'Vui lòng chọn tour';
        }
        
        // Validate ngày khởi hành
        if (empty($_POST['start_date'])) {
            $errors[] = 'Vui lòng chọn ngày khởi hành';
        }
        
        // Validate số lượng khách
        if (empty($_POST['total_guests']) || $_POST['total_guests'] < 1) {
            $errors[] = 'Số lượng khách không hợp lệ';
        }
        
        // Validate thông tin khách hàng
        if (empty($_POST['customers'])) {
            $errors[] = 'Vui lòng nhập thông tin khách hàng';
        } else {
            foreach ($_POST['customers'] as $index => $customer) {
                if (empty($customer['full_name'])) {
                    $errors[] = "Vui lòng nhập tên khách hàng " . ($index + 1);
                }
                if (empty($customer['phone'])) {
                    $errors[] = "Vui lòng nhập số điện thoại khách hàng " . ($index + 1);
                }
            }
        }
        
        // Kiểm tra số chỗ trống
        if (empty($errors)) {
            if (!$this->bookingModel->checkAvailableSeats($_POST['tour_id'], $_POST['total_guests'])) {
                $errors[] = 'Tour không đủ chỗ trống cho số lượng khách yêu cầu';
            }
        }
        
        if (empty($errors)) {
            // Lấy giá tour
            $tour = $this->tourModel->find($_POST['tour_id']);
            $totalPrice = $tour['price'] * $_POST['total_guests'];
            
            // Tạo booking
            $bookingId = $this->bookingModel->create([
                'tour_id' => $_POST['tour_id'],
                'total_guests' => $_POST['total_guests'],
                'booking_status' => 'pending',
                'total_price' => $totalPrice,
                'start_date' => $_POST['start_date'],
                'customers' => $_POST['customers']
            ]);
            
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        
        // Nếu có lỗi, hiển thị lại form
        $tours = $this->tourModel->all();
        include 'views/bookings/create.php';
    }
    
    public function updateStatus() {
        if (empty($_GET['id']) || empty($_GET['status'])) {
            die('Thiếu thông tin yêu cầu');
        }
        
        $allowedStatuses = ['pending', 'deposit', 'completed', 'canceled'];
        if (!in_array($_GET['status'], $allowedStatuses)) {
            die('Trạng thái không hợp lệ');
        }
        
        $this->bookingModel->updateStatus(
            $_GET['id'], 
            $_GET['status'],
            $_SESSION['user_id'] ?? null,
            $_GET['note'] ?? null
        );
        
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    public function detail() {
        $id = $_GET['id'] ?? 0;
        $booking = $this->bookingModel->find($id);
        $guests = $this->bookingModel->getBookingGuests($id);
        $statusHistory = $this->bookingModel->getStatusHistory($id);
        
        include 'views/bookings/detail.php';
    }
}
