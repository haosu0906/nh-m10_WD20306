<?php
require_once __DIR__ . '/../../assets/configs/db.php';
require_once __DIR__ . '/../../models/admin/booking.php';
require_once __DIR__ . '/../../models/TourModel.php';
require_once __DIR__ . '/../../models/ScheduleModel.php';

function booking_index() {
    $status = $_GET['status'] ?? "";
    $items = booking_all($status);

    // Chuẩn bị map tên khách hàng, tên tour, HDV và nhà cung cấp để hiển thị đẹp hơn
    $pdo = DB::get();

    // Map khách hàng theo id
    $stmt = $pdo->query("SELECT id, full_name FROM users");
    $userRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $customersById = [];
    foreach ($userRows as $u) {
        $customersById[(int)$u['id']] = $u['full_name'] ?: ('User #' . $u['id']);
    }

    // Map tour theo id
    $stmt = $pdo->query("SELECT id, title FROM tours");
    $tourRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $toursById = [];
    foreach ($tourRows as $t) {
        $toursById[(int)$t['id']] = $t['title'] ?: ('Tour #' . $t['id']);
    }

    // Map nhà cung cấp chính theo tour (nếu tours có supplier_id)
    $suppliersByTour = [];
    $serviceTypeMap = [
        'hotel'      => 'Khách sạn',
        'restaurant' => 'Nhà hàng',
        'transport'  => 'Vận chuyển',
        'ticket'     => 'Vé tham quan',
        'insurance'  => 'Bảo hiểm',
        'guide'      => 'Hướng dẫn viên',
        'meal'       => 'Ăn uống',
        'entertain'  => 'Giải trí',
        'other'      => 'Dịch vụ khác',
    ];
    try {
        $stmt = $pdo->query("SELECT t.id AS tour_id, s.name, s.service_type
                              FROM tours t
                              LEFT JOIN tour_suppliers s ON t.supplier_id = s.id");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $tid = (int)($r['tour_id'] ?? 0);
            if ($tid <= 0) {
                continue;
            }
            $label = $r['name'] ?? '';
            if ($label === '' && isset($suppliersByTour[$tid])) {
                continue;
            }
            if (!empty($r['service_type'])) {
                $type = (string)$r['service_type'];
                $typeLabel = $serviceTypeMap[$type] ?? $type;
                $label .= $label !== '' ? ' - ' . $typeLabel : $typeLabel;
            }
            if ($label !== '') {
                $suppliersByTour[$tid] = $label;
            }
        }
    } catch (PDOException $e) {
        $suppliersByTour = [];
    }

    // Map HDV chính theo tour (lấy theo lịch khởi hành đầu tiên tìm được)
    $stmt = $pdo->query("SELECT ts.tour_id, u.full_name AS guide_name FROM tour_schedules ts LEFT JOIN users u ON u.id = ts.guide_user_id WHERE ts.guide_user_id IS NOT NULL ORDER BY ts.start_date ASC");
    $guideRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $guidesByTour = [];
    foreach ($guideRows as $g) {
        $tid = (int)$g['tour_id'];
        if (!isset($guidesByTour[$tid])) {
            $guidesByTour[$tid] = $g['guide_name'] ?: '';
        }
    }

    require __DIR__ . '/../../views/booking/list_booking.php';
}

