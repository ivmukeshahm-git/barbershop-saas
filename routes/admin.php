<?php

/**
 * Shop-tenant admin routes (loaded under /{shop}/admin).
 * Route names use the admin.* prefix from the parent group.
 */

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GstController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentmethodController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServicecategoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShopcloseController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffreportController;
use App\Http\Controllers\Admin\StaffServiceController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\WorkinghoursController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;

Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

Route::get('plan-expired', [BillingController::class, 'expired'])->name('plan.expired');
Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
Route::post('billing/renew', [BillingController::class, 'renew'])
    ->middleware('throttle:10,1')
    ->name('billing.renew');
Route::post('billing/branding', [BillingController::class, 'updateBranding'])->name('billing.branding');
Route::post('billing/currency', [BillingController::class, 'updateCurrency'])->name('billing.currency');

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('canany:Dashboard-Today_Appointments,Dashboard-Upcoming_Appointments,Dashboard-Top_Services,Dashboard-Top_Staff');
Route::post('get-today-appointment', [DashboardController::class, 'GetTodayGetTableData'])
    ->name('get-today-appointment.gettabledata');
Route::post('get-upcoming-appointment', [DashboardController::class, 'GetUpcomingGetTableData'])
    ->name('get-upcoming-appointment.gettabledata');
Route::post('top-services', [DashboardController::class, 'TopServicesGetTableData'])
    ->name('top-services.gettabledata');
Route::post('staff-performance', [DashboardController::class, 'getStaffPerformance'])
    ->name('staff-performance.gettabledata');

Route::resource('service-category', ServicecategoryController::class)->middleware('canany:	Service_Category-Add,Service_Category-Edit,	Service_Category-Delete');
Route::post('service-category-action', [ServicecategoryController::class, 'GetTableData'])
    ->name('service-category.gettabledata');

Route::resource('service', ServiceController::class)->middleware('canany:Service-Add,Service-Edit,Service-Delete');
Route::post('service-action', [ServiceController::class, 'GetTableData'])
    ->name('service.gettabledata');
Route::post('service-home-section', [ServiceController::class, 'updateHomeSection'])
    ->name('service.home-section');

Route::resource('product', ProductController::class)->middleware('canany:Product-Add,Product-Edit,	Product-Delete');
Route::post('product-action', [ProductController::class, 'GetTableData'])
    ->name('product.gettabledata');

Route::resource('gst', GstController::class)->middleware('canany:Gst-Add,Gst-Edit,Gst-Delete');
Route::post('gst-action', [GstController::class, 'GetTableData'])
    ->name('gst.gettabledata');

Route::resource('shop-closures', ShopcloseController::class)->middleware('canany:Shop_Closures-Add,Shop_Closures-Edit,Shop_Closures-Delete');
Route::post('shop-closures-action', [ShopcloseController::class, 'GetTableData'])
    ->name('shop-closures.gettabledata');

Route::resource('slot', SlotController::class)->middleware('canany:Slot-Update');

Route::resource('setting', SettingController::class)->middleware('can:Setting-Payment_Method');

Route::resource('customers', CustomerController::class)->middleware('canany:Customer-Add,Customer-Edit,Customer-Delete');
Route::post('customers-action', [CustomerController::class, 'GetTableData'])
    ->name('customers.gettabledata');

Route::get('customer-profile/{id}', [CustomerController::class, 'customer_profile'])
    ->name('customer.customer-profile');
Route::post('customer-pending-appointment-action', [CustomerController::class, 'PendingAppointmentgetTableData'])
    ->name('customer-pending-appointment.gettabledata');

Route::resource('appointment', AppointmentController::class)->middleware('canany:Appointment-View');
Route::post('appointment-action', [AppointmentController::class, 'GetTableData'])
    ->name('appointment.gettabledata')->middleware('can:Appointment-View');
