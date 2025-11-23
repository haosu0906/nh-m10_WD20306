<?php
require_once __DIR__ . '/../../assets/configs/db.php';

/**
 * Lấy tất cả booking, có thể filter theo status
 */
function booking_all($status = "") {
    $pdo = DB::get();

    if ($status != "") {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_status = :status");
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
 * Sinh mã booking dạng BK000001 từ ID
 */
function booking_code_from_id($id) {
    return 'BK' . str_pad((string)$id, 6, '0', STR_PAD_LEFT);
}

/**
 * Cập nhật trạng thái booking và lưu log
 */
function booking_update_status_db($id, $new_status, $old_status, $changed_by_user_id) {
    $pdo = DB::get();

    // Cập nhật trạng thái
    $stmt = $pdo->prepare("UPDATE bookings SET booking_status = :status WHERE id = :id");
    $stmt->execute(['status' => $new_status, 'id' => $id]);

    // Lưu log
    $stmt = $pdo->prepare("
        INSERT INTO booking_status_logs(booking_id, old_status, new_status, changed_by_user_id, changed_at)
        VALUES (:booking_id, :old_status, :new_status, :changed_by_user_id, NOW())
    ");
    $stmt->execute([
        'booking_id' => $id,
        'old_status' => $old_status,
        'new_status' => $new_status,
        'changed_by_user_id' => $changed_by_user_id
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
        LEFT JOIN users u ON u.id = l.changed_by_user_id
        WHERE booking_id = :booking_id
        ORDER BY l.changed_at DESC
    ");
    $stmt->execute(['booking_id' => $booking_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Tạo booking mới kèm danh sách khách
 * $data: tour_id, schedule_id, customer_user_id, sales_user_id, total_guests, status
 * $guests: mảng các field guest_* từ form
 */
function booking_create_with_guests($data, $guests) {
    $pdo = DB::get();

    try {
        // Kiểm tra dữ liệu cơ bản
        if (empty($data['tour_id'])) {
            return ['success' => false, 'message' => 'Vui lòng chọn tour'];
        }
        if ($data['total_guests'] <= 0) {
            return ['success' => false, 'message' => 'Số lượng khách phải lớn hơn 0'];
        }

        // Nếu có schedule_id thì kiểm tra chỗ trống theo max_capacity
        if (!empty($data['schedule_id'])) {
            $stmt = $pdo->prepare("SELECT max_capacity FROM tour_schedules WHERE id = :id");
            $stmt->execute(['id' => (int)$data['schedule_id']]);
            $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($schedule) {
                $max_capacity = (int)$schedule['max_capacity'];
                // Tổng khách đã đặt của tour này (simple)
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_guests),0) AS total FROM bookings WHERE tour_id = :tour_id");
                $stmt->execute(['tour_id' => (int)$data['tour_id']]);
                $current = (int)$stmt->fetchColumn();

                if ($current + $data['total_guests'] > $max_capacity) {
                    return ['success' => false, 'message' => 'Số lượng khách vượt quá số chỗ trống của tour'];
                }
            }
        }

        $pdo->beginTransaction();

        // Tính tổng tiền đơn giản theo giá người lớn nếu có
        $stmt = $pdo->prepare("SELECT adult_price FROM tour_prices WHERE tour_id = :tour_id LIMIT 1");
        $stmt->execute(['tour_id' => (int)$data['tour_id']]);
        $priceRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $adult_price = $priceRow ? (float)$priceRow['adult_price'] : 0;
        $total_price = $adult_price * $data['total_guests'];

        // Tạo booking
        $stmt = $pdo->prepare("INSERT INTO bookings(tour_id, customer_user_id, sales_user_id, total_guests, booking_status, total_price, date_booked)
                               VALUES (:tour_id, :customer_user_id, :sales_user_id, :total_guests, :status, :total_price, NOW())");
        $stmt->execute([
            'tour_id' => (int)$data['tour_id'],
            'customer_user_id' => !empty($data['customer_user_id']) ? (int)$data['customer_user_id'] : null,
            'sales_user_id' => !empty($data['sales_user_id']) ? (int)$data['sales_user_id'] : null,
            'total_guests' => $data['total_guests'],
            'status' => $data['status'] ?? 'pending',
            'total_price' => $total_price,
        ]);

        $bookingId = (int)$pdo->lastInsertId();

        // Lưu danh sách khách
        $names = $guests['full_name'] ?? [];
        $genders = $guests['gender'] ?? [];
        $dobs = $guests['dob'] ?? [];
        $ids = $guests['id_document_no'] ?? [];
        $notes = $guests['notes'] ?? [];

        $countGuests = count($names);
        for ($i = 0; $i < $countGuests; $i++) {
            if (trim($names[$i]) === '') continue;
            $stmt = $pdo->prepare("INSERT INTO booking_guests(booking_id, full_name, gender, dob, id_document_no, is_checked_in, notes)
                                   VALUES (:booking_id, :full_name, :gender, :dob, :id_document_no, 0, :notes)");
            $stmt->execute([
                'booking_id' => $bookingId,
                'full_name' => $names[$i],
                'gender' => $genders[$i] ?? 'unknown',
                'dob' => $dobs[$i] ?? null,
                'id_document_no' => $ids[$i] ?? '',
                'notes' => $notes[$i] ?? '',
            ]);
        }

        // Ghi log trạng thái ban đầu
        $stmt = $pdo->prepare("INSERT INTO booking_status_logs(booking_id, old_status, new_status, changed_by_user_id, changed_at)
                               VALUES (:booking_id, :old_status, :new_status, :changed_by_user_id, NOW())");
        $stmt->execute([
            'booking_id' => $bookingId,
            'old_status' => 'pending',
            'new_status' => 'pending',
            'changed_by_user_id' => !empty($data['sales_user_id']) ? (int)$data['sales_user_id'] : null,
        ]);

        $pdo->commit();

        return [
            'success' => true,
            'booking_id' => $bookingId,
            'booking_code' => booking_code_from_id($bookingId),
        ];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }
}