function booking_detail() {
    $id = $_GET['id'] ?? 0;
    $item = booking_find($id);
    $logs = booking_logs($id);

    // Lấy thêm thông tin liên quan: tour, khách hàng, sales, danh sách khách
    $pdo = DB::get();

    $tour = null;
    $customer = null;
    $sales = null;
    $guests = [];

    if ($item) {
        if (!empty($item['tour_id'])) {
            try {
                $stmt = $pdo->prepare("SELECT t.id AS tour_id, t.title, t.supplier_id, s.name AS supplier_name, s.service_type
                                        FROM tours t
                                        LEFT JOIN tour_suppliers s ON t.supplier_id = s.id
                                        WHERE t.id = :id");
                $stmt->execute(['id' => (int)$item['tour_id']]);
                $tour = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($tour && !empty($tour['service_type'])) {
                    $serviceTypeMap = [
                        'hotel'      => 'Khách sạn',
                        'restaurant' => 'Nhà hàng',
                        'transport'  => 'Vận chuyển',
                        'ticket'     => 'Vé tham quan',
                        'insurance'  => 'Bảo hiểm',
                        'guide'      => 'Hướng dẫn viên',
                        'meal'       => 'Ăn uống',
                        'entertain'  => 'Giải trí',
                        'other'      => 'Dịch vụ khác',
                    ];
                    $type = (string)$tour['service_type'];
                    $tour['service_type'] = $serviceTypeMap[$type] ?? $type;
                }
            } catch (PDOException $e) {
                $stmt = $pdo->prepare("SELECT id AS tour_id, title FROM tours WHERE id = :id");
                $stmt->execute(['id' => (int)$item['tour_id']]);
                $tour = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }

        if (!empty($item['customer_user_id'])) {
            $stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = :id");
            $stmt->execute(['id' => (int)$item['customer_user_id']]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!empty($item['sales_user_id'])) {
            $stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = :id");
            $stmt->execute(['id' => (int)$item['sales_user_id']]);
            $sales = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $stmt = $pdo->prepare("SELECT * FROM booking_guests WHERE booking_id = :id ORDER BY id ASC");
        $stmt->execute(['id' => (int)$id]);
        $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy các dịch vụ/chi phí của nhà cung cấp trong tour này (nếu có)
        $supplierExpenses = [];
        if (!empty($tour['supplier_id']) && !empty($item['tour_id'])) {
            try {
                $sql = "SELECT te.*, s.name AS supplier_name
                        FROM tour_expenses te
                        LEFT JOIN tour_suppliers s ON te.supplier_id = s.id
                        WHERE te.supplier_id = :sid AND te.tour_id = :tid
                        ORDER BY te.date_incurred DESC";
                $q = $pdo->prepare($sql);
                $q->execute([
                    'sid' => (int)$tour['supplier_id'],
                    'tid' => (int)$item['tour_id'],
                ]);
                $supplierExpenses = $q->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $supplierExpenses = [];
            }
        }
    }

    require __DIR__ . '/../../views/booking/detail_booking.php';
}

function booking_create() {
    $tourModel = new TourModel();
    $scheduleModel = new ScheduleModel();

    $tours = $tourModel->all();
    $schedules = $scheduleModel->all();

    // Map nhà cung cấp chính theo tour từ dữ liệu tourModel
    $suppliersByTour = [];
    foreach ($tours as $t) {
        $tid = (int)($t['id'] ?? 0);
        if ($tid <= 0) {
            continue;
        }
        $name = '';
        if (!empty($t['supplier_name'])) {
            $name = (string)$t['supplier_name'];
        } elseif (!empty($t['supplier_text'])) {
            $name = (string)$t['supplier_text'];
        }
        if ($name !== '') {
            $suppliersByTour[$tid] = $name;
        }
    }

    // Lấy danh sách khách hàng (users role = traveler)
    $pdo = DB::get();
    $stmt = $pdo->prepare("SELECT id, full_name, email FROM users WHERE role = 'traveler' ORDER BY full_name ASC");
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require __DIR__ . '/../../views/booking/create_booking.php';
}

function booking_update_status() {
    session_start();

    $id = $_POST['id'];
    $new_status = $_POST['status'];
    $note = $_POST['note'] ?? "";
    $user_id = $_SESSION['user_id'] ?? 1; // giả sử admin id = 1

    $booking = booking_find($id);
    $old_status = $booking['booking_status'];

    // Gọi đúng hàm PDO
    booking_update_status_db($id, $new_status, $old_status, $user_id);

    header("Location: " . BASE_URL . "?r=booking_detail&id=" . $id);
    exit;
}

function booking_store() {
    session_start();

    $data = [
        'tour_id' => $_POST['tour_id'] ?? null,
        'schedule_id' => $_POST['schedule_id'] ?? null,
        'customer_user_id' => $_POST['customer_user_id'] ?? null,
        'sales_user_id' => $_SESSION['user_id'] ?? 3,
        'total_guests' => (int)($_POST['total_guests'] ?? 0),
        'status' => 'pending',
    ];

    $guests = [
        'full_name' => $_POST['guest_full_name'] ?? [],
        'gender' => $_POST['guest_gender'] ?? [],
        'dob' => $_POST['guest_dob'] ?? [],
        'id_document_no' => $_POST['guest_id_document_no'] ?? [],
        'notes' => $_POST['guest_notes'] ?? [],
    ];

    $result = booking_create_with_guests($data, $guests);

    if ($result['success']) {
        header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $result['booking_id']);
        exit;
    }

    // Nếu lỗi, quay lại form với thông báo đơn giản
    $error = urlencode($result['message'] ?? 'Không thể tạo booking');
    header('Location: ' . BASE_URL . '?r=booking_create&error=' . $error);
    exit;
}
