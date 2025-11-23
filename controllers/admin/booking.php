<?php
require_once __DIR__ . '/../../assets/configs/db.php';
require_once __DIR__ . '/../../models/admin/booking.php';
require_once __DIR__ . '/../../models/TourModel.php';
require_once __DIR__ . '/../../models/ScheduleModel.php';

function booking_index() {
    $status = $_GET['status'] ?? "";
    $items = booking_all($status);
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
            $stmt = $pdo->prepare("SELECT title FROM tours WHERE id = :id");
            $stmt->execute(['id' => (int)$item['tour_id']]);
            $tour = $stmt->fetch(PDO::FETCH_ASSOC);
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
    }

    require __DIR__ . '/../../views/booking/detail_booking.php';
}

function booking_create() {
    $tourModel = new TourModel();
    $scheduleModel = new ScheduleModel();

    $tours = $tourModel->all();
    $schedules = $scheduleModel->all();

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
