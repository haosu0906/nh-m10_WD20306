<?php
require_once __DIR__ . '/../models/ScheduleModel.php';
require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class ScheduleController {
    protected $scheduleModel;
    protected $tourModel;
    protected $userModel;
    
    public function __construct() { 
        $this->scheduleModel = new ScheduleModel();
        $this->tourModel = new TourModel();
        $this->userModel = new UserModel();
    }

    public function index(){
        $tourId = isset($_GET['tour_id']) ? (int)$_GET['tour_id'] : 0;
        if ($tourId > 0) {
            $schedules = $this->scheduleModel->getByTour($tourId);
        } else {
            $schedules = $this->scheduleModel->all();
        }
        require __DIR__ . '/../views/schedules/index.php';
    }

    // Lịch khởi hành dạng calendar
    public function calendar(){
        $schedules = $this->scheduleModel->all();
        require __DIR__ . '/../views/schedules/calendar.php';
    }

    public function create(){
        $tours = $this->tourModel->all();
        $guides = $this->userModel->getGuides();
        require __DIR__ . '/../views/schedules/form.php';
    }

    public function store(){
        $data = [
            'tour_id' => (int)($_POST['tour_id'] ?? 0),
            'start_date' => trim($_POST['start_date'] ?? ''),
            'end_date' => trim($_POST['end_date'] ?? ''),
            'guide_user_id' => !empty($_POST['guide_user_id']) ? (int)$_POST['guide_user_id'] : null,
            'driver_user_id' => !empty($_POST['driver_user_id']) ? (int)$_POST['driver_user_id'] : null,
            'max_capacity' => (int)($_POST['max_capacity'] ?? 20),
        ];

        $errors = [];
        if (empty($data['tour_id'])) { $errors['tour_id'] = 'Vui lòng chọn tour'; }
        if ($data['start_date'] === '' || $data['end_date'] === '') { $errors['date'] = 'Vui lòng nhập ngày bắt đầu và kết thúc'; }

        if (!empty($errors)) {
            redirect_with_flash(BASE_URL . '?r=schedules_create', $errors, $_POST);
        }

        $this->scheduleModel->create($data);
        header('Location: ' . BASE_URL . '?r=schedules');
        exit;
    }

    public function edit($id){
        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            header('Location: ' . BASE_URL . '?r=schedules');
            exit;
        }
        $tours = $this->tourModel->all();
        $guides = $this->userModel->getGuides();
        require __DIR__ . '/../views/schedules/form.php';
    }

    public function update($id){
        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            header('Location: ' . BASE_URL . '?r=schedules');
            exit;
        }

        $data = [
            'tour_id' => (int)($_POST['tour_id'] ?? 0),
            'start_date' => trim($_POST['start_date'] ?? ''),
            'end_date' => trim($_POST['end_date'] ?? ''),
            'guide_user_id' => !empty($_POST['guide_user_id']) ? (int)$_POST['guide_user_id'] : null,
            'driver_user_id' => !empty($_POST['driver_user_id']) ? (int)$_POST['driver_user_id'] : null,
            'max_capacity' => (int)($_POST['max_capacity'] ?? 20),
        ];

        $errors = [];
        if (empty($data['tour_id'])) { $errors['tour_id'] = 'Vui lòng chọn tour'; }
        if ($data['start_date'] === '' || $data['end_date'] === '') { $errors['date'] = 'Vui lòng nhập ngày bắt đầu và kết thúc'; }

        if (!empty($errors)) {
            redirect_with_flash(BASE_URL . '?r=schedules_edit&id=' . $id, $errors, $_POST);
        }

        $this->scheduleModel->update($id, $data);
        header('Location: ' . BASE_URL . '?r=schedules');
        exit;
    }

    public function delete($id){
        $this->scheduleModel->delete($id);
        header('Location: ' . BASE_URL . '?r=schedules');
        exit;
    }
}
?>