<?php
require_once __DIR__ . '/../../models/admin/booking.php';

function booking_index() {
    $status = $_GET['status'] ?? "";
    $items = booking_all($status);
    require __DIR__ . '/../../views/booking/list_booking.php';
}

function booking_detail() {
    $id = $_GET['id'] ?? 0;
    $item = booking_find($id);
    $logs = booking_logs($id);
    require __DIR__ . '/../../views/booking/detail_booking.php';
}

function booking_update_status() {
    session_start();

    $id = $_POST['id'];
    $new_status = $_POST['status'];
    $note = $_POST['note'] ?? "";
    $user_id = $_SESSION['user_id'] ?? 1; // giả sử admin id = 1

    $booking = booking_find($id);
    $old_status = $booking['status'];

    // Gọi đúng hàm PDO
    booking_update_status_db($id, $new_status, $old_status, $user_id, $note);

    header("Location: " . BASE_URL . "?r=booking_detail&id=" . $id);
    exit;
}
