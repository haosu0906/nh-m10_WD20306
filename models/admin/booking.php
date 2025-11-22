<?php
require_once __DIR__ . '/../../assets/configs/db.php';

/**
 * Lấy tất cả booking, có thể filter theo status
 */
function booking_all($status = "") {
    $pdo = DB::get();

    if ($status != "") {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE status = :status");
        $stmt->execute(['status' => $status]);
    } else {
        $stmt = $pdo->query("SELECT * FROM bookings");
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy thông tin 1 booking theo id
 */
function booking_find($id) {
    $pdo = DB::get();
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Cập nhật trạng thái booking và lưu log
 */
function booking_update_status_db($id, $new_status, $old_status, $changed_by, $note = "") {
    $pdo = DB::get();

    // Cập nhật trạng thái
    $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $new_status, 'id' => $id]);

    // Lưu log
    $stmt = $pdo->prepare("
        INSERT INTO booking_status_logs(booking_id, old_status, new_status, changed_by, changed_at, note)
        VALUES (:booking_id, :old_status, :new_status, :changed_by, NOW(), :note)
    ");
    $stmt->execute([
        'booking_id' => $id,
        'old_status' => $old_status,
        'new_status' => $new_status,
        'changed_by' => $changed_by,
        'note' => $note
    ]);
}

/**
 * Lấy lịch sử thay đổi trạng thái của booking
 */
function booking_logs($booking_id) {
    $pdo = DB::get();
    $stmt = $pdo->prepare("
        SELECT l.*, u.full_name AS changed_name
        FROM booking_status_logs l
        LEFT JOIN users u ON u.id = l.changed_by
        WHERE booking_id = :booking_id
        ORDER BY l.changed_at DESC
    ");
    $stmt->execute(['booking_id' => $booking_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}