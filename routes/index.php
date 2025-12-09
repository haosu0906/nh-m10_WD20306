<?php

$route = $_GET['r'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

require_once __DIR__ . '/../controllers/TourCategoryController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
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
require_once __DIR__ . '/../controllers/QRCodeController.php';
require_once __DIR__ . '/../controllers/ReportsController.php';
require_once __DIR__ . '/../controllers/GuidePortalController.php';
require_once __DIR__ . '/../controllers/TourLogsController.php';

$catController = new TourCategoryController();
$adminController = new AdminController();
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
$qrController = new QRCodeController();
$reportsController = new ReportsController();
$guidePortalController = new GuidePortalController();
$tourLogsController2 = new TourLogsController();
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
    case 'tour_categories_show':
        $catController->show($_GET['id'] ?? 0); break;

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
    case 'tour_suppliers_json':
        $tourController->suppliersJson(); break;
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

    case 'tours_show':
        $tourController->show($_GET['id'] ?? 0); break;

    case 'suppliers':
        $supplierController->index(); break;
    case 'suppliers_show':
        $supplierController->show($_GET['id'] ?? 0); break;
    case 'suppliers_create':
        $supplierController->create(); break;
    case 'suppliers_store':
        if ($method === 'POST') $supplierController->store(); break;
    case 'suppliers_edit':
        $supplierController->edit($_GET['id'] ?? 0); break;
    case 'suppliers_update':
        if ($method === 'POST') $supplierController->update($_GET['id'] ?? 0); break;
    case 'suppliers_delete':
        $supplierController->delete($_GET['id'] ?? 0); break;

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
    case 'admin_profile':
        $adminController->profile(); break;

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
    case 'schedules_show':
        $scheduleController->show($_GET['id'] ?? 0); break;
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
    case 'tour_logs_store':
        if ($method === 'POST') $tourLogsController2->store(); break;
    case 'tour_logs_update':
        if ($method === 'POST') $tourLogsController2->update(); break;
    case 'tour_logs_export':
        $tourLogController->export(); break;

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
        $bookingController->edit($_GET['id'] ?? 0); break;
    case 'booking_update':
        if ($method === 'POST') $bookingController->update(); break;
    case 'booking_cancel':
        $bookingController->cancel(); break;
    case 'booking_guest_checkin':
        $bookingController->guestCheckin(); break;
    case 'booking_guest_noshow':
        $bookingController->guestNoShow(); break;
    case 'booking_group_checkin':
        $bookingController->groupCheckin(); break;
    case 'booking_group_noshow':
        $bookingController->groupNoShow(); break;
    case 'booking_manifest':
        $bookingController->manifest(); break;
    case 'booking_supplier_confirm':
        if ($method === 'POST') $bookingController->supplierServiceConfirm(); break;
    case 'booking_supplier_remind':
        if ($method === 'POST') $bookingController->supplierServiceRemind(); break;
    case 'tour_manifest':
        require __DIR__ . '/../views/booking/manifest_departure.php'; break;
    case 'departure_group_checkin':
        $bookingController->departureGroupCheckin(); break;
    case 'departure_group_noshow':
        $bookingController->departureGroupNoShow(); break;
    case 'departure_group_pending':
        $bookingController->departureGroupPending(); break;
    case 'guest_checkin_history':
        $bookingController->guestCheckinHistory(); break;
    case 'tour_manifest_export':
        $bookingController->manifestDepartureExport(); break;
    case 'booking_send_email':
        $bookingController->sendEmail(); break;
    case 'booking_pdf':
        $bookingController->pdf(); break;
    case 'qr':
        $qrController->generate(); break;
    case 'qr_scan':
        require __DIR__ . '/../views/qr/scan.php'; break;
    case 'reports_profit':
        $reportsController->profit(); break;
    case 'reports_profit_detail':
        $reportsController->profitDetail(); break;
    case 'reports_profit_export':
        $reportsController->export(); break;
    case 'reports_debts':
        $reportsController->debts(); break;
    case 'reports_debts_export':
        $reportsController->debtsExport(); break;
    case 'guide_portal':
        $guidePortalController->index(); break;
    case 'guide_portal_customers':
        $guidePortalController->customers(); break;
    case 'guide_portal_update_guest_note':
        if ($method === 'POST') $guidePortalController->updateGuestNote(); else { header('HTTP/1.1 405 Method Not Allowed'); echo 'method_not_allowed'; }
        break;
    case 'booking_assign_room':
        if ($method === 'POST') $bookingController->assignRoom(); break;
    case 'booking_unassign_room':
        $bookingController->unassignRoom(); break;
    case 'booking_delete':
        $bookingController->delete($_GET['id'] ?? 0); break;

    // Payments
    case 'booking_add_payment':
        $paymentController->create($_GET['id'] ?? 0); break;
    case 'payments':
        $paymentController->index(); break;
    case 'booking_payment_history':
        $paymentController->bookingPaymentHistory(); break;
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
    case 'payments_refund':
        if ($method === 'POST') $paymentController->refund($_GET['id'] ?? 0); break;

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

    case 'admin_dashboard':
        $adminController->dashboard(); break;
    case 'home':
        try { $adminController->dashboard(); }
        catch (Throwable $e) { require __DIR__ . '/../views/main.php'; }
        break;
    default:
        try { $adminController->dashboard(); }
        catch (Throwable $e) { require __DIR__ . '/../views/main.php'; }
        break;
}
