<?php

class HomeController {
    public function index() {
        // Không cần require view ở đây, chỉ cần include trực tiếp
        include __DIR__ . '/../views/main.php';
    }
}