<?php

require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/TourCategoryModel.php';
require_once __DIR__ . '/../models/TourItineraryModel.php';
require_once __DIR__ . '/../models/TourImageModel.php';
require_once __DIR__ . '/../models/TourPriceModel.php';
require_once __DIR__ . '/../models/TourSupplierModel.php';
require_once __DIR__ . '/../models/SupplierModel.php';
require_once __DIR__ . '/../models/ScheduleModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/TourItineraryItemModel.php';

class TourController
{
    protected $tourModel;
    protected $categoryModel;
    protected $itineraryModel;
    protected $imageModel;
    protected $priceModel;
    protected $supplierModel;
    protected $scheduleModel;
    protected $userModel;
    protected $itineraryItemModel;

    protected $types = [
        'domestic' => 'Tour trong nước',
        'international' => 'Tour quốc tế',
        'custom' => 'Tour theo yêu cầu'
    ];

    protected $statuses = [
        'upcoming' => 'Chuẩn bị khởi hành',
        'open' => 'Đang mở bán',
        'closed' => 'Đã đóng',
        'finished' => 'Đã kết thúc'
    ];

    public function __construct()
    {
        $this->tourModel = new TourModel();
        $this->categoryModel = new TourCategoryModel();
        $this->itineraryModel = new TourItineraryModel();
        $this->imageModel = new TourImageModel();
        $this->priceModel = new TourPriceModel();
        $this->supplierModel = new TourSupplierModel();
        $this->scheduleModel = new ScheduleModel();
        $this->userModel = new UserModel();
        $this->itineraryItemModel = new TourItineraryItemModel();
    }

    public function itinerary($id)
    {
        $tour = $this->tourModel->find($id);
        if (!$tour) {
            header('Location: ' . BASE_URL . '?r=tours');
            exit;
        }
        $types = $this->types;
        $statuses = $this->statuses;
        $gallery = $this->imageModel->getByTour($id);
        $price = $this->priceModel->getByTour($id);
        if (!$price && isset($tour['price'])) { // fallback legacy price
            $price = ['adult_price' => (float)$tour['price'], 'child_price' => 0, 'infant_price' => 0];
        }
        $itineraries = $this->itineraryModel->getByTour($id);
        $itemModel = $this->itineraryItemModel;
        $items = $itemModel->getByTour($id);

        // Nếu chưa có item chi tiết, tự sinh dựa trên lịch gần nhất hoặc mặc định 3 ngày
        if (empty($items)) {
            $durationDays = 3;
            try {
                $schedules = $this->scheduleModel->getByTour($id);
                if (!empty($schedules)) {
                    $s = $schedules[0];
                    $start = new DateTime($s['start_date']);
                    $end   = new DateTime($s['end_date']);
                    $durationDays = max(1, $start->diff($end)->days + 1);
                }
            } catch (Exception $e) { /* ignore */ }

            $gen = [];
            for ($d = 1; $d <= $durationDays; $d++) {
                $gen[] = ['day_number'=>$d,'activity_time'=>'07:30:00','end_time'=>'11:30:00','slot'=>'morning','title'=>'Buổi sáng','details'=>'Đang cập nhật hoạt động buổi sáng','meal_plan'=>'Sáng'];
                $gen[] = ['day_number'=>$d,'activity_time'=>'11:30:00','end_time'=>'13:30:00','slot'=>'noon','title'=>'Buổi trưa','details'=>'Đang cập nhật bữa trưa','meal_plan'=>'Trưa'];
                $gen[] = ['day_number'=>$d,'activity_time'=>'14:00:00','end_time'=>'17:30:00','slot'=>'afternoon','title'=>'Buổi chiều','details'=>'Đang cập nhật hoạt động buổi chiều','meal_plan'=>''];
                $gen[] = ['day_number'=>$d,'activity_time'=>'19:00:00','end_time'=>'21:00:00','slot'=>'evening','title'=>'Buổi tối','details'=>'Đang cập nhật hoạt động buổi tối','meal_plan'=>'Tối'];
            }
            try { $this->itineraryItemModel->addBulk($id, $gen); } catch (Exception $e) { /* ignore */ }
            $items = $itemModel->getByTour($id);
        }
        require __DIR__ . '/../views/tours/itinerary.php';
    }

    public function itinerary_add_item($post)
    {
        $tourId = (int)($post['tour_id'] ?? 0);
        if (!$tourId) { header('Location: ' . BASE_URL . '?r=tours'); exit; }
        $data = [[
            'day_number' => (int)($post['day_number'] ?? 1),
            'activity_time' => trim($post['activity_time'] ?? '08:00'),
            'end_time' => trim($post['end_time'] ?? ''),
            'slot' => trim($post['slot'] ?? ''),
            'title' => trim($post['title'] ?? ''),
            'details' => trim($post['details'] ?? ''),
            'meal_plan' => trim($post['meal_plan'] ?? ''),
        ]];
        try { $this->itineraryItemModel->addBulk($tourId, $data); } catch (Exception $e) { /* ignore */ }
        header('Location: ' . BASE_URL . '?r=tours_itinerary&id=' . $tourId);
        exit;
    }

