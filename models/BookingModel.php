<?php
// D:\laragon\www\base\models\BookingModel.php

class BookingModel extends BaseModel {
    protected $table_name = "bookings";

    public function __construct() {
        parent::__construct();
    }

    public function all() {
        $sql = "SELECT b.*, t.title as tour_name, u.full_name as customer_name
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u ON b.customer_user_id = u.id
                ORDER BY b.date_booked DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $query = "SELECT b.*, t.title as tour_name, u.full_name as customer_name, s.name as supplier_name, s.phone as supplier_phone, s.email as supplier_email
                  FROM bookings b
                  LEFT JOIN tours t ON b.tour_id = t.id
                  LEFT JOIN users u ON b.customer_user_id = u.id
                  LEFT JOIN suppliers s ON t.supplier_id = s.id
                  WHERE b.id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table_name} (tour_id, schedule_id, customer_user_id, sales_user_id, total_guests, booking_status, total_price)
                VALUES (:tour_id, :schedule_id, :customer_user_id, :sales_user_id, :total_guests, :booking_status, :total_price)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $params = [
            ':tour_id' => $data['tour_id'] ?? null,
            ':schedule_id' => $data['schedule_id'] ?? null,
            ':customer_user_id' => $data['customer_user_id'] ?? null,
            ':sales_user_id' => $data['sales_user_id'] ?? null,
            ':total_guests' => $data['total_guests'] ?? 1,
            ':booking_status' => $data['booking_status'] ?? 'pending',
            ':total_price' => $data['total_price'] ?? 0
        ];
        
