<?php

require_once __DIR__ . '/../models/TourLogModel.php';
require_once __DIR__ . '/../models/TourModel.php';

class TourLogController
{
    protected $logModel;

    public function __construct()
    {
        $this->logModel = new TourLogModel();
    }

    public function index()
    {
        $guideId = $_GET['guide_id'] ?? null;
        $tourId = isset($_GET['tour_id']) ? (int)$_GET['tour_id'] : null;
        if (!$guideId && !empty($_SESSION['guide_user_id'])) {
            $guideId = (int)$_SESSION['guide_user_id'];
        }

        $logs = [];
        if ($guideId) {
            if ($tourId) {
                $logs = $this->logModel->getByGuideAndTour((int)$guideId, $tourId);
            } else {
                $logs = $this->logModel->getByGuide($guideId);
            }
        } elseif ($tourId) {
            $logs = $this->logModel->getByTour($tourId);
        }

        require __DIR__ . '/../views/tour_logs/index.php';
    }

    public function create()
    {
        $tourId = (int)($_GET['tour_id'] ?? 0);
        // Tạm thời: nếu chưa có phiên đăng nhập HDV thì dùng 1 ID mặc định (ví dụ 1)
        $guideId = 0;
        if (!empty($_SESSION['guide_user_id'])) {
            $guideId = (int)$_SESSION['guide_user_id'];
        } elseif (!empty($_SESSION['user']['id'])) {
            $guideId = (int)$_SESSION['user']['id'];
        } else {
            $guideId = 1; // ID HDV mặc định để demo, bạn có thể sửa lại cho đúng user thật
        }
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'tour_id' => (int)($_POST['tour_id'] ?? 0),
                'guide_user_id' => $guideId,
                'log_date' => $_POST['log_date'] ?? date('Y-m-d H:i:s'),
                'incident_details' => trim($_POST['incident_details'] ?? ''),
                'customer_feedback' => trim($_POST['customer_feedback'] ?? ''),
                'weather' => trim($_POST['weather'] ?? '')
            ];

            // Chỉ bắt buộc: chọn tour và nhập nội dung sự kiện
            if ($data['tour_id'] && $data['incident_details'] !== '') {
                $logId = $this->logModel->create($data);

                if ($logId) {
                    // Upload nhiều ảnh nếu có
                    if (!empty($_FILES['images'])) {
                        $paths = upload_multiple_files('tour_logs', $_FILES['images']);
                        if (!empty($paths)) {
                            $this->logModel->addImages($logId, $paths);
                        }
                    }

                    header('Location: ' . BASE_URL . '?r=tour_logs&guide_id=' . (int)$data['guide_user_id']);
                    exit;
                }
                $error = 'Không lưu được nhật ký, vui lòng thử lại.';
            } else {
                $error = 'Vui lòng chọn tour và nhập nội dung sự kiện.';
            }
        }

        $tourModel = new TourModel();
        $tours = $tourModel->all();

        require __DIR__ . '/../views/tour_logs/create.php';
    }

    public function show($id)
    {
        $log = $this->logModel->find((int)$id);
        require __DIR__ . '/../views/tour_logs/show.php';
    }

    public function edit($id)
    {
        $log = $this->logModel->find((int)$id);
        if (!$log) {
            header('Location: ' . BASE_URL . '?r=tour_logs');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'log_date' => $_POST['log_date'] ?? $log['log_date'],
                'weather' => trim($_POST['weather'] ?? ''),
                'incident_details' => trim($_POST['incident_details'] ?? ''),
                'customer_feedback' => trim($_POST['customer_feedback'] ?? ''),
            ];

            if ($data['incident_details'] !== '') {
                $ok = $this->logModel->update((int)$id, $data);

                if ($ok) {
                    // Thêm ảnh mới nếu có
                    if (!empty($_FILES['images'])) {
                        $paths = upload_multiple_files('tour_logs', $_FILES['images']);
                        if (!empty($paths)) {
                            $this->logModel->addImages((int)$id, $paths);
                        }
                    }

                    header('Location: ' . BASE_URL . '?r=tour_logs_show&id=' . (int)$id);
                    exit;
                }
                $error = 'Không cập nhật được nhật ký, vui lòng thử lại.';
            } else {
                $error = 'Vui lòng nhập nội dung sự kiện.';
            }
        }

        require __DIR__ . '/../views/tour_logs/edit.php';
    }

    public function delete($id)
    {
        $log = $this->logModel->find((int)$id);
        if ($log) {
            $this->logModel->delete((int)$id);
        }

        header('Location: ' . BASE_URL . '?r=tour_logs');
        exit;
    }
}