    public function itinerary_update_item($id, $post)
    {
        $itemId = (int)$id;
        $tourId = (int)($post['tour_id'] ?? 0);
        if (!$itemId || !$tourId) { header('Location: ' . BASE_URL . '?r=tours'); exit; }
        try {
            $this->itineraryItemModel->updateItem($itemId, $tourId, [
                'day_number' => (int)($post['day_number'] ?? 1),
                'activity_time' => trim($post['activity_time'] ?? '08:00'),
                'end_time' => trim($post['end_time'] ?? ''),
                'slot' => trim($post['slot'] ?? ''),
                'title' => trim($post['title'] ?? ''),
                'details' => trim($post['details'] ?? ''),
                'meal_plan' => trim($post['meal_plan'] ?? ''),
            ]);
        } catch (Exception $e) { /* ignore */ }
        header('Location: ' . BASE_URL . '?r=tours_itinerary&id=' . $tourId);
        exit;
    }

    public function itinerary_delete_item($id, $tourId)
    {
        $itemId = (int)$id; $tourId = (int)$tourId;
        if (!$itemId || !$tourId) { header('Location: ' . BASE_URL . '?r=tours'); exit; }
        try {
            $stmt = $this->itineraryItemModel->pdo->prepare(
                "DELETE FROM {$this->itineraryItemModel->table_name} WHERE id=? AND tour_id=?"
            );
            $stmt->execute([$itemId, $tourId]);
        } catch (Exception $e) { /* ignore */ }
        header('Location: ' . BASE_URL . '?r=tours_itinerary&id=' . $tourId);
        exit;
    }

