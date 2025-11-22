<?php
require_once __DIR__ . '/../models/BookingModel.php';
require_once __DIR__ . '/../models/TourModel.php';

class BookingController {
    protected $bookingModel;
    protected $tourModel;
    
    public function __construct() { 
        $this->bookingModel = new BookingModel();
        $this->tourModel = new TourModel();
    }

    public function index(){
        $bookings = $this->bookingModel->all();
        require __DIR__ . '/../views/bookings/index.php';
    }

    public function create(){
        $tours = $this->tourModel->all();
        require __DIR__ . '/../views/bookings/form.php';
    }

    public function store(){
        // Xử lý đặt tour
        header('Location: ' . BASE_URL . '?r=bookings');
        exit;
    }
}
?>