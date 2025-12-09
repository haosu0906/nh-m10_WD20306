<?php

require_once __DIR__ . '/../models/BaseModel.php';

class GuideRatingController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new BaseModel())->getConnection();
    }

    // Dashboard admin - Tổng hợp đánh giá
    public function index()
    {
        $guideId = (int)($_GET['guide_id'] ?? 0);
        $status = $_GET['status'] ?? '';
        $raterType = $_GET['rater_type'] ?? '';

        $sql = "SELECT gr.*, u.full_name as guide_name, u.email as guide_email,
                       c.full_name as customer_name, b.id as booking_id,
                       t.title as tour_title,
                       rater.full_name as rater_name
                FROM guide_ratings gr
                LEFT JOIN users u ON gr.guide_user_id = u.id
                LEFT JOIN bookings b ON gr.booking_id = b.id
                LEFT JOIN users c ON b.customer_user_id = c.id
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users rater ON gr.created_by = rater.id
                WHERE 1=1";

        $params = [];
        if ($guideId > 0) {
            $sql .= " AND gr.guide_user_id = :gid";
            $params[':gid'] = $guideId;
        }
        if (!empty($status)) {
            $sql .= " AND gr.status = :status";
            $params[':status'] = $status;
        }
        if (!empty($raterType)) {
            $sql .= " AND gr.rater_type = :rater_type";
            $params[':rater_type'] = $raterType;
        }

        $sql .= " ORDER BY gr.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $ratings = $stmt->fetchAll();

        // Thống kê tổng quan
        $stats = $this->getRatingStats();

        // Lấy danh sách guides cho bộ lọc
        $stmtG = $this->pdo->query("SELECT u.id, u.full_name FROM users u 
                                      LEFT JOIN guides_info gi ON u.id = gi.user_id 
                                      WHERE gi.user_id IS NOT NULL 
                                      ORDER BY u.full_name ASC");
        $guides = $stmtG->fetchAll();

        require __DIR__ . '/../views/guide_ratings/admin_dashboard.php';
    }

    // Form đánh giá cho khách/staff
    public function create()
    {
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        $guideId = (int)($_GET['guide_id'] ?? 0);

        // Lấy thông tin booking nếu có
        $booking = null;
        if ($bookingId > 0) {
            $stmt = $this->pdo->prepare("SELECT b.*, t.title as tour_title
                                         FROM bookings b
                                         LEFT JOIN tours t ON b.tour_id = t.id
                                         WHERE b.id = :id");
            $stmt->execute([':id' => $bookingId]);
            $booking = $stmt->fetch();
            
            if ($booking && $guideId === 0) {
                // Lấy guide từ guide_assignments
                $stmt = $this->pdo->prepare("SELECT guide_user_id FROM guide_assignments 
                                             WHERE tour_id = :tid LIMIT 1");
                $stmt->execute([':tid' => $booking['tour_id']]);
                $assignment = $stmt->fetch();
                $guideId = $assignment ? (int)$assignment['guide_user_id'] : 0;
            }
        }

        // Lấy thông tin guide
        $guide = null;
        if ($guideId > 0) {
            $stmt = $this->pdo->prepare("SELECT u.*, gi.* FROM users u 
                                        LEFT JOIN guides_info gi ON u.id = gi.user_id 
                                        WHERE u.id = :id");
            $stmt->execute([':id' => $guideId]);
            $guide = $stmt->fetch();
        }

        // Danh sách guides để chọn nếu chưa có guide cụ thể
        try {
            $stmtG = $this->pdo->query("SELECT u.id, u.full_name 
                                        FROM users u 
                                        LEFT JOIN guides_info gi ON u.id = gi.user_id 
                                        WHERE gi.user_id IS NOT NULL 
                                        ORDER BY u.full_name ASC");
            $guides = $stmtG->fetchAll();
        } catch (PDOException $e) { $guides = []; }

        require __DIR__ . '/../views/guide_ratings/create_rating.php';
    }

    // Lưu đánh giá
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?r=guide_ratings');
            exit;
        }

        $guideId = (int)($_POST['guide_id'] ?? 0);
        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $raterId = 1; // Default admin user
        
        // Database hiện tại chỉ có 1 trường rating, không có rating chi tiết
        $rating = (int)($_POST['rating'] ?? 5);
        $comment = $_POST['comment'] ?? '';

        // Kiểm tra đã đánh giá chưa
        $stmt = $this->pdo->prepare("SELECT id FROM guide_ratings 
                                     WHERE guide_user_id = :gid AND booking_id = :bid AND created_by = :rid");
        $stmt->execute([
            ':gid' => $guideId, 
            ':bid' => $bookingId, 
            ':rid' => $raterId
        ]);
        if ($stmt->fetch()) {
            require_once __DIR__ . '/../assets/helpers/flash.php';
            flash_set('error', 'Bạn đã đánh giá HDV này cho booking này!');
            header('Location: ' . BASE_URL . '?r=guide_ratings_create&booking_id=' . $bookingId);
            exit;
        }

        // Thêm đánh giá - chỉ có các trường trong database hiện tại
        $stmt = $this->pdo->prepare("INSERT INTO guide_ratings 
                                     (guide_user_id, booking_id, rating, comment, created_by) 
                                     VALUES (:gid, :bid, :rating, :comment, :created_by)");
        $result = $stmt->execute([
            ':gid' => $guideId,
            ':bid' => $bookingId,
            ':rating' => $rating,
            ':comment' => $comment,
            ':created_by' => $raterId
        ]);

        require_once __DIR__ . '/../assets/helpers/flash.php';
        if ($result) {
            flash_set('success', 'Gửi đánh giá thành công! Cảm ơn bạn đã đóng góp ý kiến.');
        } else {
            flash_set('error', 'Có lỗi xảy ra!');
        }

        // Redirect tùy theo người đánh giá
        if ($raterType === 'customer') {
            header('Location: ' . BASE_URL . '?r=booking_detail&id=' . $bookingId);
        } else {
            header('Location: ' . BASE_URL . '?r=guide_ratings');
        }
        exit;
    }

    // Duyệt đánh giá (admin) - Database hiện tại không có status, approve fields
    public function approve($id)
    {
        $id = (int)$id;
        $adminId = 1; // Hardcoded, nên lấy từ session

        $stmt = $this->pdo->prepare("UPDATE guide_ratings 
                                     SET status = 'approved', approved_at = CURRENT_TIMESTAMP, approved_by = :admin_id
                                     WHERE id = :id");
        $result = $stmt->execute([':admin_id' => $adminId, ':id' => $id]);

        require_once __DIR__ . '/../assets/helpers/flash.php';
        if ($result) {
            flash_set('success', 'Duyệt đánh giá thành công!');
        } else {
            flash_set('error', 'Có lỗi xảy ra!');
        }

        header('Location: ' . BASE_URL . '?r=guide_ratings');
        exit;
    }

    // Từ chối đánh giá
    public function reject($id)
    {
        $id = (int)$id;
        $adminId = 1; // Hardcoded, nên lấy từ session

        $stmt = $this->pdo->prepare("UPDATE guide_ratings 
                                     SET status = 'rejected', approved_at = CURRENT_TIMESTAMP, approved_by = :admin_id
                                     WHERE id = :id");
        $result = $stmt->execute([':admin_id' => $adminId, ':id' => $id]);

        require_once __DIR__ . '/../assets/helpers/flash.php';
        if ($result) {
            flash_set('success', 'Từ chối đánh giá thành công!');
        } else {
            flash_set('error', 'Có lỗi xảy ra!');
        }

        header('Location: ' . BASE_URL . '?r=guide_ratings');
        exit;
    }

    // Ẩn đánh giá
    public function hide($id)
    {
        $id = (int)$id;

        $stmt = $this->pdo->prepare("UPDATE guide_ratings SET status = 'hidden' WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);

        require_once __DIR__ . '/../assets/helpers/flash.php';
        if ($result) {
            flash_set('success', 'Ẩn đánh giá thành công!');
        } else {
            flash_set('error', 'Có lỗi xảy ra!');
        }

        header('Location: ' . BASE_URL . '?r=guide_ratings');
        exit;
    }

    // Chi tiết đánh giá
    public function show($id)
    {
        $id = (int)$id;

        $stmt = $this->pdo->prepare("SELECT gr.*, u.full_name as guide_name, u.email as guide_email,
                                           c.full_name as customer_name, b.id as booking_id,
                                           t.title as tour_title,
                                           rater.full_name as rater_name
                                    FROM guide_ratings gr
                                    LEFT JOIN users u ON gr.guide_user_id = u.id
                                    LEFT JOIN bookings b ON gr.booking_id = b.id
                                    LEFT JOIN users c ON b.customer_user_id = c.id
                                    LEFT JOIN tours t ON b.tour_id = t.id
                                    LEFT JOIN users rater ON gr.created_by = rater.id
                                    WHERE gr.id = :id");
        $stmt->execute([':id' => $id]);
        $rating = $stmt->fetch();

        if (!$rating) {
            require_once __DIR__ . '/../assets/helpers/flash.php';
            flash_set('error', 'Không tìm thấy đánh giá!');
            header('Location: ' . BASE_URL . '?r=guide_ratings');
            exit;
        }

        require __DIR__ . '/../views/guide_ratings/show_rating.php';
    }

    // Thống kê đánh giá
    private function getRatingStats()
    {
        $stats = [];

        // Tổng số đánh giá
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM guide_ratings");
        $stats['total_ratings'] = $stmt->fetch()['total'];

        // Đánh giá theo trạng thái - database hiện tại không có status
        $stats['by_status'] = [];

        // Đánh giá theo loại người đánh giá - database hiện tại không có rater_type
        $stats['by_rater_type'] = [];

        // Điểm trung bình tổng thể
        $stmt = $this->pdo->query("SELECT AVG(rating) as avg_rating FROM guide_ratings");
        $stats['average_rating'] = number_format($stmt->fetch()['avg_rating'], 1);

        // Top HDV được đánh giá cao nhất
        $stmt = $this->pdo->query("SELECT u.full_name, COUNT(*) as rating_count, AVG(gr.rating) as avg_rating
                                    FROM guide_ratings gr
                                    LEFT JOIN users u ON gr.guide_user_id = u.id
                                    GROUP BY gr.guide_user_id, u.full_name
                                    HAVING rating_count >= 1
                                    ORDER BY avg_rating DESC, rating_count DESC
                                    LIMIT 10");
        $stats['top_guides'] = $stmt->fetchAll();

        return $stats;
    }

    // API endpoint cho khách hàng đánh giá
    public function customerRatingForm()
    {
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        
        if ($bookingId === 0) {
            echo 'Invalid booking';
            exit;
        }

        // Kiểm tra booking có tồn tại và đã hoàn thành không
        $stmt = $this->pdo->prepare("SELECT b.*, u.full_name as guide_name, t.title as tour_title
                                     FROM bookings b
                                     LEFT JOIN tours t ON b.tour_id = t.id
                                     LEFT JOIN guide_assignments ga ON t.id = ga.tour_id
                                     LEFT JOIN users u ON ga.guide_user_id = u.id
                                     WHERE b.id = :id AND b.booking_status = 'completed'");
        $stmt->execute([':id' => $bookingId]);
        $booking = $stmt->fetch();

        if (!$booking) {
            echo 'Booking not found or not eligible for rating';
            exit;
        }

        // Kiểm tra đã đánh giá chưa
        $stmt = $this->pdo->prepare("SELECT id FROM guide_ratings 
                                     WHERE booking_id = :bid");
        $stmt->execute([':bid' => $bookingId]);
        if ($stmt->fetch()) {
            echo 'You have already rated this guide';
            exit;
        }

        require __DIR__ . '/../views/guide_ratings/customer_form.php';
    }
}
