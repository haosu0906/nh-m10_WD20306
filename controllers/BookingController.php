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

    private function computeDepartureCutoff($scheduleId)
    {
        try {
            require_once __DIR__ . '/../assets/configs/db.php';
            $pdo = DB::get();
            $st = $pdo->prepare("SELECT id, tour_id, start_date FROM tour_schedules WHERE id = ?");
            $st->execute([(int)$scheduleId]);
            $sch = $st->fetch(PDO::FETCH_ASSOC);
            if (!$sch || empty($sch['start_date'])) { return null; }
            $tourId = (int)($sch['tour_id'] ?? 0);
            $date = $sch['start_date'];
            $hasTable = false; $hasStartCol = false; $hasActivityCol = false;
            try {
                $q = $pdo->query("SHOW TABLES LIKE 'tour_itinerary_items'");
                $hasTable = $q && $q->rowCount() > 0;
                if ($hasTable) {
                    $q1 = $pdo->query("SHOW COLUMNS FROM `tour_itinerary_items` LIKE 'start_time'");
                    $hasStartCol = $q1 && $q1->rowCount() > 0;
                    $q2 = $pdo->query("SHOW COLUMNS FROM `tour_itinerary_items` LIKE 'activity_time'");
                    $hasActivityCol = $q2 && $q2->rowCount() > 0;
                }
            } catch (Exception $e) {}
            $timeStr = '08:00:00';
            if ($hasTable && $tourId > 0) {
                if ($hasStartCol) {
                    $stmt2 = $pdo->prepare("SELECT MIN(start_time) AS t FROM tour_itinerary_items WHERE tour_id = ? AND day_number = 1 AND start_time IS NOT NULL");
                    $stmt2->execute([$tourId]);
                    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                    if (!empty($row2['t'])) { $timeStr = $row2['t']; }
                } elseif ($hasActivityCol) {
                    $stmt3 = $pdo->prepare("SELECT MIN(activity_time) AS t FROM tour_itinerary_items WHERE tour_id = ? AND day_number = 1 AND activity_time IS NOT NULL");
                    $stmt3->execute([$tourId]);
                    $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
                    if (!empty($row3['t'])) { $timeStr = $row3['t']; }
                }
            }
            $dt = strtotime($date . ' ' . $timeStr);
            return $dt ?: null;
        } catch (Exception $e) { return null; }
    }

    private function isLockedForGuideBySchedule($scheduleId)
    {
        $cutoff = $this->computeDepartureCutoff($scheduleId);
        if (!$cutoff) return false;
        return (time() >= $cutoff) && (($_SESSION['role'] ?? '') === 'guide');
    }

    public function index() {
        $statusFilter = $_GET['status'] ?? null;
        $items = $this->bookingModel->all($statusFilter);
        // Đếm loại khách NL/TE/EB cho mỗi booking nếu DB hỗ trợ
        $typeCountsByBooking = [];
        $gm = new BookingGuestsModel();
        foreach ($items as $row) {
            $bid = (int)($row['id'] ?? 0);
            if ($bid > 0) { $typeCountsByBooking[$bid] = $gm->getTypeCounts($bid); }
        }
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
        // Map giá niêm yết theo tour để fallback hiển thị trên form
        $tourPricesById = [];
        try {
            $pdo = (new BaseModel())->getConnection();
            try {
                $stmtP = $pdo->query("SELECT tour_id, adult_price, child_price, infant_price FROM tour_prices");
                foreach ($stmtP->fetchAll(PDO::FETCH_ASSOC) as $r) {
                    $tourPricesById[(int)$r['tour_id']] = [
                        'adult' => (float)($r['adult_price'] ?? 0),
                        'child' => (float)($r['child_price'] ?? 0),
                        'infant' => (float)($r['infant_price'] ?? 0),
                    ];
                }
            } catch (Exception $e1) {
                // Fallback cột trong bảng tours nếu có
                try {
                    $stmtT = $pdo->query("SELECT id AS tour_id, adult_price, child_price, infant_price, price FROM tours");
                    foreach ($stmtT->fetchAll(PDO::FETCH_ASSOC) as $r2) {
                        $tourPricesById[(int)$r2['tour_id']] = [
                            'adult' => (float)($r2['adult_price'] ?? ($r2['price'] ?? 0)),
                            'child' => (float)($r2['child_price'] ?? 0),
                            'infant' => (float)($r2['infant_price'] ?? 0),
                        ];
                    }
                } catch (Exception $e2) {}
            }
        } catch (Exception $e) {}
        
        require 'views/booking/create_booking.php';
    }

    public function store() {
        $scheduleModel = new ScheduleModel();
        $bookingGuestsModel = new BookingGuestsModel();
        $tourModel = new TourModel();
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            flash_set('error', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=booking_create');
            exit;
        }

        // Basic validation
        $schedule_id = $_POST['schedule_id'] ?? null;
        $total_guests = filter_input(INPUT_POST, 'total_guests', FILTER_VALIDATE_INT);
        $guest_types = $_POST['guest_type'] ?? [];
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
        $seatsUsed = 0;
        foreach ($guest_types as $gt) { if ($gt === 'adult' || $gt === 'child') { $seatsUsed++; } }
        if ($seatsUsed === 0) { $seatsUsed = min((int)$total_guests, (int)$total_guests); }
        $available = $maxCap - $occupied;
        if ($available < $seatsUsed) {
            flash_set('error', 'Không đủ chỗ trống. Còn lại: ' . max(0, $available) . ' chỗ.');
            header('Location: ' . BASE_URL . '?r=booking_create');
            exit;
        }

        // DB Transaction
        $this->bookingModel->beginTransaction();
        try {
            // Giá: ưu tiên theo schedule, nếu =0 thì fallback sang tour_prices hoặc cột tours
            $tour = $tourModel->find($schedule['tour_id']);
            $pa = (float)($schedule['price_adult'] ?? 0);
            $pc = (float)($schedule['price_child'] ?? 0);
            $pi = (float)($schedule['price_infant'] ?? 0);
            if ($pa <= 0 || $pc <= 0 || $pi <= 0) {
                try {
                    $pdo = (new BaseModel())->getConnection();
                    // Thử bảng tour_prices
                    try {
                        $stmtP = $pdo->prepare("SELECT adult_price, child_price, infant_price FROM tour_prices WHERE tour_id = ? LIMIT 1");
                        $stmtP->execute([(int)$schedule['tour_id']]);
                        $rowP = $stmtP->fetch(PDO::FETCH_ASSOC);
                        if ($rowP) {
                            if ($pa <= 0) $pa = (float)($rowP['adult_price'] ?? 0);
                            if ($pc <= 0) $pc = (float)($rowP['child_price'] ?? 0);
                            if ($pi <= 0) $pi = (float)($rowP['infant_price'] ?? 0);
                        }
                    } catch (Exception $e1) {}
                    // Fallback cột trên bảng tours
                    if ($pa <= 0) $pa = (float)($tour['adult_price'] ?? ($tour['price'] ?? 0));
                    if ($pc <= 0) $pc = (float)($tour['child_price'] ?? 0);
                    if ($pi <= 0) $pi = (float)($tour['infant_price'] ?? 0);
                } catch (Exception $e) {}
            }
            $ca = 0; $cc = 0; $ci = 0;
            foreach ($guest_types as $gt) {
                if ($gt === 'adult') $ca++; elseif ($gt === 'child') $cc++; elseif ($gt === 'infant') $ci++;
            }
            if (($ca + $cc + $ci) === 0) { $ca = (int)$total_guests; }
            $total_price = ($ca*$pa) + ($cc*$pc) + ($ci*$pi);

            // 1. Create main booking
            $postedCustomerId = !empty($_POST['customer_user_id']) ? (int)$_POST['customer_user_id'] : null;
            if (empty($postedCustomerId)) {
                try {
                    $um = new UserModel();
                    $custs = $um->getCustomers();
                    if (!empty($custs)) { $postedCustomerId = (int)$custs[0]['id']; }
                } catch (Exception $e) {}
            }
            if (empty($postedCustomerId)) { $postedCustomerId = (int)($_SESSION['user_id'] ?? 0); }
            $bookingData = [
                'tour_id' => $schedule['tour_id'],
                'schedule_id' => $schedule_id,
                'customer_user_id' => $postedCustomerId,
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
                    'guest_type' => $guest_types[$index] ?? 'adult',
                    'notes' => $guest_notes[$index] ?? ''
                ];
                
                if (!$bookingGuestsModel->create($guestData)) {
                    throw new Exception("Không thể thêm khách '{$fullName}'.");
                }
            }

            // 3. Update booked_count theo ghế dùng
            try {
                $stmtU = (new BaseModel())->getConnection()->prepare("UPDATE tour_schedules SET booked_count = booked_count + :pax WHERE id = :id");
                $stmtU->execute([':pax' => (int)$seatsUsed, ':id' => (int)$schedule_id]);
            } catch (Exception $e) {}

            // If all good, commit
            $this->bookingModel->commit();
            flash_set('success', "Tạo booking #{$bookingId} thành công! Tổng tiền: " . number_format($total_price,0,',','.')." đ");
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
        $typeCounts = (new BookingGuestsModel())->getTypeCounts($id);
        
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
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            flash_set('error', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
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
        $role = strtolower($_SESSION['role'] ?? '');
        $allowed = ['admin','manager','guide'];
        if (!in_array($role, $allowed)) {
            if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'error'=>'not_allowed']); exit; }
            flash_set('error', 'Bạn không có quyền check-in khách.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?r=booking')));
            exit;
        }
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_validate($_POST['csrf_token'] ?? '')) {
                if ((int)($_POST['ajax'] ?? 0) === 1) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'error'=>'csrf_invalid']); exit; }
                flash_set('error', 'CSRF token không hợp lệ.');
                header('Location: ' . BASE_URL . '?r=booking');
                exit;
            }
        }
        $booking_id = (int)($_POST['booking_id'] ?? $_GET['booking_id'] ?? 0);
        $guest_id = (int)($_POST['guest_id'] ?? $_GET['guest_id'] ?? 0);
        $expectedScheduleId = (int)($_POST['schedule_id'] ?? $_GET['schedule_id'] ?? 0);
        $checked = isset($_POST['checked']) ? (int)$_POST['checked'] : (isset($_GET['checked']) ? (int)$_GET['checked'] : 0);
        if ($booking_id <= 0 || $guest_id <= 0) {
            if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'error'=>'missing_params']); exit; }
            flash_set('error', 'Thiếu thông tin khách/booking.');
            header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
            exit;
        }
        // Nếu là guide, xác thực phân công với tour/schedule của booking
        if ($role === 'guide') {
            try {
                require_once __DIR__ . '/../assets/configs/db.php';
                $pdo = DB::get();
                $st = $pdo->prepare("SELECT COUNT(*) FROM bookings b
                                      JOIN guide_assignments ga ON ga.tour_id = b.tour_id
                                      WHERE b.id = ? AND ga.guide_user_id = ?");
                $st->execute([(int)$booking_id, (int)($_SESSION['user_id'] ?? 0)]);
                if ((int)$st->fetchColumn() === 0) {
                    if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'error'=>'guide_not_assigned']); exit; }
                    flash_set('error', 'HDV không được phân công tour này.');
                    header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
                    exit;
                }
                if ($expectedScheduleId > 0) {
                    $stDep = $pdo->prepare("SELECT schedule_id FROM bookings WHERE id = ?");
                    $stDep->execute([(int)$booking_id]);
                    $realScheduleId = (int)($stDep->fetchColumn() ?: 0);
                    if ($realScheduleId !== $expectedScheduleId) {
                        if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'error'=>'schedule_mismatch']); exit; }
                        flash_set('error', 'QR không thuộc lịch này.');
                        header('Location: ' . BASE_URL . '?r=tour_manifest&departure_id=' . $expectedScheduleId);
                        exit;
                    }
                }
            } catch (Exception $e) {}
        }
        // enforce lock after departure for guides
        try {
            require_once __DIR__ . '/../assets/configs/db.php';
            $pdo = DB::get();
            $stDep = $pdo->prepare("SELECT schedule_id FROM bookings WHERE id = ?");
            $stDep->execute([$booking_id]);
            $scheduleId = (int)($stDep->fetchColumn() ?: 0);
            if ($this->isLockedForGuideBySchedule($scheduleId)) {
                if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'error'=>'locked']); exit; }
                flash_set('error', 'Đã quá giờ xuất phát. Liên hệ Quản lý để sửa.');
                header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id); exit;
            }
        } catch (Exception $e) {}
        $gm = new BookingGuestsModel();
        $ok = $gm->setCheckin($guest_id, $checked);
        $stage = $_POST['stage'] ?? $_GET['stage'] ?? null;
        $location = $_POST['location'] ?? $_GET['location'] ?? null;
        $status = $_POST['status'] ?? ($checked ? 'arrived' : 'pending');
        $reason = $_POST['reason'] ?? null;
        $note = $_POST['note'] ?? null;
        $gm->addCheckinLog($booking_id, $guest_id, $stage, $location, $status, $reason, $note);
        if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) {
            $guest = $gm->find($guest_id);
            $guests = $gm->getByBooking($booking_id);
            $stats = ['total'=>count($guests),'checked'=>0,'pending'=>0,'noshow'=>0];
            foreach ($guests as $g) { if (!empty($g['is_no_show'])) { $stats['noshow']++; } else if (!empty($g['is_checked_in'])) { $stats['checked']++; } else { $stats['pending']++; } }
            header('Content-Type: application/json');
            echo json_encode(['success'=>$ok, 'guest_id'=>$guest_id, 'is_checked_in'=>(int)($guest['is_checked_in'] ?? 0), 'is_no_show'=>(int)($guest['is_no_show'] ?? 0), 'stats'=>$stats]);
            exit;
        }
        flash_set($ok ? 'success' : 'error', $ok ? 'Đã cập nhật check-in.' : 'Không thể cập nhật check-in.');
        header('Location: ' . BASE_URL . '?r=booking_manifest&id=' . $booking_id); exit;
    }

    public function guestNoShow() {
        $role = strtolower($_SESSION['role'] ?? '');
        $allowed = ['admin','manager','guide'];
        if (!in_array($role, $allowed)) {
            flash_set('error', 'Bạn không có quyền đánh dấu vắng.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?r=booking')));
            exit;
        }
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_validate($_POST['csrf_token'] ?? '')) {
                if ((int)($_POST['ajax'] ?? 0) === 1) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'error'=>'csrf_invalid']); exit; }
                flash_set('error', 'CSRF token không hợp lệ.');
                header('Location: ' . BASE_URL . '?r=booking');
                exit;
            }
        }
        $booking_id = (int)($_POST['booking_id'] ?? $_GET['booking_id'] ?? 0);
        $guest_id = (int)($_POST['guest_id'] ?? $_GET['guest_id'] ?? 0);
        $flag = isset($_POST['no_show']) ? (int)$_POST['no_show'] : (isset($_GET['no_show']) ? (int)$_GET['no_show'] : 1);
        if ($booking_id <= 0 || $guest_id <= 0) { flash_set('error', 'Thiếu thông tin khách/booking.'); header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id); exit; }
        if ($role === 'guide') {
            try {
                require_once __DIR__ . '/../assets/configs/db.php';
                $pdo = DB::get();
                $st = $pdo->prepare("SELECT COUNT(*) FROM bookings b
                                      JOIN guide_assignments ga ON ga.tour_id = b.tour_id
                                      WHERE b.id = ? AND ga.guide_user_id = ?");
                $st->execute([(int)$booking_id, (int)($_SESSION['user_id'] ?? 0)]);
                if ((int)$st->fetchColumn() === 0) {
                    flash_set('error', 'HDV không được phân công tour này.');
                    header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
                    exit;
                }
            } catch (Exception $e) {}
        }
        // enforce lock after departure for guides
        try {
            require_once __DIR__ . '/../assets/configs/db.php';
            $pdo = DB::get();
            $stDep = $pdo->prepare("SELECT schedule_id FROM bookings WHERE id = ?");
            $stDep->execute([$booking_id]);
            $scheduleId = (int)($stDep->fetchColumn() ?: 0);
            if ($this->isLockedForGuideBySchedule($scheduleId)) {
                if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'error'=>'locked']); exit; }
                flash_set('error', 'Đã quá giờ xuất phát. Liên hệ Quản lý để sửa.');
                header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id); exit;
            }
        } catch (Exception $e) {}
        $gm = new BookingGuestsModel();
        $ok = $gm->setNoShow($guest_id, $flag);
        $status = $_POST['status'] ?? ($flag ? 'noshow' : 'pending');
        $reason = $_POST['reason'] ?? null;
        $note = $_POST['note'] ?? null;
        $gm->addCheckinLog($booking_id, $guest_id, 'no_show', null, $status, $reason, $note);
        if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) {
            $guest = $gm->find($guest_id);
            $guests = $gm->getByBooking($booking_id);
            $stats = ['total'=>count($guests),'checked'=>0,'pending'=>0,'noshow'=>0];
            foreach ($guests as $g) { if (!empty($g['is_no_show'])) { $stats['noshow']++; } else if (!empty($g['is_checked_in'])) { $stats['checked']++; } else { $stats['pending']++; } }
            header('Content-Type: application/json');
            echo json_encode(['success'=>$ok, 'guest_id'=>$guest_id, 'is_checked_in'=>(int)($guest['is_checked_in'] ?? 0), 'is_no_show'=>(int)($guest['is_no_show'] ?? 0), 'stats'=>$stats]);
            exit;
        }
        flash_set($ok ? 'success' : 'error', $ok ? 'Đã đánh dấu vắng mặt.' : 'Không thể cập nhật vắng mặt.');
        header('Location: ' . BASE_URL . '?r=booking_manifest&id=' . $booking_id); exit;
    }

    public function guestCheckinHistory() {
        $role = $_SESSION['role'] ?? '';
        $allowed = ['admin','manager','guide'];
        if (!in_array($role, $allowed)) { header('HTTP/1.1 403 Forbidden'); echo json_encode(['success'=>false]); exit; }
        $guestId = (int)($_GET['guest_id'] ?? $_POST['guest_id'] ?? 0);
        if ($guestId <= 0) { echo json_encode(['success'=>false]); exit; }
        $gm = new BookingGuestsModel();
        $logs = $gm->getCheckinLogs($guestId);
        header('Content-Type: application/json');
        echo json_encode(['success'=>true,'logs'=>$logs]);
        exit;
    }

    public function groupCheckin() {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_validate($_POST['csrf_token'] ?? '')) {
                flash_set('error', 'CSRF token không hợp lệ.');
                header('Location: ' . BASE_URL . '?r=booking');
                exit;
            }
        }
        $booking_id = (int)($_POST['booking_id'] ?? $_GET['booking_id'] ?? 0);
        $checked = isset($_POST['checked']) ? (int)$_POST['checked'] : 1;
        if ($booking_id <= 0) { flash_set('error','Thiếu booking'); header('Location: ' . BASE_URL . '?r=booking'); exit; }
        $gm = new BookingGuestsModel();
        $ok = $gm->setCheckinAllByBooking($booking_id, $checked);
        flash_set($ok ? 'success' : 'error', $ok ? 'Đã cập nhật check-in cả đoàn.' : 'Không thể cập nhật.');
        header('Location: ' . BASE_URL . '?r=booking_manifest&id=' . $booking_id); exit;
    }

    public function groupNoShow() {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_validate($_POST['csrf_token'] ?? '')) {
                flash_set('error', 'CSRF token không hợp lệ.');
                header('Location: ' . BASE_URL . '?r=booking');
                exit;
            }
        }
        $booking_id = (int)($_POST['booking_id'] ?? $_GET['booking_id'] ?? 0);
        $flag = isset($_POST['no_show']) ? (int)$_POST['no_show'] : 1;
        if ($booking_id <= 0) { flash_set('error','Thiếu booking'); header('Location: ' . BASE_URL . '?r=booking'); exit; }
        $gm = new BookingGuestsModel();
        $ok = $gm->setNoShowAllByBooking($booking_id, $flag);
        flash_set($ok ? 'success' : 'error', $ok ? 'Đã đánh dấu vắng mặt cả đoàn.' : 'Không thể cập nhật.');
        header('Location: ' . BASE_URL . '?r=booking_manifest&id=' . $booking_id); exit;
    }

    public function departureGroupCheckin() {
        $role = strtolower($_SESSION['role'] ?? '');
        $allowed = ['admin','manager','guide'];
        if (!in_array($role, $allowed)) { echo json_encode(['success'=>false]); exit; }
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_validate($_POST['csrf_token'] ?? '')) { echo json_encode(['success'=>false]); exit; }
        }
        $dep = (int)($_POST['departure_id'] ?? $_GET['departure_id'] ?? 0);
        $checked = isset($_POST['checked']) ? (int)$_POST['checked'] : 1;
        if ($dep <= 0) { echo json_encode(['success'=>false]); exit; }
        if ($role === 'guide') {
            try {
                require_once __DIR__ . '/../assets/configs/db.php';
                $pdo = DB::get();
                $st = $pdo->prepare("SELECT COUNT(*) FROM guide_assignments WHERE schedule_id = ? AND guide_user_id = ?");
                $st->execute([$dep, (int)($_SESSION['user_id'] ?? 0)]);
                if ((int)$st->fetchColumn() === 0) { echo json_encode(['success'=>false]); exit; }
            } catch (Exception $e) {}
        }
        // lock check for guide
        try {
            if ($this->isLockedForGuideBySchedule($dep)) { echo json_encode(['success'=>false,'error'=>'locked']); exit; }
        } catch (Exception $e) {}
        $gm = new BookingGuestsModel();
        $ok = $gm->setCheckinAllByDeparture($dep, $checked);
        if ($ok) {
            $status = 'arrived'; $reason = $_POST['reason'] ?? null; $note = $_POST['note'] ?? null;
            try {
                require_once __DIR__ . '/../assets/configs/db.php';
                $pdo = DB::get();
                $st = $pdo->prepare("SELECT bg.id AS guest_id, bg.booking_id FROM booking_guests bg JOIN bookings b ON bg.booking_id=b.id WHERE b.schedule_id = ?");
                $st->execute([$dep]);
                $rows = $st->fetchAll();
                foreach ($rows as $r) { $gm->addCheckinLog((int)$r['booking_id'], (int)$r['guest_id'], 'gather', null, $status, $reason, $note); }
            } catch (Exception $e) {}
        }
        if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) {
            echo json_encode(['success'=>$ok]); exit;
        }
        header('Location: ' . BASE_URL . '?r=tour_manifest&departure_id=' . $dep); exit;
    }

    public function departureGroupNoShow() {
        $role = $_SESSION['role'] ?? '';
        $allowed = ['admin','manager','guide'];
        if (!in_array($role, $allowed)) { echo json_encode(['success'=>false]); exit; }
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_validate($_POST['csrf_token'] ?? '')) { echo json_encode(['success'=>false]); exit; }
        }
        $dep = (int)($_POST['departure_id'] ?? $_GET['departure_id'] ?? 0);
        $flag = isset($_POST['no_show']) ? (int)$_POST['no_show'] : 1;
        if ($dep <= 0) { echo json_encode(['success'=>false]); exit; }
        if ($role === 'guide') {
            try {
                require_once __DIR__ . '/../assets/configs/db.php';
                $pdo = DB::get();
                $st = $pdo->prepare("SELECT COUNT(*) FROM guide_assignments WHERE schedule_id = ? AND guide_user_id = ?");
                $st->execute([$dep, (int)($_SESSION['user_id'] ?? 0)]);
                if ((int)$st->fetchColumn() === 0) { echo json_encode(['success'=>false]); exit; }
            } catch (Exception $e) {}
        }
        // lock check for guide
        try {
            if ($this->isLockedForGuideBySchedule($dep)) { echo json_encode(['success'=>false,'error'=>'locked']); exit; }
        } catch (Exception $e) {}
        $gm = new BookingGuestsModel();
        $ok = $gm->setNoShowAllByDeparture($dep, $flag);
        if ($ok) {
            $status = 'noshow'; $reason = $_POST['reason'] ?? null; $note = $_POST['note'] ?? null;
            try {
                require_once __DIR__ . '/../assets/configs/db.php';
                $pdo = DB::get();
                $st = $pdo->prepare("SELECT bg.id AS guest_id, bg.booking_id FROM booking_guests bg JOIN bookings b ON bg.booking_id=b.id WHERE b.schedule_id = ?");
                $st->execute([$dep]);
                $rows = $st->fetchAll();
                foreach ($rows as $r) { $gm->addCheckinLog((int)$r['booking_id'], (int)$r['guest_id'], 'no_show', null, $status, $reason, $note); }
            } catch (Exception $e) {}
        }
        if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) {
            echo json_encode(['success'=>$ok]); exit;
        }
        header('Location: ' . BASE_URL . '?r=tour_manifest&departure_id=' . $dep); exit;
    }

    public function departureGroupPending() {
        $role = $_SESSION['role'] ?? '';
        $allowed = ['admin','manager','guide'];
        if (!in_array($role, $allowed)) { echo json_encode(['success'=>false]); exit; }
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_validate($_POST['csrf_token'] ?? '')) { echo json_encode(['success'=>false]); exit; }
        }
        $dep = (int)($_POST['departure_id'] ?? $_GET['departure_id'] ?? 0);
        if ($dep <= 0) { echo json_encode(['success'=>false]); exit; }
        if ($role === 'guide') {
            try {
                require_once __DIR__ . '/../assets/configs/db.php';
                $pdo = DB::get();
                $st = $pdo->prepare("SELECT COUNT(*) FROM guide_assignments WHERE schedule_id = ? AND guide_user_id = ?");
                $st->execute([$dep, (int)($_SESSION['user_id'] ?? 0)]);
                if ((int)$st->fetchColumn() === 0) { echo json_encode(['success'=>false]); exit; }
            } catch (Exception $e) {}
        }
        // lock check for guide
        try {
            if ($this->isLockedForGuideBySchedule($dep)) { echo json_encode(['success'=>false,'error'=>'locked']); exit; }
        } catch (Exception $e) {}
        $gm = new BookingGuestsModel();
        $ok = $gm->setPendingAllByDeparture($dep);
        if ($ok) {
            $status = 'pending'; $reason = $_POST['reason'] ?? null; $note = $_POST['note'] ?? null;
            try {
                require_once __DIR__ . '/../assets/configs/db.php';
                $pdo = DB::get();
                $st = $pdo->prepare("SELECT bg.id AS guest_id, bg.booking_id FROM booking_guests bg JOIN bookings b ON bg.booking_id=b.id WHERE b.schedule_id = ?");
                $st->execute([$dep]);
                $rows = $st->fetchAll();
                foreach ($rows as $r) { $gm->addCheckinLog((int)$r['booking_id'], (int)$r['guest_id'], 'gather', null, $status, $reason, $note); }
            } catch (Exception $e) {}
        }
        if ((int)($_POST['ajax'] ?? $_GET['ajax'] ?? 0) === 1) { echo json_encode(['success'=>$ok]); exit; }
        header('Location: ' . BASE_URL . '?r=tour_manifest&departure_id=' . $dep); exit;
    }

    public function manifest() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { flash_set('error','Thiếu booking'); header('Location: ' . BASE_URL . '?r=booking'); exit; }
        $booking = $this->bookingModel->find($id);
        if (!$booking) { flash_set('error','Không tìm thấy booking'); header('Location: ' . BASE_URL . '?r=booking'); exit; }
        $gm = new BookingGuestsModel();
        $guests = $gm->getByBooking($id);
        $stats = [
            'total' => count($guests),
            'checked' => 0,
            'pending' => 0,
            'noshow' => 0,
        ];
        foreach ($guests as $g) {
            if (!empty($g['is_no_show'])) { $stats['noshow']++; continue; }
            if (!empty($g['is_checked_in'])) { $stats['checked']++; } else { $stats['pending']++; }
        }
        require __DIR__ . '/../views/booking/manifest.php';
    }

    public function manifestDepartureExport() {
        $type = $_GET['type'] ?? 'csv';
        $departureId = (int)($_GET['departure_id'] ?? 0);
        if ($departureId <= 0) { echo 'Thiếu departure_id'; exit; }
        try {
            require_once __DIR__ . '/../assets/configs/db.php';
            $pdo = DB::get();
            $sql = "SELECT bg.*, b.id AS booking_id, t.title AS tour_title, u.full_name AS leader_name, u.phone AS leader_phone
                    FROM booking_guests bg
                    JOIN bookings b ON bg.booking_id = b.id
                    LEFT JOIN tours t ON b.tour_id = t.id
                    LEFT JOIN users u ON b.customer_user_id = u.id
                    WHERE b.schedule_id = :dep AND b.booking_status IN ('deposit','completed')
                    ORDER BY b.id, bg.id";
            $st = $pdo->prepare($sql);
            $st->execute(['dep'=>$departureId]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) { $rows = []; }

        if ($type === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="tour_manifest_' . $departureId . '.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Booking','Leader','Phone','Guest','Gender','DOB','ID Doc','Note','Arrived','No-show']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    'BK' . (int)($r['booking_id'] ?? 0),
                    $r['leader_name'] ?? '',
                    $r['leader_phone'] ?? '',
                    $r['full_name'] ?? '',
                    $r['gender'] ?? '',
                    $r['dob'] ?? '',
                    $r['id_document_no'] ?? '',
                    $r['notes'] ?? '',
                    !empty($r['is_checked_in']) ? 1 : 0,
                    !empty($r['is_no_show']) ? 1 : 0,
                ]);
            }
            fclose($out); exit;
        }

        echo '<!doctype html><html><head><meta charset="utf-8"><title>In Danh sách đoàn</title>';
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />';
        echo '</head><body class="p-4">';
        echo '<h4 class="mb-3">Danh sách đoàn — Chuyến #' . (int)$departureId . '</h4>';
        echo '<table class="table table-bordered table-sm"><thead><tr>';
        echo '<th>Booking</th><th>Leader</th><th>Điện thoại</th><th>Khách</th><th>Giới tính</th><th>Năm sinh</th><th>Giấy tờ</th><th>Ghi chú</th><th>Đến</th><th>Vắng</th>';
        echo '</tr></thead><tbody>';
        foreach ($rows as $r) {
            echo '<tr>';
            echo '<td>BK' . (int)($r['booking_id'] ?? 0) . '</td>';
            echo '<td>' . htmlspecialchars($r['leader_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['leader_phone'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['full_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['gender'] ?? '') . '</td>';
            $year = !empty($r['dob']) ? date('Y', strtotime($r['dob'])) : '';
            echo '<td>' . htmlspecialchars($year) . '</td>';
            echo '<td>' . htmlspecialchars($r['id_document_no'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($r['notes'] ?? '') . '</td>';
            echo '<td>' . (!empty($r['is_checked_in']) ? '✓' : '') . '</td>';
            echo '<td>' . (!empty($r['is_no_show']) ? '✓' : '') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</body></html>';
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
        $role = $_SESSION['role'] ?? '';
        $allowed = ['admin','manager','sales'];
        if (!in_array($role, $allowed)) {
            flash_set('error', 'Bạn không có quyền cập nhật trạng thái booking.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?r=booking')));
            exit;
        }
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
        $role = $_SESSION['role'] ?? '';
        $allowed = ['admin','manager'];
        if (!in_array($role, $allowed)) {
            flash_set('error', 'Bạn không có quyền hủy booking.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?r=booking')));
            exit;
        }
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
        $role = $_SESSION['role'] ?? '';
        if ($role !== 'admin') {
            flash_set('error', 'Chỉ admin được phép xóa booking.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?r=booking')));
            exit;
        }
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
            try { $pdo->prepare("DELETE FROM room_assignments WHERE booking_id = ?")->execute([$id]); } catch (Exception $e0) {}
            try { $pdo->prepare("DELETE FROM booking_requests WHERE booking_id = ?")->execute([$id]); } catch (Exception $e1) {}
            try { $pdo->prepare("DELETE FROM guest_checkins WHERE booking_id = ?")->execute([$id]); } catch (Exception $e2) {}
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
                // Chặn gán phòng cho khách vắng mặt
                try {
                    $gm = new BookingGuestsModel();
                    $g = $gm->find($guest_id);
                    if (!empty($g['is_no_show'])) {
                        flash_set('error', 'Khách đã đánh dấu vắng mặt, không xếp phòng.');
                        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
                        exit;
                    }
                } catch (Exception $e) {}
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

    public function supplierServiceConfirm() {
        $role = $_SESSION['role'] ?? '';
        $allowed = ['admin','manager','sales'];
        if (!in_array($role, $allowed)) {
            flash_set('error', 'Bạn không có quyền xác nhận dịch vụ NCC.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?r=booking')));
            exit;
        }
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            flash_set('error', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $booking_id = (int)($_POST['booking_id'] ?? 0);
        $supplier_id = (int)($_POST['supplier_id'] ?? 0);
        $service_type = trim($_POST['service_type'] ?? '');
        $status = trim($_POST['status'] ?? '');
        if ($booking_id <= 0 || $supplier_id <= 0 || $service_type === '' || ($status !== 'pending' && $status !== 'confirmed' && $status !== 'canceled')) {
            flash_set('error', 'Thiếu thông tin xác nhận dịch vụ.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $item = $this->bookingModel->find($booking_id);
        if (!$item) {
            flash_set('error', 'Không tìm thấy booking.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $tour_id = (int)($item['tour_id'] ?? 0);
        if ($tour_id <= 0) {
            flash_set('error', 'Thiếu tour cho booking.');
            header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
            exit;
        }
        try {
            $pdo = (new BaseModel())->getConnection();
            $stmt = $pdo->prepare("SELECT id, description FROM tour_expenses WHERE tour_id = ? AND supplier_id = ? AND expense_type = ? AND is_actual_cost = 0 AND amount = 0 ORDER BY id ASC LIMIT 1");
            $stmt->execute([$tour_id, $supplier_id, $service_type]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $stmt2 = $pdo->prepare("SELECT id, description FROM tour_expenses WHERE tour_id = ? AND supplier_id = ? AND expense_type = ? ORDER BY id ASC LIMIT 1");
                $stmt2->execute([$tour_id, $supplier_id, $service_type]);
                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            }
            if (!$row) {
                flash_set('error', 'Không tìm thấy dịch vụ NCC để cập nhật.');
                header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
                exit;
            }
            $desc = (string)($row['description'] ?? '');
            $base = preg_replace('/\s*\[CONFIRM:(pending|confirmed|canceled)\]/i', '', $desc);
            $base = preg_replace('/\s*\[REMIND:([0-9\-: ]+)\]/i', '', $base);
            $newdesc = trim($base) . ' [CONFIRM:' . $status . ']';
            $up = $pdo->prepare("UPDATE tour_expenses SET description = ? WHERE id = ?");
            $up->execute([$newdesc, (int)$row['id']]);
            flash_set('success', 'Đã cập nhật xác nhận dịch vụ NCC.');
        } catch (Exception $e) {
            flash_set('error', 'Không thể cập nhật: ' . $e->getMessage());
        }
        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
        exit;
    }

    public function supplierServiceRemind() {
        $role = $_SESSION['role'] ?? '';
        $allowed = ['admin','manager','sales'];
        if (!in_array($role, $allowed)) {
            flash_set('error', 'Bạn không có quyền nhắc NCC.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?r=booking')));
            exit;
        }
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            flash_set('error', 'CSRF token không hợp lệ.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $booking_id = (int)($_POST['booking_id'] ?? 0);
        $supplier_id = (int)($_POST['supplier_id'] ?? 0);
        $service_type = trim($_POST['service_type'] ?? '');
        if ($booking_id <= 0 || $supplier_id <= 0 || $service_type === '') {
            flash_set('error', 'Thiếu thông tin nhắc NCC.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $item = $this->bookingModel->find($booking_id);
        if (!$item) {
            flash_set('error', 'Không tìm thấy booking.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $tour_id = (int)($item['tour_id'] ?? 0);
        try {
            $pdo = (new BaseModel())->getConnection();
            $stmt = $pdo->prepare("SELECT id, description FROM tour_expenses WHERE tour_id = ? AND supplier_id = ? AND expense_type = ? AND is_actual_cost = 0 AND amount = 0 ORDER BY id ASC LIMIT 1");
            $stmt->execute([$tour_id, $supplier_id, $service_type]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $stmt2 = $pdo->prepare("SELECT id, description FROM tour_expenses WHERE tour_id = ? AND supplier_id = ? AND expense_type = ? ORDER BY id ASC LIMIT 1");
                $stmt2->execute([$tour_id, $supplier_id, $service_type]);
                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            }
            if (!$row) {
                flash_set('error', 'Không tìm thấy dịch vụ NCC để nhắc.');
                header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
                exit;
            }
            $desc = (string)($row['description'] ?? '');
            $base = preg_replace('/\s*\[REMIND:([0-9\-: ]+)\]/i', '', $desc);
            $now = date('Y-m-d H:i');
            $newdesc = trim($base) . ' [REMIND:' . $now . ']';
            $up = $pdo->prepare("UPDATE tour_expenses SET description = ? WHERE id = ?");
            $up->execute([$newdesc, (int)$row['id']]);
            $supplier = (new SupplierModel())->find($supplier_id);
            $to = trim((string)($supplier['email'] ?? ''));
            if ($to !== '') {
                $scheduleModel = new ScheduleModel();
                $schedule = null;
                if (!empty($item['schedule_id'])) { $schedule = $scheduleModel->find((int)$item['schedule_id']); }
                $start = !empty($schedule['start_date'] ?? null) ? date('d/m/Y', strtotime($schedule['start_date'])) : '';
                $end = !empty($schedule['end_date'] ?? null) ? date('d/m/Y', strtotime($schedule['end_date'])) : '';
                $dates = ($start !== '' && $end !== '') ? ($start . ' - ' . $end) : ($start !== '' ? $start : $end);
                $subject = 'Nhắc xác nhận dịch vụ ' . $service_type . ' | Booking #' . $booking_id;
                $html = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;">'
                      . '<p>Kính gửi ' . htmlspecialchars((string)($supplier['contact_person'] ?? 'Quý đối tác'), ENT_QUOTES, 'UTF-8') . ',</p>'
                      . '<p>Vui lòng xác nhận dịch vụ ' . htmlspecialchars($service_type, ENT_QUOTES, 'UTF-8') . ' cho booking #' . $booking_id . ' (' . htmlspecialchars((string)($item['tour_name'] ?? ''), ENT_QUOTES, 'UTF-8') . ').</p>'
                      . ($dates !== '' ? ('<p>Thời gian: ' . htmlspecialchars($dates, ENT_QUOTES, 'UTF-8') . '</p>') : '')
                      . '<p>Mô tả hiện tại: ' . htmlspecialchars($base, ENT_QUOTES, 'UTF-8') . '</p>'
                      . '<p>Trân trọng,<br>TripMate</p>'
                      . '</div>';
                send_email($to, $subject, $html);
            }
            flash_set('success', 'Đã ghi nhận nhắc NCC.');
        } catch (Exception $e) {
            flash_set('error', 'Không thể cập nhật: ' . $e->getMessage());
        }
        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $booking_id);
        exit;
    }

    public function sendEmail() {
        $role = $_SESSION['role'] ?? '';
        $allowed = ['admin','manager','sales'];
        if (!in_array($role, $allowed)) {
            flash_set('error', 'Bạn không có quyền gửi email.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?r=booking')));
            exit;
        }
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            flash_set('error', 'Thiếu mã booking.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $item = $this->bookingModel->find($id);
        if (!$item) {
            flash_set('error', 'Không tìm thấy booking.');
            header('Location: ' . BASE_URL . '?r=booking');
            exit;
        }
        $userModel = new UserModel();
        $customer = $userModel->find((int)($item['customer_user_id'] ?? 0));
        $email = trim((string)($customer['email'] ?? ''));
        if ($email === '') {
            flash_set('error', 'Khách hàng chưa có email.');
            header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $id);
            exit;
        }
        $scheduleModel = new ScheduleModel();
        $schedule = null;
        if (!empty($item['schedule_id'])) {
            $schedule = $scheduleModel->find((int)$item['schedule_id']);
        }
        $subject = 'Tóm tắt Booking #' . $id . ' - ' . ((string)($item['tour_name'] ?? ''));
        $start = !empty($schedule['start_date'] ?? null) ? date('d/m/Y', strtotime($schedule['start_date'])) : '';
        $end = !empty($schedule['end_date'] ?? null) ? date('d/m/Y', strtotime($schedule['end_date'])) : '';
        $dates = ($start !== '' && $end !== '') ? ($start . ' - ' . $end) : ($start !== '' ? $start : $end);
        $status = (string)($item['booking_status'] ?? 'pending');
        $totalGuests = (int)($item['total_guests'] ?? 0);
        $totalPrice = (float)($item['total_price'] ?? 0);
        $special = (string)($item['special_requests'] ?? '');
        $link = BASE_URL . '?r=booking_detail&id=' . $id;
        $customerName = (string)($customer['full_name'] ?? '');
        $html = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;">'
              . '<p>Xin chào ' . htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8') . ',</p>'
              . '<p>Đây là tóm tắt booking của bạn:</p>'
              . '<ul>'
              . '<li>Tour: ' . htmlspecialchars((string)($item['tour_name'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
              . '<li>Thời gian: ' . htmlspecialchars($dates, ENT_QUOTES, 'UTF-8') . '</li>'
              . '<li>Số khách: ' . $totalGuests . '</li>'
              . '<li>Trạng thái: ' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '</li>'
              . '<li>Tổng tiền: ' . number_format($totalPrice, 0, ',', '.') . ' VND</li>'
              . '</ul>'
              . ($special !== '' ? ('<p>Yêu cầu đặc biệt: ' . nl2br(htmlspecialchars($special, ENT_QUOTES, 'UTF-8')) . '</p>') : '')
              . '<p>Xem chi tiết tại: <a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '</a></p>'
              . '<p>Trân trọng,<br>TripMate</p>'
              . '</div>';
        $ok = send_email($email, $subject, $html);
        if ($ok) {
            flash_set('success', 'Đã gửi email tóm tắt booking cho khách.');
        } else {
            flash_set('error', 'Gửi email thất bại.');
        }
        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $id);
        exit;
    }
}
?>
