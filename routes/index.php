<?php

$route = $_GET['r'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

require_once __DIR__ . '/../controllers/TourCategoryController.php';
require_once __DIR__ . '/../controllers/TourController.php';
require_once __DIR__ . '/../controllers/GuidesController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/SupplierController.php';
require_once __DIR__ . '/../controllers/StaffController.php';
require_once __DIR__ . '/../controllers/ScheduleController.php';
require_once __DIR__ . '/../controllers/BookingController.php';
require_once __DIR__ . '/../controllers/TourLogController.php';
require_once __DIR__ . '/../controllers/PaymentController.php';
require_once __DIR__ . '/../controllers/CancellationPolicyController.php';
require_once __DIR__ . '/../controllers/GuideAssignmentController.php';
require_once __DIR__ . '/../controllers/GuideScheduleController.php';
require_once __DIR__ . '/../controllers/GuideRatingController.php';

$catController = new TourCategoryController();
$tourController = new TourController();
$guideController = new GuidesController();
$authController = new AuthController();
$supplierController = new SupplierController();
$staffController = new StaffController();
$scheduleController = new ScheduleController();
$tourLogController = new TourLogController();
$paymentController = new PaymentController();
$cancellationPolicyController = new CancellationPolicyController();
$guideAssignmentController = new GuideAssignmentController();
$guideScheduleController = new GuideScheduleController();
$guideRatingController = new GuideRatingController();
$bookingController = new BookingController();

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
        if ($method === 'GET') $tourController->index();
        break;
    case 'tours_create':
        $tourController->create(); break;
    case 'tours_store':
        if ($method === 'POST') $tourController->store();
        break;
    case 'tours_edit':
        $tourController->edit($_GET['id'] ?? 0); break;
    case 'tours_update':
        if ($method === 'POST') $tourController->update($_GET['id'] ?? 0);
        break;
    case 'tours_delete':
        $tourController->delete($_GET['id'] ?? 0); break;
    case 'tours_itinerary':
        $tourController->itinerary($_GET['id'] ?? 0); break;
    case 'tours_itinerary_add':
        if ($method === 'POST') $tourController->itinerary_add_item($_POST);
        break;
    case 'tours_itinerary_update':
        if ($method === 'POST') $tourController->itinerary_update_item($_POST['id'] ?? 0, $_POST);
        break;
    case 'tours_itinerary_delete':
        if ($method === 'POST') $tourController->itinerary_delete_item($_POST['id'] ?? 0, $_POST['tour_id'] ?? 0);
        break;

    case 'suppliers':
        $supplierController->index(); break;
    case 'suppliers_show':
        $supplierController->show($_GET['id'] ?? 0); break;

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

    // Auth - Guide
    case 'guide_login':
        $authController->showGuideLogin(); break;
    case 'guide_login_post':
        if ($method === 'POST') $authController->handleGuideLogin(); break;
    case 'guide_logout':
        $authController->guideLogout(); break;

         case 'guide_dashboard':
        $scheduleController->dashboard();
        break;

    // Auth - Admin
    case 'admin_login':
        $authController->showAdminLogin(); break;
    case 'admin_login_post':
        if ($method === 'POST') $authController->handleAdminLogin(); break;
    case 'admin_logout':
        $authController->adminLogout(); break;

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
    case 'staff_fix_data':
        $staffController->fixData(); break;

    case 'schedules':
        $scheduleController->index(); break;
    case 'schedules_calendar':
        $scheduleController->calendar(); break;
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

    // Nhật ký tour
    case 'tour_logs':
        $tourLogController->index(); break;
    case 'tour_logs_create':
        $tourLogController->create(); break;
    case 'tour_logs_show':
        $tourLogController->show($_GET['id'] ?? 0); break;
    case 'tour_logs_edit':
        $tourLogController->edit($_GET['id'] ?? 0); break;
    case 'tour_logs_delete':
        $tourLogController->delete($_GET['id'] ?? 0); break;

    // Booking
    case "booking":
        $bookingController->index(); break;
    case "booking_create":
        $bookingController->create(); break;
    case "booking_detail":
        $bookingController->detail(); break;
    case "booking_store":
        if ($method === 'POST') $bookingController->store(); break;
    case "booking_update_status":
        $bookingController->updateStatus(); break;
    case "booking/create":
        $bookingController->create(); break;
    case "booking/updateStatus":
        $bookingController->updateStatus(); break;
    case "booking/detail":
        $bookingController->detail(); break;
    case 'booking_edit':
        $bookingController->edit(); break;
    case 'booking_update':
        if ($method === 'POST') $bookingController->update(); break;
    case 'booking_cancel':
        $bookingController->cancel(); break;
    case 'booking_send_email':
        $bookingController->sendEmail(); break;
    case 'booking_pdf':
        $bookingController->pdf(); break;

    // Payments
    case 'payments':
        $paymentController->index(); break;
    case 'payments_create':
        $paymentController->create(); break;
    case 'payments_store':
        if ($method === 'POST') $paymentController->store(); break;
    case 'payments_edit':
        $paymentController->edit($_GET['id'] ?? 0); break;
    case 'payments_update':
        if ($method === 'POST') $paymentController->update($_GET['id'] ?? 0); break;
    case 'payments_delete':
        $paymentController->delete($_GET['id'] ?? 0); break;

    // Cancellation Policies
    case 'cancellation_policies':
        $cancellationPolicyController->index(); break;
    case 'cancellation_policies_create':
        $cancellationPolicyController->create(); break;
    case 'cancellation_policies_store':
        if ($method === 'POST') $cancellationPolicyController->store(); break;
    case 'cancellation_policies_edit':
        $cancellationPolicyController->edit($_GET['id'] ?? 0); break;
    case 'cancellation_policies_update':
        if ($method === 'POST') $cancellationPolicyController->update($_GET['id'] ?? 0); break;
    case 'cancellation_policies_delete':
        $cancellationPolicyController->delete($_GET['id'] ?? 0); break;

    // Guide Assignments
    case 'guide_assignments':
        $guideAssignmentController->index(); break;
    case 'guide_assignments_create':
        $guideAssignmentController->create(); break;
    case 'guide_assignments_store':
        if ($method === 'POST') $guideAssignmentController->store(); break;
    case 'guide_assignments_edit':
        $guideAssignmentController->edit($_GET['id'] ?? 0); break;
    case 'guide_assignments_update':
        if ($method === 'POST') $guideAssignmentController->update($_GET['id'] ?? 0); break;
    case 'guide_assignments_delete':
        $guideAssignmentController->delete($_GET['id'] ?? 0); break;
    case 'guide_assignments_show':
        $guideAssignmentController->show(); break;
    case 'guide_assignments_calendar':
        $guideAssignmentController->calendar(); break;

    // Guide Schedules
    case 'guide_schedules':
        $guideScheduleController->index(); break;
    case 'guide_schedules_create':
        $guideScheduleController->create(); break;
    case 'guide_schedules_store':
        if ($method === 'POST') $guideScheduleController->store(); break;
    case 'guide_schedules_edit':
        $guideScheduleController->edit($_GET['id'] ?? 0); break;
    case 'guide_schedules_update':
        if ($method === 'POST') $guideScheduleController->update($_GET['id'] ?? 0); break;
    case 'guide_schedules_delete':
        $guideScheduleController->delete($_GET['id'] ?? 0); break;
    case 'guide_schedules_bulk_update':
        if ($method === 'POST') $guideScheduleController->bulkUpdate(); break;

    // Guide Ratings
    case 'guide_ratings':
        $guideRatingController->index(); break;
    case 'guide_ratings_create':
        $guideRatingController->create(); break;
    case 'guide_ratings_store':
        if ($method === 'POST') $guideRatingController->store(); break;
    case 'guide_ratings_show':
        $guideRatingController->show($_GET['id'] ?? 0); break;
    case 'guide_ratings_approve':
        $guideRatingController->approve($_GET['id'] ?? 0); break;
    case 'guide_ratings_reject':
        $guideRatingController->reject($_GET['id'] ?? 0); break;
    case 'guide_ratings_hide':
        $guideRatingController->hide($_GET['id'] ?? 0); break;
    case 'guide_rating_customer_form':
        $guideRatingController->customerRatingForm(); break;

    case 'home':
    default:
        require __DIR__ . '/../views/main.php'; break;
}