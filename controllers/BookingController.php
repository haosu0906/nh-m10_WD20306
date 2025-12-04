<?php
require_once __DIR__ . '/../models/BookingModel.php';
require_once __DIR__ . '/../models/BookingGuestsModel.php';
require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/ScheduleModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/SupplierModel.php';
require_once __DIR__ . '/../assets/helpers/flash.php';

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
        require 'views/booking/list_booking.php';
    }

    public function create() {
        $scheduleModel = new ScheduleModel();
        $userModel = new UserModel();
        $tourModel = new TourModel();
        $supplierModel = new SupplierModel();

        $schedules = $scheduleModel->all();
        $customers = $userModel->getCustomers();
        $tours = $tourModel->all();

        // Build a map of tour_id -> supplier_name
        $suppliersByTour = [];
        if (!empty($tours)) {
            // Fetch all suppliers first to avoid N+1 queries
            $allSuppliers = $supplierModel->getAll();
            $suppliersMap = [];
            foreach ($allSuppliers as $supplier) {
                $suppliersMap[$supplier['id']] = $supplier['name'];
            }

            foreach ($tours as $tour) {
                if (!empty($tour['supplier_id']) && isset($suppliersMap[$tour['supplier_id']])) {
                    $suppliersByTour[$tour['id']] = $suppliersMap[$tour['supplier_id']];
                } else {
                    $suppliersByTour[$tour['id']] = '';
                }
            }
        }
        
        require 'views/booking/create_booking.php';
    }

    public function store() {
        $scheduleModel = new ScheduleModel();
        $bookingGuestsModel = new BookingGuestsModel();
        $tourModel = new TourModel();

        // Basic validation
        $schedule_id = $_POST['schedule_id'] ?? null;
        $total_guests = filter_input(INPUT_POST, 'total_guests', FILTER_VALIDATE_INT);
        $guest_full_names = $_POST['guest_full_name'] ?? [];

        if (!isset($_SESSION['user_id'])) {
            flash_set('error', 'Vui lòng đăng nhập để tạo booking.');
            // Chuyển hướng đến trang đăng nhập chung hoặc trang admin/sales
            header('Location: ' . BASE_URL . '?r=admin_login'); 
            exit;
        }

        if (!$schedule_id || !$total_guests || $total_guests <= 0 || empty($guest_full_names[0])) {
            flash_set('error', 'Vui lòng điền đầy đủ các thông tin bắt buộc: Lịch trình, số lượng khách và ít nhất một tên khách.');
            header('Location: ' . BASE_URL . '?r=booking_create');
            exit;
        }

        // Check capacity theo schedule (tổng đã đặt và chỗ còn lại)
        $schedule = $scheduleModel->find($schedule_id);
        if (!$schedule) {
            flash_set('error', 'Không tìm thấy lịch khởi hành để tạo booking.');
            header('Location: ' . BASE_URL . '?r=booking_create');
            exit;
        }
        $maxCap = (int)($schedule['max_capacity'] ?? 0);
        $occupied = $this->bookingModel->getOccupiedSeatsBySchedule($schedule_id);
        $available = $maxCap - $occupied;
        if ($available < $total_guests) {
            flash_set('error', 'Không đủ chỗ trống. Còn lại: ' . max(0, $available) . ' chỗ.');
            header('Location: ' . BASE_URL . '?r=booking_create');
            exit;
        }

        // DB Transaction
        $this->bookingModel->beginTransaction();
        try {
            // Get price from tour
            $tour = $tourModel->find($schedule['tour_id']);
            $total_price = ($tour['price'] ?? 0) * $total_guests;

            // 1. Create main booking
            $bookingData = [
                'tour_id' => $schedule['tour_id'],
                'schedule_id' => $schedule_id,
                'customer_user_id' => !empty($_POST['customer_user_id']) ? $_POST['customer_user_id'] : null,
                'sales_user_id' => $_SESSION['user_id'], // Get from session
                'total_guests' => $total_guests,
                'booking_status' => 'pending', // Correct column name
                'total_price' => $total_price,
            ];
            $bookingId = $this->bookingModel->create($bookingData);

            if (!$bookingId) {
                throw new Exception("Không thể tạo booking chính.");
            }

            // 2. Create guests for the booking
            $guest_genders = $_POST['guest_gender'] ?? [];
            $guest_dobs = $_POST['guest_dob'] ?? [];
            $guest_docs = $_POST['guest_id_document_no'] ?? [];
            $guest_notes = $_POST['guest_notes'] ?? [];

            foreach ($guest_full_names as $index => $fullName) {
                $fullName = trim($fullName);
                if (empty($fullName)) continue;

                $guestData = [
                    'booking_id' => $bookingId,
                    'full_name' => $fullName,
                    'gender' => $guest_genders[$index] ?? 'other',
                    'dob' => !empty($guest_dobs[$index]) ? $guest_dobs[$index] : null,
                    'id_document_no' => $guest_docs[$index] ?? '',
                    'notes' => $guest_notes[$index] ?? ''
                ];
                
                if (!$bookingGuestsModel->create($guestData)) {
                    throw new Exception("Không thể thêm khách '{$fullName}'.");
                }
            }

            // If all good, commit
            $this->bookingModel->commit();
            flash_set('success', "Tạo booking #{$bookingId} thành công!");
            header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $bookingId);
            exit;

        } catch (Exception $e) {
            // If anything fails, roll back
            $this->bookingModel->rollBack();
            flash_set('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '?r=booking_create');
            exit;
        }
    }
    
    public function detail() {
        $id = $_GET['id'] ?? 0;
        $item = $this->bookingModel->find($id);
        if (!$item) {
            flash_set('error', 'Không tìm thấy booking!');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        
        $guests = $this->bookingModel->getBookingGuests($id);
        $statusHistory = $this->bookingModel->getStatusHistory($id);
        $customer = $this->bookingModel->getCustomerInfo($item['customer_user_id'] ?? 0);
        $total_paid = $this->bookingModel->getTotalPaid($id);
        $payment_history = $this->bookingModel->getPaymentHistory($id);
        $suppliers = $this->bookingModel->getTourSuppliers($item['tour_id'] ?? 0);
        $roomAssignments = $this->bookingModel->getRoomAssignmentsByBooking($id);
        $availableRooms = $this->bookingModel->getAvailableRooms();
        $assignmentsByGuest = [];
        foreach ($roomAssignments as $ra) {
            if (!empty($ra['guest_id'])) {
                $assignmentsByGuest[(int)$ra['guest_id']] = $ra;
            }
        }
        
        require 'views/booking/detail_booking.php';
    }

    public function edit($id) {
        if (!$id) {
            flash_set('error', 'ID booking không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }

        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            flash_set('error', 'Không tìm thấy booking để chỉnh sửa.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }

        $guests = (new BookingGuestsModel())->findByBookingId($id);
        $tours = $this->tourModel->all();
        $customers = (new UserModel())->getCustomers();
        $scheduleModel = new ScheduleModel();
        $schedules = $scheduleModel->all();

        // Đồng nhất biến cho view
        $item = $booking;
        require 'views/booking/edit_booking.php';
    }

    public function update() {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            flash_set('error', 'ID không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $ok = $this->bookingModel->update($id, $_POST);
        flash_set($ok ? 'success' : 'error', $ok ? 'Đã cập nhật booking.' : 'Không thể cập nhật booking.');
        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $id);
        exit;
    }

    public function guestCheckin() {
        $booking_id = (int)($_POST['booking_id'] ?? $_GET['booking_id'] ?? 0);
        $guest_id = (int)($_POST['guest_id'] ?? $_GET['guest_id'] ?? 0);
        $checked = isset($_POST['checked']) ? (int)$_POST['checked'] : (isset($_GET['checked']) ? (int)$_GET['checked'] : 0);
        if ($booking_id <= 0 || $guest_id <= 0) {
            flash_set('error', 'Thiếu thông tin khách/booking.');
            header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
            exit;
        }
        $gm = new BookingGuestsModel();
        $ok = $gm->setCheckin($guest_id, $checked);
        // Ghi log nâng cao nếu có dữ liệu
        $stage = $_POST['stage'] ?? $_GET['stage'] ?? null; // gather | bus
        $location = $_POST['location'] ?? $_GET['location'] ?? null;
        $gm->addCheckinLog($booking_id, $guest_id, $stage, $location);
        flash_set($ok ? 'success' : 'error', $ok ? 'Đã cập nhật check-in.' : 'Không thể cập nhật check-in.');
        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
        exit;
    }

    public function pdf() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo 'Invalid booking';
            exit;
        }
        $item = $this->bookingModel->find($id);
        if (!$item) {
            echo 'Booking not found';
            exit;
        }
        $customer = $this->bookingModel->getCustomerInfo($item['customer_user_id'] ?? 0);
        $guests = $this->bookingModel->getBookingGuests($id);
        $suppliers = $this->bookingModel->getTourSuppliers($item['tour_id'] ?? 0);
        // Bổ sung tên tour cho view
        try {
            $tour = $this->tourModel->find($item['tour_id'] ?? 0);
            if ($tour && !empty($tour['title'])) {
                $item['tour_name'] = $tour['title'];
            }
        } catch (Exception $e) {}
        require __DIR__ . '/../views/booking/booking_pdf.php';
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
            $_GET['status']
        );
        
        flash_set('success', 'Cập nhật trạng thái booking thành công.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function cancel() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            flash_set('error', 'ID booking không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $item = $this->bookingModel->find($id);
        if (!$item) {
            flash_set('error', 'Không tìm thấy booking để hủy.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $ok = $this->bookingModel->updateStatus($id, 'canceled');
        flash_set($ok ? 'success' : 'error', $ok ? 'Đã hủy booking.' : 'Không thể hủy booking.');
        header('Location: ' . BASE_URL . '?r=booking');
        exit;
    }

    public function delete($id) {
        $id = (int)$id;
        if ($id <= 0) {
            flash_set('error', 'ID không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        try {
            require_once __DIR__ . '/../assets/configs/db.php';
            $pdo = DB::get();
            $this->bookingModel->beginTransaction();
            // delete dependencies
            $pdo->prepare("DELETE FROM payments WHERE booking_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM booking_guests WHERE booking_id = ?")->execute([$id]);
            try { $pdo->prepare("DELETE FROM booking_status_logs WHERE booking_id = ?")->execute([$id]); } catch (Exception $e) {}
            $this->bookingModel->delete($id);
            $this->bookingModel->commit();
            flash_set('success', 'Đã xóa booking thành công.');
        } catch (Exception $e) {
            $this->bookingModel->rollBack();
            flash_set('error', 'Không thể xóa booking: ' . $e->getMessage());
        }
        header('Location: ' . BASE_URL . '?r=booking');
        exit;
    }

    public function assignRoom() {
        $booking_id = (int)($_POST['booking_id'] ?? 0);
        $room_id = (int)($_POST['room_id'] ?? 0);
        $guest_id = (int)($_POST['guest_id'] ?? 0);
        $check_in_date = $_POST['check_in_date'] ?? null;
        $check_out_date = $_POST['check_out_date'] ?? null;
        if ($booking_id <= 0 || $room_id <= 0) {
            flash_set('error', 'Thiếu thông tin phân phòng.');
            header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
            exit;
        }
        try {
            require_once __DIR__ . '/../assets/configs/db.php';
            $pdo = DB::get();
            // validate availability by date range
            if (!empty($check_in_date) && !empty($check_out_date)) {
                if (!$this->bookingModel->isRoomAvailable($room_id, $check_in_date, $check_out_date)) {
                    flash_set('error', 'Phòng đã được đặt trong khoảng ngày này.');
                    header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
                    exit;
                }
            }
            if ($guest_id > 0) {
                $stmt = $pdo->prepare("INSERT INTO room_assignments (booking_id, guest_id, room_id, check_in_date, check_out_date, status) VALUES (?, ?, ?, ?, ?, 'reserved')");
                $stmt->execute([$booking_id, $guest_id, $room_id, $check_in_date, $check_out_date]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO room_assignments (booking_id, room_id, check_in_date, check_out_date, status) VALUES (?, ?, ?, ?, 'reserved')");
                $stmt->execute([$booking_id, $room_id, $check_in_date, $check_out_date]);
            }
            try { $pdo->prepare("UPDATE rooms SET status='reserved' WHERE id = ?")->execute([$room_id]); } catch (Exception $e2) {}
            flash_set('success', 'Đã phân phòng cho đoàn.');
        } catch (Exception $e) {
            flash_set('error', 'Không thể phân phòng: ' . $e->getMessage());
        }
        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
        exit;
    }

    public function unassignRoom() {
        $assignment_id = (int)($_GET['assignment_id'] ?? 0);
        $booking_id = (int)($_GET['booking_id'] ?? 0);
        if ($assignment_id <= 0) {
            flash_set('error', 'Thiếu ID phân phòng.');
            header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
            exit;
        }
        try {
            require_once __DIR__ . '/../assets/configs/db.php';
            $pdo = DB::get();
            $roomId = null;
            try {
                $st = $pdo->prepare("SELECT room_id FROM room_assignments WHERE id = ?");
                $st->execute([$assignment_id]);
                $roomId = $st->fetchColumn();
            } catch (Exception $e0) {}
            $stmt = $pdo->prepare("DELETE FROM room_assignments WHERE id = ?");
            $stmt->execute([$assignment_id]);
            if ($roomId) { try { $pdo->prepare("UPDATE rooms SET status='available' WHERE id = ?")->execute([(int)$roomId]); } catch (Exception $e3) {} }
            flash_set('success', 'Đã gỡ phân phòng.');
        } catch (Exception $e) {
            flash_set('error', 'Không thể gỡ phân phòng: ' . $e->getMessage());
        }
        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
        exit;
    }
}
?>