        if ($stmt->execute($params)) {
            return $this->pdo->lastInsertId();
        } else {
            return false;
        }
    }

    public function update($id, $data) {
        $query = "UPDATE bookings SET tour_id = ?, total_guests = ?, total_price = ?, 
                  booking_status = ?, special_requests = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $data['tour_id'] ?? null,
            $data['number_of_guests'] ?? ($data['total_guests'] ?? 1),
            $data['total_amount'] ?? ($data['total_price'] ?? 0),
            $data['status'] ?? ($data['booking_status'] ?? 'pending'),
            $data['special_requests'] ?? '',
            (int)$id
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM bookings WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([(int)$id]);
    }

    // Cập nhật trạng thái booking
    public function updateStatus($id, $status) {
        $id = (int)$id;
        try {
            $stmt0 = $this->pdo->prepare("SELECT booking_status FROM bookings WHERE id = ?");
            $stmt0->execute([$id]);
            $row = $stmt0->fetch(PDO::FETCH_ASSOC);
            $old = $row['booking_status'] ?? null;

            $stmt = $this->pdo->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
            $ok = $stmt->execute([$status, $id]);

            if ($ok && $old !== null && $old !== $status) {
                try {
                    $changedBy = $_SESSION['user_id'] ?? null;
                    $stmt2 = $this->pdo->prepare("INSERT INTO booking_status_logs (booking_id, old_status, new_status, changed_by_user_id, changed_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt2->execute([$id, $old, $status, $changedBy]);
                } catch (Exception $e) {
                    // ignore if log table missing
                }
            }
            return $ok;
        } catch (Exception $e) {
            return false;
        }
    }

    // Tổng số chỗ đã được đặt theo schedule
    public function getOccupiedSeatsBySchedule($schedule_id) {
        $sql = "SELECT COALESCE(SUM(total_guests), 0) AS occupied
                FROM bookings
                WHERE schedule_id = ? AND booking_status != 'canceled'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([(int)$schedule_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['occupied'] ?? 0);
    }

    // Lấy booking theo user
    public function getByUser($user_id) {
        $query = "SELECT b.*, t.title as tour_name 
                  FROM bookings b
                  LEFT JOIN tours t ON b.tour_id = t.id
                  WHERE b.customer_user_id = ?
                  ORDER BY b.date_booked DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách khách của booking
    public function getBookingGuests($booking_id) {
        $query = "SELECT * FROM booking_guests WHERE booking_id = ? ORDER BY id ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy lịch sử thay đổi trạng thái
    public function getStatusHistory($booking_id) {
        $booking_id = (int)$booking_id;
        try {
            $query = "SELECT l.*, u.full_name AS changed_by_name
                      FROM booking_status_logs l
                      LEFT JOIN users u ON l.changed_by_user_id = u.id
                      WHERE l.booking_id = ?
                      ORDER BY l.id DESC
                      LIMIT 20";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$booking_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            try {
                $query2 = "SELECT * FROM booking_status_logs WHERE booking_id = ? ORDER BY id DESC LIMIT 20";
                $stmt2 = $this->pdo->prepare($query2);
                $stmt2->execute([$booking_id]);
                return $stmt2->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e2) {
                return [];
            }
        }
    }

    // Lấy thông tin khách hàng đầy đủ
    public function getCustomerInfo($user_id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([(int)$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy lịch sử thanh toán
    public function getPaymentHistory($booking_id) {
        try {
            $query = "SELECT p.*, u.full_name as confirmed_by, p.payment_method as method
                      FROM payments p
                      LEFT JOIN users u ON p.created_by = u.id
                      WHERE p.booking_id = ? 
                      ORDER BY p.id DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([(int)$booking_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Log the error for debugging if needed
            // error_log($e->getMessage());
            return [];
        }
    }

    // Tính tổng đã thanh toán
    public function getTotalPaid($booking_id) {
        try {
            $query = "SELECT COALESCE(SUM(amount), 0) as total_paid FROM payments WHERE booking_id = ? AND status = 'completed'";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([(int)$booking_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_paid'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    // Lấy danh sách nhà cung cấp cùng dịch vụ cho tour
    public function getTourSuppliers($tour_id) {
        $tour_id = (int)$tour_id;
        try {
            $query = "SELECT DISTINCT s.id, s.name, s.type, s.phone, s.email, s.contact_person,
                             te.expense_type as service_type, te.description as service_description
                      FROM tour_expenses te
                      JOIN suppliers s ON te.supplier_id = s.id
                      WHERE te.tour_id = ?
                      ORDER BY s.id, te.expense_type";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$tour_id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) return $rows;
        } catch (Exception $e) {}

        try {
            $query2 = "SELECT DISTINCT s.id, s.name, s.type, s.phone, s.email, s.contact_person,
                               ts.service_type as service_type, ts.description as service_description
                        FROM tour_suppliers ts
                        JOIN suppliers s ON ts.supplier_id = s.id
                        WHERE ts.tour_id = ?
                        ORDER BY s.id, ts.service_type";
            $stmt2 = $this->pdo->prepare($query2);
            $stmt2->execute([$tour_id]);
            $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows2)) return $rows2;
        } catch (Exception $e2) {}

        return [];
    }

    // Rooms & assignments
    public function getAvailableRooms() {
        try {
            $sql = "SELECT r.id, r.room_number, r.status, h.name AS hotel_name, rt.name AS room_type
                    FROM rooms r
                    LEFT JOIN hotels h ON r.hotel_id = h.id
                    LEFT JOIN room_types rt ON r.room_type_id = rt.id
                    WHERE r.status = 'available'
                    ORDER BY h.name, r.room_number";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function isRoomAvailable($room_id, $start_date, $end_date) {
        if (empty($start_date) || empty($end_date)) return true; // cannot validate without dates
        try {
            $sql = "SELECT COUNT(*) FROM room_assignments ra
                    WHERE ra.room_id = ?
                      AND ra.status IN ('reserved','checked_in')
                      AND ra.check_in_date <= ?
                      AND ra.check_out_date >= ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([(int)$room_id, $end_date, $start_date]);
            $conflicts = (int)$stmt->fetchColumn();
            return $conflicts === 0;
        } catch (Exception $e) { return true; }
    }

    public function getRoomAssignmentsByBooking($booking_id) {
        try {
            $sql = "SELECT ra.id, ra.room_id, ra.guest_id, ra.check_in_date, ra.check_out_date, ra.status,
                           r.room_number, h.name AS hotel_name, rt.name AS room_type,
                           bg.full_name AS guest_name
                    FROM room_assignments ra
                    LEFT JOIN rooms r ON ra.room_id = r.id
                    LEFT JOIN hotels h ON r.hotel_id = h.id
                    LEFT JOIN room_types rt ON r.room_type_id = rt.id
                    LEFT JOIN booking_guests bg ON ra.guest_id = bg.id
                    WHERE ra.booking_id = ?
                    ORDER BY ra.id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([(int)$booking_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }
}
?>
