<?php

$route = $_GET['r'] ?? 'home';

require_once __DIR__ . '/../controllers/TourCategoryController.php';
require_once __DIR__ . '/../controllers/StaffController.php';

$catController = new TourCategoryController();
$staffController = new StaffController();

switch ($route) {
    case 'tour_categories':
        $catController->index(); break;
    case 'tour_categories_create':
        $catController->create(); break;
    case 'tour_categories_store':
        $catController->store(); break;
    case 'tour_categories_edit':
        $catController->edit($_GET['id'] ?? 0); break;
    case 'tour_categories_update':
        $catController->update($_GET['id'] ?? 0); break;
    case 'tour_categories_delete':
        $catController->delete($_GET['id'] ?? 0); break;

    case 'staff':
        $staffController->index(); break;
    case 'staff_create':
        $staffController->create(); break;
    case 'staff_store':
        $staffController->store(); break;
    case 'staff_edit':
        $staffController->edit($_GET['id'] ?? 0); break;
    case 'staff_update':
        $staffController->update($_GET['id'] ?? 0); break;
    case 'staff_delete':
        $staffController->delete($_GET['id'] ?? 0); break;

    case 'home':
    default:
        require __DIR__ . '/../views/main.php';
        break;
}