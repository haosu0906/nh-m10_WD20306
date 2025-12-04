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

        // Check capacity
        $schedule = $scheduleModel->find($schedule_id);
        if (!$schedule || (int)$schedule['max_capacity'] < $total_guests) {
            flash_set('error', 'Số lượng khách vượt quá số chỗ còn lại của tour.');
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


        require 'views/booking/edit_booking.php';
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

        $updated = $this->bookingModel->updateStatus($id, 'canceled');
        if ($updated) {
            flash_set('success', 'Đã hủy booking.');
        } else {
            flash_set('error', 'Không thể hủy booking.');
        }

        header('Location: ' . BASE_URL . '?r=booking');
        exit;
    }

    public function delete($id = 0) {
        $id = (int)$id;
        if ($id <= 0) {
            flash_set('error', 'ID booking không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }

        $pdo = $this->bookingModel->getConnection();
        $this->bookingModel->beginTransaction();
        try {
            $stmt = $pdo->prepare("DELETE FROM payments WHERE booking_id = ?");
            $stmt->execute([$id]);

            $stmt2 = $pdo->prepare("DELETE FROM booking_guests WHERE booking_id = ?");
            $stmt2->execute([$id]);

            try {
                $stmt3 = $pdo->prepare("DELETE FROM booking_status_logs WHERE booking_id = ?");
                $stmt3->execute([$id]);
            } catch (Exception $e) {
                // Bảng log có thể không tồn tại
            }

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
}
?>