Route::post('appointment.get-customer-appointment-details', [AppointmentController::class, 'get_customer_appointment_details'])
    ->name('appointment.get-customer-appointment-details')->middleware('can:Appointment-View');
Route::post('appointment.get-staff-by-service', [AppointmentController::class, 'get_staff_by_service'])
    ->name('appointment.get-staff-by-service')->middleware('can:Appointment-View');
Route::post('appointment.confirm-reject-action', [AppointmentController::class, 'confirm_reject_action'])
    ->name('appointment.confirm-reject-action');
Route::post('appointment.submit-reschedule-appointment', [AppointmentController::class, 'submit_reschedule_appointment'])
    ->name('appointment.submit-reschedule-appointment');
Route::post('appointment.get-appointment-details', [AppointmentController::class, 'get_appointment_details'])
    ->name('appointment.get-appointment-details')->middleware('can:Appointment-View');

Route::middleware('canany:Role-Add,Role-Edit')->group(function () {
    Route::resource('roles', RolesController::class);
    Route::post('roles-action', [RolesController::class, 'GetTableData'])
        ->name('roles.gettabledata');
    Route::post('roles/{id}/permissions', [RolesController::class, 'getRolePermissions'])
        ->name('roles.permissions');
});

Route::get('assign-role-super-admin', [AuthController::class, 'RoleAssignSuperAdmin'])
    ->name('assign-role-super-admin');

Route::resource('staff', StaffController::class)->middleware('canany:Staff-Add,Staff-Edit,Staff-Delete');
Route::post('staff-action', [StaffController::class, 'GetTableData'])
    ->name('staff.gettabledata');
Route::post('change-password', [StaffController::class, 'changePassword'])
    ->name('staff.change-password');

Route::get('staff-service', [StaffServiceController::class, 'index'])
    ->name('staff-service.index')->middleware('canany:Staff-Add,Staff-Edit,Staff-Delete');
Route::post('staff-service-action', [StaffServiceController::class, 'GetTableData'])
    ->name('staff-service.gettabledata');
Route::post('staff-service/get-services', [StaffServiceController::class, 'getAssignedServices'])
    ->name('staff-service.get-services');
Route::post('staff-service/save', [StaffServiceController::class, 'assignServices'])
    ->name('staff-service.save');

Route::get('staff-profile/{id}', [StaffController::class, 'staff_profile'])
    ->name('staff.staff-profile');
Route::post('staff-profile-appointment-action', [StaffController::class, 'AppointmentGetTableData'])
    ->name('staff-profile-appointment.gettabledata');
Route::post('staff-profile-service-assign-action', [StaffController::class, 'AssignServicesGetTableData'])
    ->name('staff-profile-service-assign.gettabledata');
Route::post('staff-profile-daily-earning-action', [StaffController::class, 'DailyEarningGetTableData'])
    ->name('staff-profile-daily-earning.gettabledata');

Route::get('incalender', [AppointmentController::class, 'calenderIndex'])->name('calender.index')->middleware('canany:Calender-View');
Route::post('appointMentBook', [AppointmentController::class, 'calenderAppointMentStore'])->name('calender.book.store');
Route::post('breakFormSubmit', [AppointmentController::class, 'breakFormSubmit'])->name('calender.breakFormSubmit');
Route::get('/appointments/fetch', [AppointmentController::class, 'fetch'])->name('appointments.fetch');
Route::get('/staff-availability', [AppointmentController::class, 'fetchStaffAvailability'])->name('staff.availability');
Route::get('getstaffByService', [StaffController::class, 'getStaffByService'])->name('staffby.services');

