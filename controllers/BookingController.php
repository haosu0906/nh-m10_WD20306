<?php
require_once __DIR__ . '/../models/BookingModel.php';
require_once __DIR__ . '/../models/TourModel.php';

class BookingController {
    private $bookingModel;
    private $tourModel;
    
    public function __construct() {
        $this->bookingModel = new BookingModel();
        $this->tourModel = new TourModel();
    }
    
    public function index() {
        $statusFilter = $_GET['status'] ?? null;
        $items = $this->bookingModel->all($statusFilter);
        include 'views/booking/list_booking.php';
    }
    
    public function create() {
        $tours = $this->tourModel->all();
        include 'views/booking/create_booking.php';
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
        include 'views/booking/create_booking.php';
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
        $item = $this->bookingModel->find($id);
        $guests = $this->bookingModel->getBookingGuests($id);
        $statusHistory = $this->bookingModel->getStatusHistory($id);
        $customer = $this->bookingModel->getCustomerInfo($item['customer_user_id'] ?? 0);
        $total_paid = $this->bookingModel->getTotalPaid($id);
        $payment_history = $this->bookingModel->getPaymentHistory($id);
        $suppliers = $this->bookingModel->getTourSuppliers($item['tour_id'] ?? 0);
        
        include 'views/booking/detail_booking.php';
    }

    // Hủy booking (POST/GET)
    public function cancel() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }

        $ok = $this->bookingModel->updateStatus($id, 'canceled');
        if (function_exists('flash_set')) {
            if ($ok) flash_set('success', 'Booking đã được hủy'); else flash_set('danger', 'Không thể hủy booking');
        }
        // Redirect về trang chi tiết hoặc danh sách
        $redirect = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?r=booking');
        header('Location: ' . $redirect);
        exit;
    }

    // Gửi email tóm tắt booking tới khách hàng
    public function sendEmail() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }

        $item = $this->bookingModel->find($id);
        $customer = $this->bookingModel->getCustomerInfo($item['customer_user_id'] ?? 0);
        $to = $customer['email'] ?? null;

        $sent = false;
        if ($to) {
            $subject = "[Tripmate] Thông tin booking #" . $item['id'];
            $message = "Xin chào " . ($customer['full_name'] ?? '') . "\n\n";
            $message .= "Chi tiết booking:\n";
            $message .= "Tour: " . ($item['tour_name'] ?? '') . "\n";
            $message .= "Ngày đặt: " . ($item['date_booked'] ?? '') . "\n";
            $message .= "Tổng tiền: " . number_format($item['total_price'] ?? 0,0,',','.') . " đ\n\n";
            $message .= "Cám ơn.\n";

            // Try to send mail (may require mail server)
            $sent = @mail($to, $subject, $message, "From: no-reply@tripmate.local\r\n");
        }

        if (function_exists('flash_set')) {
            if ($sent) flash_set('success', 'Email đã được gửi tới khách hàng'); else flash_set('warning', 'Không gửi được email (kiểm tra cấu hình mail trên máy)');
        }

        $redirect = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?r=booking');
        header('Location: ' . $redirect);
        exit;
    }

    // In / Xuất PDF (render printable html; real PDF generation not included)
    public function pdf() {
        $id = $_GET['id'] ?? 0;
        $item = $this->bookingModel->find($id);
        $guests = $this->bookingModel->getBookingGuests($id);
        $customer = $this->bookingModel->getCustomerInfo($item['customer_user_id'] ?? 0);
        $total_paid = $this->bookingModel->getTotalPaid($id);
        $payment_history = $this->bookingModel->getPaymentHistory($id);
        $suppliers = $this->bookingModel->getTourSuppliers($item['tour_id'] ?? 0);

        // Nếu có Dompdf (composer) -> generate PDF, ngược lại render HTML printable
        $vendor = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($vendor)) {
            require_once $vendor;
            if (class_exists('\Dompdf\Dompdf')) {
                $dompdf = new \Dompdf\Dompdf();
                ob_start();
                include __DIR__ . '/../views/booking/booking_pdf.php';
                $html = ob_get_clean();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $dompdf->stream("booking_{$id}.pdf", ["Attachment" => 0]);
                exit;
            }
        }

        // Fallback: render printable HTML
        include 'views/booking/booking_pdf.php';
    }

    // Show edit form
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $item = $this->bookingModel->find($id);
        $tours = $this->tourModel->all();
        include 'views/booking/edit_booking.php';
    }

    // Handle update POST
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?r=booking'); exit;
        }
        $id = $_POST['id'] ?? null;
        if (!$id) { header('Location: ' . BASE_URL . '?r=booking'); exit; }

        $data = [
            'tour_id' => $_POST['tour_id'] ?? null,
            'user_id' => $_POST['user_id'] ?? null,
            'number_of_guests' => $_POST['number_of_guests'] ?? 1,
            'total_amount' => $_POST['total_amount'] ?? 0,
            'status' => $_POST['status'] ?? 'pending',
            'special_requests' => $_POST['special_requests'] ?? ''
        ];

        $ok = $this->bookingModel->update($id, $data);
        if (function_exists('flash_set')) {
            if ($ok) flash_set('success', 'Cập nhật booking thành công'); else flash_set('danger', 'Cập nhật thất bại');
        }

        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . urlencode($id));
        exit;
    }
}
?>