<?php

$route = $_GET['r'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

require_once __DIR__ . '/../controllers/TourCategoryController.php';
require_once __DIR__ . '/../controllers/TourController.php';
require_once __DIR__ . '/../controllers/GuidesController.php';
require_once __DIR__ . '/../controllers/StaffController.php';
require_once __DIR__ . '/../controllers/ScheduleController.php';

$catController = new TourCategoryController();
$tourController = new TourController();
$guideController = new GuidesController();
$staffController = new StaffController();
$scheduleController = new ScheduleController();

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

    case 'tours':
        if ($method === 'GET') {
            $tourController->index();
        }
        break;
    case 'tours_create':
        $tourController->create(); break;
    case 'tours_store':
        if ($method === 'POST') {
            $tourController->store();
        }
        break;
    case 'tours_edit':
        $tourController->edit($_GET['id'] ?? 0); break;
    case 'tours_update':
        if ($method === 'POST') {
            $tourController->update($_GET['id'] ?? 0);
        }
        break;
    case 'tours_delete':
        $tourController->delete($_GET['id'] ?? 0); break;
    case 'tours_itinerary':
        $tourController->itinerary($_GET['id'] ?? 0); break;
    case 'tours_itinerary_add':
        if ($method === 'POST') {
            $tourController->itinerary_add_item($_POST);
        }
        break;
    case 'tours_itinerary_update':
        if ($method === 'POST') {
            $tourController->itinerary_update_item($_POST['id'] ?? 0, $_POST);
        }
        break;
    case 'tours_itinerary_delete':
        if ($method === 'POST') {
            $tourController->itinerary_delete_item($_POST['id'] ?? 0, $_POST['tour_id'] ?? 0);
        }
        break;

    case 'guides':
        $guideController->index(); break;
    case 'guides_create':
        $guideController->create(); break;
    case 'guides_store':
        $guideController->store(); break;
    case 'guides_edit':
        $guideController->edit($_GET['id'] ?? 0); break;
    case 'guides_update':
        $guideController->update($_GET['id'] ?? 0); break;
    case 'guides_delete':
        $guideController->delete($_GET['id'] ?? 0); break;

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

    case 'schedules':
        $scheduleController->index(); break;
    case 'schedules_create':
        $scheduleController->create(); break;
    case 'schedules_store':
        $scheduleController->store(); break;
    case 'schedules_edit':
        $scheduleController->edit($_GET['id'] ?? 0); break;
    case 'schedules_update':
        $scheduleController->update($_GET['id'] ?? 0); break;
    case 'schedules_delete':
        $scheduleController->delete($_GET['id'] ?? 0); break;

    case 'home':
    default:
        require __DIR__ . '/../views/main.php';
        break;
}