    public function index()
    {
        $q = trim($_GET['q'] ?? '');
        $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
        $statusFilter = trim($_GET['status'] ?? '');
        $tours = $this->tourModel->all();
        if ($q !== '' || $categoryId > 0 || $statusFilter !== '') {
            $tours = array_values(array_filter($tours, function ($row) use ($q, $categoryId, $statusFilter) {
                $ok = true;
                if ($q !== '') {
                    $hay = strtolower((string)($row['title'] ?? '') . ' ' . (string)($row['description'] ?? ''));
                    $ok = $ok && (strpos($hay, strtolower($q)) !== false);
                }
                if ($categoryId > 0) {
                    $ok = $ok && ((int)($row['category_id'] ?? 0) === $categoryId);
                }
                if ($statusFilter !== '') {
                    $ok = $ok && ((string)($row['status'] ?? '') === $statusFilter);
                }
                return $ok;
            }));
        }
        $types = $this->types;
        $statuses = $this->statuses;
        $categories = $this->categoryModel->all();
        $priceByTour = [];
        foreach ($tours as $tour) {
            $priceByTour[$tour['id']] = $this->priceModel->getByTour($tour['id']);
        }

        // Lấy chính sách hủy cho mỗi tour
        $pdo = (new \BaseModel())->getConnection();
        $stmt = $pdo->query("SELECT t.id AS tour_id, cp.name AS cancellation_policy_name, cp.refund_percentage AS cancellation_policy_refund
                               FROM tours t
                               LEFT JOIN cancellation_policies cp ON t.cancellation_policy_id = cp.id
                               WHERE cp.id IS NOT NULL");
        $policyMap = [];
        foreach ($stmt->fetchAll() as $row) {
            $policyMap[$row['tour_id']] = [
                'name' => $row['cancellation_policy_name'],
                'refund' => $row['cancellation_policy_refund']
            ];
        }
        foreach ($tours as &$tour) {
            if (isset($policyMap[$tour['id']])) {
                $tour['cancellation_policy_name'] = $policyMap[$tour['id']]['name'];
                $tour['cancellation_policy_refund'] = $policyMap[$tour['id']]['refund'];
            } else {
                $tour['cancellation_policy_name'] = null;
                $tour['cancellation_policy_refund'] = null;
            }
        }

        require __DIR__ . '/../views/tours/index.php';
    }

    public function show($id)
    {
        $id = (int)$id;
        $tour = $this->tourModel->find($id);
        if (!$tour) { header('Location: ' . BASE_URL . '?r=tours'); exit; }
        $types = $this->types; $statuses = $this->statuses;
        $category = $this->categoryModel->find($tour['category_id'] ?? 0);
        $price = $this->priceModel->getByTour($id);
        if (!$price && isset($tour['price'])) { $price = ['adult_price'=>(float)$tour['price'],'child_price'=>0,'infant_price'=>0]; }
        $gallery = $this->imageModel->getByTour($id);
        $itineraries = $this->itineraryModel->getByTour($id);
        $items = $this->itineraryItemModel->getByTour($id);
        $supplierMain = null; if (!empty($tour['supplier_id'])) { try { $supplierMain = (new SupplierModel())->find($tour['supplier_id']); } catch (Throwable $e) {} }
        $supplierServices = [];
        try {
            $pdo = (new \BaseModel())->getConnection();
            $sql = "SELECT s.id, s.name, s.type, s.phone, s.email, te.expense_type AS service_type, te.description AS service_description
                    FROM tour_expenses te JOIN suppliers s ON te.supplier_id = s.id
                    WHERE te.tour_id = ? AND te.is_actual_cost = 0 AND te.amount = 0 ORDER BY s.name";
            $stmt = $pdo->prepare($sql); $stmt->execute([$id]);
            $supplierServices = $stmt->fetchAll();
            foreach ($supplierServices as &$r){
                $r['name_clean'] = trim((string)($r['name'] ?? ''));
                $r['status_parsed'] = null;
                if (!empty($r['service_description']) && stripos($r['service_description'], 'service:') === 0) {
                    $r['service_description'] = substr($r['service_description'], 8);
                }
                if (preg_match('/^(.*?)\s*\[[^:]*:\s*(\w+)\]/i', (string)$r['name'], $m)) {
                    $r['name_clean'] = trim($m[1]);
                    $r['status_parsed'] = strtolower($m[2]);
                } elseif (!empty($r['service_description']) && preg_match('/\[[^:]*:\s*(\w+)\]/i', (string)$r['service_description'], $m2)) {
                    $r['status_parsed'] = strtolower($m2[1]);
                }
                // Loại bỏ các tag dạng [CONFIRM:xxx] hoặc các tag trong [] khỏi mô tả
                if (!empty($r['service_description'])) {
                    $r['service_description'] = preg_replace('/\s*\[[^\]]*\]\s*/', '', (string)$r['service_description']);
                }
            }
            unset($r);
        } catch (Throwable $e) {}
        $policy = null;
        try {
            $pdo = (new \BaseModel())->getConnection();
            $stmt = $pdo->prepare("SELECT cp.name, cp.refund_percentage, cp.description FROM tours t LEFT JOIN cancellation_policies cp ON t.cancellation_policy_id = cp.id WHERE t.id = ?");
            $stmt->execute([$id]); $policy = $stmt->fetch();
        } catch (Throwable $e) {}

        // Dashboard data: schedules, bookings, stats
        $schedules = [];
        try {
            $pdoSch = (new \BaseModel())->getConnection();
            $stmtSch = $pdoSch->prepare("SELECT ts.*, ug.full_name AS guide_name, ud.full_name AS driver_name
                                          FROM tour_schedules ts
                                          LEFT JOIN users ug ON ts.guide_user_id = ug.id
                                          LEFT JOIN users ud ON ts.driver_user_id = ud.id
                                          WHERE ts.tour_id = ? ORDER BY ts.start_date DESC");
            $stmtSch->execute([$id]);
            $schedules = $stmtSch->fetchAll();
        } catch (Throwable $e) { $schedules = []; }
        $totalRuns = is_array($schedules) ? count($schedules) : 0;
        $totalPassengers = 0; $totalRevenue = 0.0; $bookings = [];
        try {
            $pdo2 = (new \BaseModel())->getConnection();
            $stmtPax = $pdo2->prepare("SELECT COALESCE(SUM(b.total_guests),0) AS pax
                                        FROM bookings b
                                        JOIN tour_schedules ts ON b.schedule_id = ts.id
                                        WHERE ts.tour_id = ? AND (b.booking_status IS NULL OR b.booking_status != 'canceled')");
            $stmtPax->execute([$id]);
            $totalPassengers = (int)($stmtPax->fetchColumn() ?: 0);

            $stmtRev = $pdo2->prepare("SELECT COALESCE(SUM(b.total_price),0) AS revenue
                                       FROM bookings b
                                       JOIN tour_schedules ts ON b.schedule_id = ts.id
                                       WHERE ts.tour_id = ? AND b.booking_status = 'confirmed'");
            $stmtRev->execute([$id]);
            $totalRevenue = (float)($stmtRev->fetchColumn() ?: 0);

            $stmtBk = $pdo2->prepare("SELECT b.*, u.full_name AS customer_name, ts.start_date, ts.end_date
                                       FROM bookings b
                                       JOIN tour_schedules ts ON b.schedule_id = ts.id
                                       LEFT JOIN users u ON b.customer_user_id = u.id
                                       WHERE ts.tour_id = ?
                                       ORDER BY b.id DESC");
            $stmtBk->execute([$id]);
            $bookings = $stmtBk->fetchAll();
        } catch (Throwable $e) {}

        $tourActive = !in_array(($tour['status'] ?? ''), ['closed','finished'], true);

        // Trend metrics (last 30 days vs previous 30 days)
        $runsTrendPct = 0.0; $paxTrendPct = 0.0; $revTrendPct = 0.0;
        try {
            $pdoT = (new \BaseModel())->getConnection();
            $curStart = (new \DateTime('now'))->modify('-30 days')->format('Y-m-d');
            $curEnd = (new \DateTime('now'))->format('Y-m-d');
            $prevStart = (new \DateTime('now'))->modify('-60 days')->format('Y-m-d');
            $prevEnd = (new \DateTime('now'))->modify('-31 days')->format('Y-m-d');

            $stmtRunsCur = $pdoT->prepare("SELECT COUNT(*) FROM tour_schedules WHERE tour_id = ? AND DATE(start_date) BETWEEN ? AND ?");
            $stmtRunsCur->execute([$id, $curStart, $curEnd]);
            $runsCur = (int)$stmtRunsCur->fetchColumn();
            $stmtRunsPrev = $pdoT->prepare("SELECT COUNT(*) FROM tour_schedules WHERE tour_id = ? AND DATE(start_date) BETWEEN ? AND ?");
            $stmtRunsPrev->execute([$id, $prevStart, $prevEnd]);
            $runsPrev = (int)$stmtRunsPrev->fetchColumn();
            $runsTrendPct = ($runsPrev > 0) ? (($runsCur - $runsPrev) / $runsPrev * 100.0) : (($runsCur > 0) ? 100.0 : 0.0);

            $stmtPaxCur = $pdoT->prepare("SELECT COALESCE(SUM(b.total_guests),0) FROM bookings b JOIN tour_schedules ts ON b.schedule_id = ts.id WHERE ts.tour_id = ? AND (b.booking_status IS NULL OR b.booking_status != 'canceled') AND DATE(b.date_booked) BETWEEN ? AND ?");
            $stmtPaxCur->execute([$id, $curStart, $curEnd]);
            $paxCur = (int)$stmtPaxCur->fetchColumn();
            $stmtPaxPrev = $pdoT->prepare("SELECT COALESCE(SUM(b.total_guests),0) FROM bookings b JOIN tour_schedules ts ON b.schedule_id = ts.id WHERE ts.tour_id = ? AND (b.booking_status IS NULL OR b.booking_status != 'canceled') AND DATE(b.date_booked) BETWEEN ? AND ?");
            $stmtPaxPrev->execute([$id, $prevStart, $prevEnd]);
            $paxPrev = (int)$stmtPaxPrev->fetchColumn();
            $paxTrendPct = ($paxPrev > 0) ? (($paxCur - $paxPrev) / $paxPrev * 100.0) : (($paxCur > 0) ? 100.0 : 0.0);

            $stmtRevCur = $pdoT->prepare("SELECT COALESCE(SUM(b.total_price),0) FROM bookings b JOIN tour_schedules ts ON b.schedule_id = ts.id WHERE ts.tour_id = ? AND b.booking_status = 'confirmed' AND DATE(b.date_booked) BETWEEN ? AND ?");
            $stmtRevCur->execute([$id, $curStart, $curEnd]);
            $revCur = (float)$stmtRevCur->fetchColumn();
            $stmtRevPrev = $pdoT->prepare("SELECT COALESCE(SUM(b.total_price),0) FROM bookings b JOIN tour_schedules ts ON b.schedule_id = ts.id WHERE ts.tour_id = ? AND b.booking_status = 'confirmed' AND DATE(b.date_booked) BETWEEN ? AND ?");
            $stmtRevPrev->execute([$id, $prevStart, $prevEnd]);
            $revPrev = (float)$stmtRevPrev->fetchColumn();
            $revTrendPct = ($revPrev > 0.0) ? (($revCur - $revPrev) / $revPrev * 100.0) : (($revCur > 0.0) ? 100.0 : 0.0);
        } catch (Throwable $e) {}

        $stats = [
            'totalRuns' => $totalRuns,
            'totalPassengers' => $totalPassengers,
            'totalRevenue' => $totalRevenue,
            'tourActive' => $tourActive,
            'runsTrendPct' => $runsTrendPct,
            'paxTrendPct' => $paxTrendPct,
            'revTrendPct' => $revTrendPct,
            'tourCode' => !empty($tour['code'] ?? '') ? (string)$tour['code'] : ('T-' . (int)$tour['id']),
            'frontendUrl' => BASE_URL . '?r=tours_show&id=' . (int)$id,
        ];

        // no driver map needed, names already joined

        require __DIR__ . '/../views/tours/show.php';
    }

    public function create()
    {
        $tour = null;
        $categories = $this->categoryModel->all();
        $suppliers = $this->supplierModel->all();
        // Master suppliers from `suppliers` table for service mapping
        try { $supplierMaster = (new SupplierModel())->getAll(); } catch (Throwable $e) { $supplierMaster = []; }
        $tourSuppliers = [];
        $itineraries = [];
        $itineraryItems = [];
        $price = ['adult_price' => 0, 'child_price' => 0, 'infant_price' => 0];
        $gallery = [];
        $types = $this->types;
        $statuses = $this->statuses;
        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];

        // Lấy danh sách chính sách hủy
        $stmt = (new \BaseModel())->getConnection()->query("SELECT id, name, refund_percentage FROM cancellation_policies WHERE is_active = 1 ORDER BY name");
        $cancellationPolicies = $stmt->fetchAll();

        require __DIR__ . '/../views/tours/form.php';
    }

    public function store()
    {
        $payload = $this->filterTourData($_POST);
        $errors = $this->validate($payload);

        if (!empty($errors)) {
            redirect_with_flash(BASE_URL . '?r=tours_create', $errors, $_POST);
        }

        // Gán giá legacy để fallback nếu thiếu bảng tour_prices
        if (isset($_POST['adult_price'])) {
            $payload['price'] = (float)$_POST['adult_price'];
        }

        // cover: ưu tiên URL nếu có, nếu không dùng file upload
        $coverUrl = trim($_POST['cover_url'] ?? '');
        if ($coverUrl !== '') {
            $payload['cover_image'] = $coverUrl;
        } elseif (!empty($_FILES['cover_image']['name'])) {
            $coverPath = upload_file('tours/cover', $_FILES['cover_image']);
            if ($coverPath) { $payload['cover_image'] = $coverPath; }
        }

        $tourId = $this->tourModel->create($payload);

        // Lưu quan hệ: lịch trình tổng quan, giá và lịch trình chi tiết theo giờ (nếu có)
        $this->storeRelations($tourId);

        // Auto-plan: nếu người dùng không nhập bất kỳ lịch trình nào (tổng quan & chi tiết), hệ thống sẽ tạo mặc định
        $itineraryDays = $_POST['itinerary_day'] ?? [];
        $itineraryLocations = $_POST['itinerary_location'] ?? [];
        $itineraryActivities = $_POST['itinerary_activity'] ?? [];
        $detailDays = $_POST['it_item_day'] ?? [];
        $detailStarts = $_POST['it_item_start'] ?? [];
        $detailTitles = $_POST['it_item_title'] ?? [];

        $hasAnyItinerary = false;
        foreach ($itineraryDays as $i => $d) {
            if ($d !== '' || !empty($itineraryLocations[$i] ?? null) || !empty($itineraryActivities[$i] ?? null)) { $hasAnyItinerary = true; break; }
        }
        if (!$hasAnyItinerary) {
            foreach ($detailDays as $i => $d) {
                if ($d !== '' || !empty($detailStarts[$i] ?? null) || !empty($detailTitles[$i] ?? null)) { $hasAnyItinerary = true; break; }
            }
        }

        if (!$hasAnyItinerary) {
            // 1) Thêm 1 dòng lịch trình mặc định (tổng quan)
            $this->itineraryModel->replace($tourId, [[
                'day_number' => 1,
                'location' => 'Đang cập nhật',
                'activities' => 'Hệ thống sẽ cập nhật chi tiết lịch trình sau',
            ]]);
        }

        // 2) Tự tạo lịch khởi hành mặc định + gán HDV
        $start = (new DateTime('now'))->modify('+7 days')->format('Y-m-d');
        $durationDays = (int)($_POST['duration_days'] ?? 3);
        if ($durationDays < 1) { $durationDays = 3; }
        $end = (new DateTime($start))->modify('+' . max(0, $durationDays - 1) . ' days')->format('Y-m-d');
        try {
            $guideId = $this->scheduleModel->findAvailableGuide($start, $end);
            if ($guideId === null) {
                $guides = $this->userModel->getGuides();
                $guideId = !empty($guides) ? (int)$guides[0]['id'] : null;
            }
            $this->scheduleModel->create([
                'tour_id' => $tourId,
                'start_date' => $start,
                'end_date' => $end,
                'guide_user_id' => $guideId,
                'driver_user_id' => null,
                'max_capacity' => 20,
            ]);
        } catch (Exception $e) {
            // Bỏ qua lỗi auto-schedule để không chặn quy trình tạo tour
        }

        // 3) Tự tạo chi tiết lịch trình theo khung giờ (sáng / trưa / chiều / tối)
        if (!$hasAnyItinerary) {
            $items = [];
            for ($d = 1; $d <= $durationDays; $d++) {
                $items[] = ['day_number'=>$d,'activity_time'=>'07:30:00','end_time'=>'11:30:00','slot'=>'morning','title'=>'Buổi sáng','details'=>'Đang cập nhật hoạt động buổi sáng','meal_plan'=>'Sáng'];
                $items[] = ['day_number'=>$d,'activity_time'=>'11:30:00','end_time'=>'13:30:00','slot'=>'noon','title'=>'Buổi trưa','details'=>'Đang cập nhật bữa trưa','meal_plan'=>'Trưa'];
                $items[] = ['day_number'=>$d,'activity_time'=>'14:00:00','end_time'=>'17:30:00','slot'=>'afternoon','title'=>'Buổi chiều','details'=>'Đang cập nhật hoạt động buổi chiều','meal_plan'=>''];
                $items[] = ['day_number'=>$d,'activity_time'=>'19:00:00','end_time'=>'21:00:00','slot'=>'evening','title'=>'Buổi tối','details'=>'Đang cập nhật hoạt động buổi tối','meal_plan'=>'Tối'];
            }
            try { $this->itineraryItemModel->addBulk($tourId, $items); } catch (Exception $e) { /* ignore */ }
        }
        if (!empty($_FILES['gallery']['name'])) {
            $paths = upload_multiple_files('tours/gallery', $_FILES['gallery']);
            $this->imageModel->addGallery($tourId, $paths);
        }

        header('Location: ' . BASE_URL . '?r=tours');
        exit;
    }

    public function edit($id)
    {
        $tour = $this->tourModel->find($id);
        if (!$tour) {
            header('Location: ' . BASE_URL . '?r=tours');
            exit;
        }
        $categories = $this->categoryModel->all();
        $suppliers = $this->supplierModel->all();
        // Master suppliers from `suppliers` table for service mapping
        try { $supplierMaster = (new SupplierModel())->getAll(); } catch (Throwable $e) { $supplierMaster = []; }
        $itineraries = $this->itineraryModel->getByTour($id);
        $itineraryItems = $this->itineraryItemModel->getByTour($id);
        $price = $this->priceModel->getByTour($id) ?? ['adult_price' => 0, 'child_price' => 0, 'infant_price' => 0];
        $gallery = $this->imageModel->getByTour($id);
        $types = $this->types;
        $statuses = $this->statuses;
        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];

        // Lấy danh sách chính sách hủy
        $stmt = (new \BaseModel())->getConnection()->query("SELECT id, name, refund_percentage FROM cancellation_policies WHERE is_active = 1 ORDER BY name");
        $cancellationPolicies = $stmt->fetchAll();

        // Prefill tour supplier services from tour_expenses (amount=0, is_actual_cost=0 OR any)
        try {
            $pdo = (new \BaseModel())->getConnection();
            $q = $pdo->prepare("SELECT te.supplier_id, te.expense_type AS service_type, te.description 
                                FROM tour_expenses te 
                                WHERE te.tour_id = ? AND te.is_actual_cost = 0 AND te.amount = 0");
            $q->execute([(int)$id]);
            $tourSuppliers = $q->fetchAll();
            // Loại bỏ prefix 'service:' nếu còn
            foreach ($tourSuppliers as &$ts) {
                if (!empty($ts['description']) && stripos($ts['description'], 'service:') === 0) {
                    $ts['description'] = substr($ts['description'], 8);
                }
                if (!empty($ts['description'])) {
                    $ts['description'] = preg_replace('/\s*\[[^\]]*\]\s*/', '', (string)$ts['description']);
                }
            }
            unset($ts);
        } catch (Throwable $e) { $tourSuppliers = []; }

        require __DIR__ . '/../views/tours/form.php';
    }

    public function update($id)
    {
        $tour = $this->tourModel->find($id);
        if (!$tour) {
            header('Location: ' . BASE_URL . '?r=tours');
            exit;
        }

        $payload = $this->filterTourData($_POST);
        $errors = $this->validate($payload, $id);

        if (!empty($errors)) {
            redirect_with_flash(BASE_URL . '?r=tours_edit&id=' . $id, $errors, $_POST);
        }

        // Gán giá legacy để fallback nếu thiếu bảng tour_prices
        if (isset($_POST['adult_price'])) {
            $payload['price'] = (float)$_POST['adult_price'];
        }

        // cover: ưu tiên URL nếu có, nếu không dùng file upload; nếu trống cả hai thì giữ ảnh cũ
        $coverUrl = trim($_POST['cover_url'] ?? '');
        if ($coverUrl !== '') {
            $payload['cover_image'] = $coverUrl;
        } elseif (!empty($_FILES['cover_image']['name'])) {
            $coverPath = upload_file('tours/cover', $_FILES['cover_image']);
            if ($coverPath) { $payload['cover_image'] = $coverPath; }
        }

        $this->tourModel->update($id, $payload);
        $this->storeRelations($id);

        $removeIdsRaw = $_POST['remove_images'] ?? [];
        if (is_string($removeIdsRaw)) { $removeIdsRaw = [$removeIdsRaw]; }
        $removeIds = array_values(array_filter($removeIdsRaw, function($v){ return preg_match('/^\d+$/', (string)$v) && (int)$v > 0; }));
        if (!empty($removeIds)) {
            $this->imageModel->removeByIds($removeIds);
        }

        if (!empty($_FILES['gallery']['name'])) {
            $paths = upload_multiple_files('tours/gallery', $_FILES['gallery']);
            $this->imageModel->addGallery($id, $paths);
        }

        header('Location: ' . BASE_URL . '?r=tours');
        exit;
    }

    public function delete($id)
    {
        // Chỉ admin mới được phép xóa tour
        $role = $_SESSION['role'] ?? null;
        if ($role !== 'admin') {
            header('Location: ' . BASE_URL . '?r=tours');
            exit;
        }

        $tour = $this->tourModel->find($id);
        if ($tour && !empty($tour['cover_image'])) {
            $path = PATH_ASSETS_UPLOADS . $tour['cover_image'];
            if (is_file($path)) {
                unlink($path);
            }
        }
        // Xóa chi tiết lịch trình theo giờ (nếu có)
        try { $this->itineraryItemModel->deleteByTour($id); } catch (Exception $e) { /* ignore */ }
        // Kiểm tra xem có thể xóa tour không
        $result = $this->tourModel->delete($id);
        
        if ($result['success']) {
            // Nếu xóa thành công, xóa các dữ liệu liên quan
            $this->itineraryModel->replace($id, []);
            try { $this->scheduleModel->deleteByTour($id); } catch (Exception $e) { /* ignore */ }
            $this->priceModel->deleteByTour($id);
            $this->imageModel->deleteByTour($id);
            
            // Set flash message và redirect
            flash_set('success', $result['message']);
            header('Location: ' . BASE_URL . '?r=tours');
            exit;
        } else {
            // Nếu không thể xóa, set flash message và redirect back
            flash_set('danger', $result['message']);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    protected function filterTourData($input)
    {
        return [
            'category_id' => !empty($input['category_id']) ? (int)$input['category_id'] : null,
            'title' => trim($input['title'] ?? ''),
            'description' => trim($input['description'] ?? ''),
            'tour_type' => $input['tour_type'] ?? 'domestic',
            'status' => $input['status'] ?? 'upcoming',
            'supplier_id' => !empty($input['supplier_id']) ? (int)$input['supplier_id'] : null,
            'cancellation_policy_id' => !empty($input['cancellation_policy_id']) ? (int)$input['cancellation_policy_id'] : null,
            'policy' => trim($input['policy'] ?? ''),
        ];
    }

    protected function storeRelations($tourId)
    {
        $itineraryDays = $_POST['itinerary_day'] ?? [];
        $itineraryLocations = $_POST['itinerary_location'] ?? [];
        $itineraryActivities = $_POST['itinerary_activity'] ?? [];

        $itineraryPayload = [];
        foreach ($itineraryDays as $index => $day) {
            if ($day === '' && empty($itineraryLocations[$index]) && empty($itineraryActivities[$index])) {
                continue;
            }
            $itineraryPayload[] = [
                'day_number' => (int)$day,
                'location' => trim($itineraryLocations[$index] ?? ''),
                'activities' => trim($itineraryActivities[$index] ?? ''),
            ];
        }
        if (!empty($itineraryPayload)) {
            $this->itineraryModel->replace($tourId, $itineraryPayload);
        }

        // Lưu lịch trình chi tiết theo khung giờ nếu được nhập từ form tour
        $detailDays   = $_POST['it_item_day'] ?? [];
        $detailStarts = $_POST['it_item_start'] ?? [];
        $detailEnds   = $_POST['it_item_end'] ?? [];
        $detailSlots  = $_POST['it_item_slot'] ?? [];
        $detailTitles = $_POST['it_item_title'] ?? [];
        $detailNotes  = $_POST['it_item_details'] ?? [];
        $detailMeals  = $_POST['it_item_location'] ?? [];

        $detailItems = [];
        foreach ($detailDays as $i => $day) {
            $day = trim((string)$day);
            $start = trim($detailStarts[$i] ?? '');
            $title = trim($detailTitles[$i] ?? '');
            $details = trim($detailNotes[$i] ?? '');

            if ($day === '' && $start === '' && $title === '' && $details === '') {
                continue;
            }

            $detailItems[] = [
                'day_number'    => (int)($day !== '' ? $day : 1),
                'activity_time' => $start !== '' ? $start . ':00' : '08:00:00',
                'end_time'      => ($detailEnds[$i] ?? '') !== '' ? ($detailEnds[$i] . ':00') : null,
                'slot'          => trim($detailSlots[$i] ?? ''),
                'title'         => $title,
                'details'       => $details,
                'meal_plan'     => trim($detailMeals[$i] ?? ''),
            ];
        }

        try {
            if (!empty($detailItems)) {
                $this->itineraryItemModel->deleteByTour($tourId);
                $this->itineraryItemModel->addBulk($tourId, $detailItems);
            }
        } catch (Exception $e) {
        }

        $prices = [
            'adult_price' => (float)($_POST['adult_price'] ?? 0),
            'child_price' => (float)($_POST['child_price'] ?? 0),
            'infant_price' => (float)($_POST['infant_price'] ?? 0),
        ];
        $this->priceModel->upsert($tourId, $prices);

        // Lưu ánh xạ NCC dịch vụ cho tour → dùng bảng tour_expenses (amount=0, is_actual_cost=0)
        $tsSupplierIds = $_POST['ts_supplier_id'] ?? [];
        $tsServiceTypes = $_POST['ts_service_type'] ?? [];
        $tsDescriptions = $_POST['ts_description'] ?? [];
        $allowedServices = ['hotel','restaurant','transport','ticket','insurance'];
        try {
            $pdo = (new \BaseModel())->getConnection();
            // Xóa ánh xạ cũ (amount=0, is_actual_cost=0) theo loại dịch vụ cho tour
            $in = implode(',', array_fill(0, count($allowedServices), '?'));
            $sqlDel = "DELETE FROM tour_expenses WHERE tour_id = ? AND is_actual_cost = 0 AND amount = 0 AND expense_type IN ($in)";
            $del = $pdo->prepare($sqlDel);
            $paramsDel = array_merge([(int)$tourId], $allowedServices);
            $del->execute($paramsDel);
            // Chèn lại theo form
            foreach ($tsSupplierIds as $i => $sid) {
                $sid = (int)$sid;
                $stype = trim($tsServiceTypes[$i] ?? '');
                if ($sid <= 0 || $stype === '' || !in_array($stype, $allowedServices, true)) { continue; }
                $desc = trim($tsDescriptions[$i] ?? '');
                // Bỏ prefix 'service:' nếu người dùng paste từ dữ liệu cũ
                if (stripos($desc, 'service:') === 0) { $desc = substr($desc, 8); }
                $ins = $pdo->prepare("INSERT INTO tour_expenses (tour_id, supplier_id, expense_type, description, amount, is_actual_cost, date_incurred) VALUES (?,?,?,?,0,0,CURDATE())");
                $ins->execute([(int)$tourId, $sid, $stype, $desc !== '' ? $desc : $stype]);
            }
        } catch (Throwable $e) {
            // bỏ qua lỗi mapping để không chặn lưu tour
        }
    }

    // API: Trả về danh sách NCC dịch vụ theo tour (dùng khi tạo booking)
    public function suppliersJson()
    {
        $tourId = (int)($_GET['tour_id'] ?? 0);
        header('Content-Type: application/json; charset=utf-8');
        if ($tourId <= 0) { echo json_encode([]); exit; }
        try {
            $pdo = (new \BaseModel())->getConnection();
            $sql = "SELECT s.id, s.name, s.type, s.phone, s.email, te.expense_type AS service_type, te.description AS service_description
                    FROM tour_expenses te
                    JOIN suppliers s ON te.supplier_id = s.id
                    WHERE te.tour_id = ? AND te.is_actual_cost = 0 AND te.amount = 0
                    ORDER BY s.name";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$tourId]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            // Bỏ prefix 'service:' nếu còn
            foreach ($rows as &$r) {
                if (!empty($r['service_description']) && stripos($r['service_description'], 'service:') === 0) {
                    $r['service_description'] = substr($r['service_description'], 8);
                }
            }
            unset($r);
            echo json_encode($rows);
        } catch (Throwable $e) {
            echo json_encode([]);
        }
        exit;
    }

    protected function validate($data, $ignoreId = null)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Tên tour không được để trống.';
        }

        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Vui lòng chọn danh mục tour.';
        } elseif (!$this->categoryModel->find($data['category_id'])) {
            $errors['category_id'] = 'Danh mục không hợp lệ.';
        }

        if (!array_key_exists($data['tour_type'], $this->types)) {
            $errors['tour_type'] = 'Loại tour không hợp lệ.';
        }

        if (!array_key_exists($data['status'], $this->statuses)) {
            $errors['status'] = 'Trạng thái tour không hợp lệ.';
        }

        // Nhà cung cấp bắt buộc
        if (empty($data['supplier_id'])) {
            $errors['supplier_id'] = 'Vui lòng chọn nhà cung cấp.';
        } else {
            $ids = array_column($this->supplierModel->all(), 'id');
            if (!in_array((int)$data['supplier_id'], array_map('intval', $ids), true)) {
                $errors['supplier_id'] = 'Nhà cung cấp không tồn tại.';
            }
        }

        // Giá người lớn bắt buộc
        $adult = $_POST['adult_price'] ?? null;
        if ($adult === null || $adult === '' ) {
            $errors['adult_price'] = 'Vui lòng nhập giá người lớn.';
        } elseif (!is_numeric($adult) || (float)$adult < 0) {
            $errors['adult_price'] = 'Giá người lớn không hợp lệ.';
        }

        // Ảnh cover: yêu cầu có URL hoặc upload khi tạo mới; khi cập nhật nếu chưa có sẵn trên tour thì cũng yêu cầu
        $needCover = $ignoreId === null; // tạo mới
        $existingCover = null;
        if ($ignoreId !== null) {
            $exist = $this->tourModel->find($ignoreId);
            $existingCover = $exist['cover_image'] ?? '';
            if (empty($existingCover)) { $needCover = true; }
        }
        if ($needCover) {
            $coverUrl = trim($_POST['cover_url'] ?? '');
            $hasFile = !empty($_FILES['cover_image']['name']);
            if ($coverUrl === '' && !$hasFile) {
                $errors['cover_image'] = 'Vui lòng nhập Link ảnh cover hoặc tải lên file ảnh.';
            }
            if ($coverUrl !== '' && !preg_match('#^https?://#i', $coverUrl)) {
                $errors['cover_url'] = 'Link ảnh cover không hợp lệ.';
            }
        } else {
            $coverUrl = trim($_POST['cover_url'] ?? '');
            if ($coverUrl !== '' && !preg_match('#^https?://#i', $coverUrl)) {
                $errors['cover_url'] = 'Link ảnh cover không hợp lệ.';
            }
        }

        return $errors;
    }
}

