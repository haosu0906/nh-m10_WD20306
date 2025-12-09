<?php
// Helper functions (flash helper tự động gọi session_start() khi cần)
require_once __DIR__ . '/../helpers/flash.php';

define('BASE_URL',          'http://localhost/duan1/nh-m10_WD20306/');

// Thư mục gốc của project: nh-m10_WD20306/
define('PATH_ROOT',         __DIR__ . '/../..' . '/');

define('PATH_VIEW',         PATH_ROOT . 'views/');

define('PATH_VIEW_MAIN',    PATH_ROOT . 'views/main.php');

define('BASE_ASSETS_UPLOADS',   BASE_URL . 'assets/uploads/');

define('PATH_ASSETS_UPLOADS',   PATH_ROOT . 'assets/uploads/');

define('PATH_CONTROLLER',       PATH_ROOT . 'controllers/');

define('PATH_MODEL',            PATH_ROOT . 'models/');

define('DB_HOST',     'localhost');
define('DB_PORT',     '3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME',     'tripmate_db');
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
define('MAIL_FROM_EMAIL', 'no-reply@tripmate.local');
define('MAIL_FROM_NAME', 'TripMate');
