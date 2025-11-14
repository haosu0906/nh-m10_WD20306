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
        $schedules = $this->scheduleModel->all();
        require __DIR__ . '/../views/schedules/index.php';
    }

    public function create(){
        $tours = $this->tourModel->all();
        $guides = $this->userModel->getGuides();
        require __DIR__ . '/../views/schedules/form.php';
    }

    public function store(){
        // Xử lý thêm lịch
        header('Location: /base/?r=schedules');
        exit;
    }
}
?>