Route::middleware('canany:Pos_invoice-Add,Pos_invoice-Edit')->group(function () {
    Route::get('invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::get('invoice/create/{id}', [InvoiceController::class, 'createFromAppointment'])->name('invoice.create-from-appointment');
    Route::get('invoice/{id}/details', [InvoiceController::class, 'getInvoiceDetails'])->name('invoice.details');
    Route::get('invoice/edit/{id}', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::post('invoice/store', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::post('invoice/update', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::delete('invoice/{id}', [InvoiceController::class, 'destroy'])->name('invoice.destroy')->middleware('can:Pos_invoice-Edit');
    Route::post('invoice/{id}/restore', [InvoiceController::class, 'restoreInvoice'])->name('invoice.restore')->middleware('can:Pos_invoice-Edit');
    Route::get('getCustomer', [InvoiceController::class, 'getCustomerDetails'])->name('invoice.customerDetials');
    Route::post('send-invoice-mail', [InvoiceController::class, 'SendInvoiceMail'])->name('mail.send-invoice-mail');
});

Route::middleware(['canany:Report-View', 'not.staff'])->group(function () {
    Route::get('reports', [ReportController::class, 'index'])->name('reports');
    Route::post('get-report-appointment', [ReportController::class, 'getAppointmentsTableData'])->name('reports.get-report-appointment');
    Route::post('get-report-payment', [ReportController::class, 'getPaymentMethodsTableData'])->name('reports.get-report-payment');
    Route::post('get-staff-performance', [ReportController::class, 'GetStaffEarnGetTableData'])->name('reports.get-staff-performance');
    Route::post('customer-report-invoice', [ReportController::class, 'ReportCustomerInvoices'])->name('customer-report-invoice.gettabledata');
    Route::post('sell-product-report', [ReportController::class, 'ProductSellReport'])->name('sell-product-report.gettabledata');
    Route::post('top-selling-product-report', [ReportController::class, 'TopSellingProductReport'])->name('top-selling-product-report.gettabledata');
});

Route::resource('payment-method', PaymentmethodController::class)->middleware('can:Setting-Payment_Method');

Route::resource('working-hour', WorkinghoursController::class)->middleware(['staff.only', 'can:Calender-View']);
Route::post('working-hour/full-day-off-staff', [WorkinghoursController::class, 'updateFullDayOffStaff'])->name('working-hour.full-day-off-staff');

Route::get('testimonial', [TestimonialController::class, 'index'])->name('testimonial.index')->middleware('canany:Testimonial-Add,Testimonial-Edit,Testimonial-Delete');
Route::post('testimonial/gettabledata', [TestimonialController::class, 'getTableData'])->name('testimonial.gettabledata');
Route::post('testimonial', [TestimonialController::class, 'store'])->name('testimonial.store')->middleware('can:Testimonial-Add');
Route::put('testimonial/{id}', [TestimonialController::class, 'update'])->name('testimonial.update')->middleware('can:Testimonial-Edit');
Route::delete('testimonial/{id}', [TestimonialController::class, 'destroy'])->name('testimonial.destroy')->middleware('can:Testimonial-Delete');

Route::middleware('canany:Gallery-Add,Gallery-Edit,Gallery-Delete')->group(function () {
    Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::post('gallery/gettabledata', [GalleryController::class, 'getTableData'])->name('gallery.gettabledata');
});
Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store')->middleware('can:Gallery-Add');
Route::put('gallery/{id}', [GalleryController::class, 'update'])->name('gallery.update')->middleware('can:Gallery-Edit');
Route::delete('gallery/{id}', [GalleryController::class, 'destroy'])->name('gallery.destroy')->middleware('can:Gallery-Delete');

Route::middleware(['staff.only', 'can:Report-View'])->group(function () {
    Route::get('staff-report', [StaffreportController::class, 'index'])->name('staff.staff-report');
    Route::post('staff-report-appointment', [StaffreportController::class, 'getAppointmentsTableData'])->name('staff-report-appointment.gettabledata');
    Route::post('staff-report-assign-service', [StaffreportController::class, 'AssignServicesGetTableData'])->name('staff-report-assign-service.gettabledata');
    Route::post('staff-report-earning', [StaffreportController::class, 'DailyEarningGetTableData'])->name('staff-report-earning.gettabledata');
});
