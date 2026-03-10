<?php

use App\Http\Controllers\AdminFeeController;
use App\Http\Controllers\AdminParentController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AuthLoginController;
use App\Http\Controllers\StaffDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthLoginController::class, 'login'])->name('login');
Route::post('/authenticate', [AuthLoginController::class, 'authenticate'])
    ->name('authenticate');
Route::get('/authenticate', function () {
    return redirect()->route('login');
});

Route::post('/logout', [AuthLoginController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthLoginController::class, 'logout']);

// Password Reset Routes
Route::get('forgot-password', [App\Http\Controllers\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [App\Http\Controllers\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [App\Http\Controllers\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [App\Http\Controllers\ForgotPasswordController::class, 'reset'])->name('password.update');

Route::post('/webhooks/paymongo', [\App\Http\Controllers\ParentPaymentController::class, 'webhook'])->name('webhooks.paymongo');

// Protected Routes
Route::middleware(['auth', 'checkMaintenance'])->group(function () {
    Route::get('/change-password', [AuthLoginController::class, 'changePassword'])->name('auth.password.change');
    Route::post('/change-password', [AuthLoginController::class, 'updatePassword'])->name('auth.password.update');
    Route::get('/user_dashboard', [AuthLoginController::class, 'user_dashboard'])->name('user_dashboard');
    Route::get('/student/fees/summary', [AuthLoginController::class, 'user_fee_summary'])->name('student.fees.summary');

    Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [AuthLoginController::class, 'user_dashboard'])->middleware('ensureRole:parent')->name('dashboard');
        Route::get('/metrics', [AuthLoginController::class, 'parent_metrics'])->middleware('ensureRole:parent')->name('metrics');
        Route::post('/link-student', [AuthLoginController::class, 'linkStudent'])->middleware('ensureRole:parent')->name('link_student');
        Route::post('/unlink-student', [AuthLoginController::class, 'unlinkStudent'])->middleware('ensureRole:parent')->name('unlink_student');

        // Receipt routes (PDF first to avoid {payment} catching "pdf")
        Route::get('/receipt/{payment}/pdf', [\App\Http\Controllers\ParentPaymentController::class, 'receiptPdf'])->middleware('ensureRole:parent')->name('receipt.pdf');
        Route::get('/receipt/{payment}', [\App\Http\Controllers\ParentPaymentController::class, 'showReceipt'])->middleware('ensureRole:parent')->name('receipts.download');

        Route::get('/history', [\App\Http\Controllers\ParentPaymentController::class, 'history'])->middleware('ensureRole:parent')->name('history');

        // SOA routes (PDF first to avoid {student} catching "pdf")
        Route::get('/soa/{student}/pdf', [\App\Http\Controllers\ParentFeesController::class, 'soaPdf'])->middleware('ensureRole:parent')->name('soa.pdf');
        Route::get('/soa/{student}', [\App\Http\Controllers\ParentFeesController::class, 'soa'])->middleware('ensureRole:parent')->name('soa');

        Route::get('/pay', [\App\Http\Controllers\ParentPaymentController::class, 'show'])->middleware('ensureRole:parent')->name('pay');
        Route::post('/pay', [\App\Http\Controllers\ParentPaymentController::class, 'store'])->middleware('ensureRole:parent')->name('pay.store');
        Route::get('/pay/success', [\App\Http\Controllers\ParentPaymentController::class, 'success'])->middleware('ensureRole:parent')->name('pay.success');
        Route::get('/pay/cancel', [\App\Http\Controllers\ParentPaymentController::class, 'cancel'])->middleware('ensureRole:parent')->name('pay.cancel');

        // Multi-Child Combined Payment
        Route::get('/pay/multi', [\App\Http\Controllers\ParentPaymentController::class, 'multiShow'])->middleware('ensureRole:parent')->name('pay.multi');
        Route::post('/pay/multi', [\App\Http\Controllers\ParentPaymentController::class, 'multiStore'])->middleware('ensureRole:parent')->name('pay.multi.store');
        Route::get('/pay/multi/success', [\App\Http\Controllers\ParentPaymentController::class, 'multiSuccess'])->middleware('ensureRole:parent')->name('pay.multi.success');
        Route::get('/pay/multi/cancel', [\App\Http\Controllers\ParentPaymentController::class, 'multiCancel'])->middleware('ensureRole:parent')->name('pay.multi.cancel');

        Route::get('/student/{student}/fees', [\App\Http\Controllers\ParentFeesController::class, 'show'])->middleware('ensureRole:parent')->name('fees.show');

        // Payment Schedule
        Route::get('/student/{student}/schedule', [\App\Http\Controllers\ParentFeesController::class, 'schedule'])->middleware('ensureRole:parent')->name('schedule');

        // Notifications (static routes first to avoid {id} catching "read-all" / "unread-count")
        Route::get('/notifications/unread-count', [\App\Http\Controllers\ParentNotificationController::class, 'unreadCount'])->middleware('ensureRole:parent')->name('notifications.unreadCount');
        Route::post('/notifications/read-all', [\App\Http\Controllers\ParentNotificationController::class, 'markAllAsRead'])->middleware('ensureRole:parent')->name('notifications.readAll');
        Route::get('/notifications', [\App\Http\Controllers\ParentNotificationController::class, 'index'])->middleware('ensureRole:parent')->name('notifications');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\ParentNotificationController::class, 'markAsRead'])->middleware('ensureRole:parent')->name('notifications.read');

        // Parent Profile
        Route::get('/profile', [\App\Http\Controllers\ParentProfileController::class, 'edit'])->middleware('ensureRole:parent')->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\ParentProfileController::class, 'update'])->middleware('ensureRole:parent')->name('profile.update');
    });
    Route::get('/admin_dashboard', [AuthLoginController::class, 'admin_dashboard'])->middleware('ensureRole:admin')->name('admin_dashboard');
    Route::get('/admin_dashboard/metrics', [AuthLoginController::class, 'admin_metrics'])->middleware('ensureRole:admin|staff')->name('admin_dashboard.metrics');

    // Admin Password Requests
    Route::prefix('admin/requests')->name('admin.requests.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminPasswordRequestController::class, 'index'])->name('index');
        Route::post('/{request}/approve', [\App\Http\Controllers\AdminPasswordRequestController::class, 'approve'])->name('approve');
        Route::post('/{request}/reject', [\App\Http\Controllers\AdminPasswordRequestController::class, 'reject'])->name('reject');
        Route::delete('/{request}', [\App\Http\Controllers\AdminPasswordRequestController::class, 'destroy'])->name('destroy');
    });

    // Staff Dashboard and actions
    Route::get('/staff_dashboard', [StaffDashboardController::class, 'index'])->middleware('ensureRole:staff')->name('staff_dashboard');
    Route::get('/staff_dashboard/list', [StaffDashboardController::class, 'list'])->middleware('ensureRole:staff')->name('staff_dashboard.list');
    Route::post('/staff/remind/{student}', [StaffDashboardController::class, 'remind'])->name('staff.remind');
    Route::post('/staff/approve/{student}', [StaffDashboardController::class, 'approve'])->name('staff.approve');

    // Staff-specific routes for the new design
    Route::prefix('staff')->name('staff.')->middleware('auth')->group(function () {
        Route::get('/payment-processing', [\App\Http\Controllers\StaffPaymentController::class, 'index'])->middleware('ensureRole:staff')->name('payment_processing');
        Route::post('/payments', [\App\Http\Controllers\StaffPaymentController::class, 'store'])->middleware('ensureRole:staff')->name('payments.store');
        Route::post('/payments/bulk', [\App\Http\Controllers\StaffPaymentController::class, 'bulkStore'])->middleware('ensureRole:staff')->name('payments.bulk_store');
        Route::get('/payments/{payment}/receipt', [\App\Http\Controllers\StaffPaymentController::class, 'showReceipt'])->middleware('ensureRole:staff')->name('payments.receipt');

        // SMS Reminders
        Route::get('/sms-reminders', [\App\Http\Controllers\StaffSmsController::class, 'index'])->middleware('ensureRole:staff')->name('sms_reminders');
        Route::post('/sms-reminders/send', [\App\Http\Controllers\StaffSmsController::class, 'send'])->middleware('ensureRole:staff')->name('sms_reminders.send');
        Route::post('/sms-reminders/refresh', [\App\Http\Controllers\StaffSmsController::class, 'refreshStatus'])->middleware('ensureRole:staff')->name('sms_reminders.refresh');
        Route::delete('/sms-reminders/schedule/{id}', [\App\Http\Controllers\StaffSmsController::class, 'cancelSchedule'])->middleware('ensureRole:staff')->name('sms_reminders.schedule.cancel');

        // Reports
        Route::get('/reports', [\App\Http\Controllers\StaffReportsController::class, 'index'])->middleware('ensureRole:staff')->name('reports');
        Route::post('/reports/export/csv', [\App\Http\Controllers\StaffReportsController::class, 'exportCsv'])->middleware('ensureRole:staff')->name('reports.export.csv');
        Route::get('/reports/download/{id}', [\App\Http\Controllers\StaffReportsController::class, 'downloadReport'])->middleware('ensureRole:staff')->name('reports.download');
        Route::post('/reports/schedule', [\App\Http\Controllers\StaffReportsController::class, 'schedule'])->middleware('ensureRole:staff')->name('reports.schedule');
        Route::delete('/reports/schedule/{id}', [\App\Http\Controllers\StaffReportsController::class, 'destroy'])->middleware('ensureRole:staff')->name('reports.schedule.destroy');

        // Payment History
        Route::get('/payment-history', [\App\Http\Controllers\StaffPaymentHistoryController::class, 'index'])->middleware('ensureRole:staff')->name('payment_history');

        // Payment Void Requests
        Route::post('/payments/{payment}/void', [\App\Http\Controllers\StaffPaymentVoidController::class, 'store'])->middleware('ensureRole:staff')->name('payments.void');

        // Student Details
        Route::get('/student-details/{student}', [\App\Http\Controllers\StaffStudentDetailsController::class, 'show'])->middleware('ensureRole:staff')->name('student_details');
        Route::post('/student-details/{student}/update-category', [\App\Http\Controllers\StaffStudentDetailsController::class, 'updateCategory'])->middleware('ensureRole:staff')->name('student_details.update_category');

        // Fee Records (Editing)
        Route::post('/fee-records/{record}', [\App\Http\Controllers\StaffRecordsController::class, 'update'])->middleware('ensureRole:staff')->name('fee_records.update');
        Route::post('/fee-records', [\App\Http\Controllers\StaffRecordsController::class, 'store'])->middleware('ensureRole:staff')->name('fee_records.store');

        // Notifications
        Route::get('/notifications/unread-count', [\App\Http\Controllers\StaffNotificationController::class, 'unreadCount'])->middleware('ensureRole:staff')->name('notifications.unreadCount');
        Route::post('/notifications/read-all', [\App\Http\Controllers\StaffNotificationController::class, 'markAllAsRead'])->middleware('ensureRole:staff')->name('notifications.readAll');
        Route::get('/notifications', [\App\Http\Controllers\StaffNotificationController::class, 'index'])->middleware('ensureRole:staff')->name('notifications');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\StaffNotificationController::class, 'markAsRead'])->middleware('ensureRole:staff')->name('notifications.read');

        // Audit Trail / Activity Log
        Route::get('/audit-trail', [\App\Http\Controllers\StaffAuditTrailController::class, 'index'])->middleware('ensureRole:staff')->name('audit_trail');
    });

    // Admin Audit Logs
    Route::prefix('admin/audit-logs')->name('admin.audit-logs.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminAuditLogController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\AdminAuditLogController::class, 'export'])->name('export');
    });

    // Admin SMS delivery status callbacks
    Route::post('/webhooks/sms/twilio', [\App\Http\Controllers\AdminSmsController::class, 'twilioCallback'])->name('webhooks.sms.twilio');
    Route::post('/webhooks/sms/philsms', [\App\Http\Controllers\AdminSmsController::class, 'philsmsCallback'])->name('webhooks.sms.philsms');

    // Alias for backward compatibility - points to enrollment list
    Route::get('/admin/students', [\App\Http\Controllers\AdminStudentEnrollmentController::class, 'index'])->name('admin.students.index')->middleware('ensureRole:admin');
    Route::get('/admin/staff', [\App\Http\Controllers\AdminStaffController::class, 'index'])->name('admin.staff.index')->middleware('ensureRole:admin');

    // Admin Student Enrollment
    Route::prefix('admin/enrollment')->name('admin.enrollment.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminStudentEnrollmentController::class, 'index'])->name('index');
        
        Route::get('/{student}', [\App\Http\Controllers\AdminStudentEnrollmentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [\App\Http\Controllers\AdminStudentEnrollmentController::class, 'edit'])->name('edit');
        Route::get('/{student}/print', [\App\Http\Controllers\AdminStudentEnrollmentController::class, 'printStatement'])->name('print');
        Route::put('/{student}', [\App\Http\Controllers\AdminStudentEnrollmentController::class, 'update'])->name('update');
        Route::delete('/{student}', [\App\Http\Controllers\AdminStudentEnrollmentController::class, 'destroy'])->name('destroy');
        Route::post('/{student}/adjustments', [\App\Http\Controllers\StudentFeeAdjustmentController::class, 'store'])->name('adjustments.store');

        // Student Discounts
        Route::post('/{student}/discounts', [\App\Http\Controllers\StudentDiscountController::class, 'store'])->name('discounts.store');
        Route::delete('/{student}/discounts/{discount}', [\App\Http\Controllers\StudentDiscountController::class, 'destroy'])->name('discounts.destroy');
    });

    // Admin Fee Management
    Route::prefix('admin/fees')->name('admin.fees.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [AdminFeeController::class, 'index'])->name('index');
        Route::get('/summary', [AdminFeeController::class, 'summary'])->name('summary');

        // Tuition Fees
        Route::get('/tuition/create', [AdminFeeController::class, 'createTuitionFee'])->name('create-tuition');
        Route::post('/tuition', [AdminFeeController::class, 'storeTuitionFee'])->name('store-tuition');
        Route::get('/tuition/{tuitionFee}', [AdminFeeController::class, 'showTuitionFee'])->name('show-tuition');
        Route::get('/tuition/{tuitionFee}/edit', [AdminFeeController::class, 'editTuitionFee'])->name('edit-tuition');
        Route::put('/tuition/{tuitionFee}', [AdminFeeController::class, 'updateTuitionFee'])->name('update-tuition');
        Route::match(['post', 'patch'], '/tuition/{tuitionFee}/toggle', [AdminFeeController::class, 'toggleTuitionStatus'])->name('toggle-tuition');
        Route::delete('/tuition/{tuitionFee}', [AdminFeeController::class, 'destroyTuitionFee'])->name('destroy-tuition');

        // Charges
        Route::get('/charges/create', [AdminFeeController::class, 'createAdditionalCharge'])->name('create-charge');
        Route::post('/charges', [AdminFeeController::class, 'storeAdditionalCharge'])->name('store-charge');
        Route::get('/charges/{charge}/edit', [AdminFeeController::class, 'editAdditionalCharge'])->name('edit-charge');
        Route::put('/charges/{charge}', [AdminFeeController::class, 'updateAdditionalCharge'])->name('update-charge');
        Route::delete('/charges/{charge}', [AdminFeeController::class, 'destroyAdditionalCharge'])->name('destroy-charge');

        // Discounts
        Route::get('/discounts/create', [AdminFeeController::class, 'createDiscount'])->name('create-discount');
        Route::post('/discounts', [AdminFeeController::class, 'storeDiscount'])->name('store-discount');
        Route::get('/discounts/{discount}/edit', [AdminFeeController::class, 'editDiscount'])->name('edit-discount');
        Route::put('/discounts/{discount}', [AdminFeeController::class, 'updateDiscount'])->name('update-discount');
        Route::delete('/discounts/{discount}', [AdminFeeController::class, 'destroyDiscount'])->name('destroy-discount');
        Route::post('/discounts/assign-group', [AdminFeeController::class, 'assignDiscountToGroup'])->name('assign-discount-group');
        Route::post('/discounts/assign-students', [AdminFeeController::class, 'assignDiscountToStudents'])->name('assign-discount-students');

        // Fee Assignments
        Route::post('/assignments/generate', [AdminFeeController::class, 'generateFeeAssignments'])->name('assignments.generate');
        Route::post('/assignments/{feeAssignment}/recalculate', [AdminFeeController::class, 'recalculateStudentFees'])->name('assignments.recalculate');
    });

    // Admin SMS Management
    Route::prefix('admin/sms')->name('admin.sms.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/templates', [\App\Http\Controllers\AdminSmsController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [\App\Http\Controllers\AdminSmsController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [\App\Http\Controllers\AdminSmsController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/templates/{template}/edit', [\App\Http\Controllers\AdminSmsController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{template}', [\App\Http\Controllers\AdminSmsController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{template}', [\App\Http\Controllers\AdminSmsController::class, 'destroy'])->name('templates.destroy');

        Route::get('/logs', [\App\Http\Controllers\AdminSmsController::class, 'logs'])->name('logs');
        Route::post('/logs/{log}/resend', [\App\Http\Controllers\AdminSmsController::class, 'resend'])->name('logs.resend');
        Route::get('/logs/statuses', [\App\Http\Controllers\AdminSmsController::class, 'statuses'])->name('logs.statuses');
        Route::post('/logs/{log}/simulate', [\App\Http\Controllers\AdminSmsController::class, 'simulate'])->name('logs.simulate');
    });

    // Admin Payment Approvals
    Route::prefix('admin/payment-approvals')->name('admin.payment_approvals.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminPaymentApprovalController::class, 'index'])->name('index');
        Route::post('/{payment}/approve', [\App\Http\Controllers\AdminPaymentApprovalController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject', [\App\Http\Controllers\AdminPaymentApprovalController::class, 'reject'])->name('reject');
    });

    // Admin Online Payment Confirmations
    Route::prefix('admin/online-confirmations')->name('admin.online_confirmations.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminOnlineConfirmationController::class, 'index'])->name('index');
        Route::post('/{payment}/confirm', [\App\Http\Controllers\AdminOnlineConfirmationController::class, 'confirm'])->name('confirm');
        Route::post('/{payment}/reject', [\App\Http\Controllers\AdminOnlineConfirmationController::class, 'reject'])->name('reject');
    });

    // Admin Void Approvals
    Route::prefix('admin/void-approvals')->name('admin.void_approvals.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminPaymentVoidController::class, 'index'])->name('index');
        Route::post('/{voidRequest}/approve', [\App\Http\Controllers\AdminPaymentVoidController::class, 'approve'])->name('approve');
        Route::post('/{voidRequest}/reject', [\App\Http\Controllers\AdminPaymentVoidController::class, 'reject'])->name('reject');
    });

    // Admin Student Link Approvals
    Route::prefix('admin/link-approvals')->name('admin.link_approvals.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminLinkApprovalController::class, 'index'])->name('index');
        Route::post('/{linkRequest}/approve', [\App\Http\Controllers\AdminLinkApprovalController::class, 'approve'])->name('approve');
        Route::post('/{linkRequest}/reject', [\App\Http\Controllers\AdminLinkApprovalController::class, 'reject'])->name('reject');
    });

    // Admin Reports
    Route::prefix('admin/reports')->name('admin.reports.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminReportsController::class, 'index'])->name('index');
        Route::get('/metrics', [\App\Http\Controllers\AdminReportsController::class, 'metrics'])->name('metrics');
        Route::post('/export/csv', [\App\Http\Controllers\AdminReportsController::class, 'exportCsv'])->name('export.csv');
        Route::post('/export/sms-csv', [\App\Http\Controllers\AdminReportsController::class, 'exportSmsCsv'])->name('export.sms-csv');
        Route::post('/schedule', [\App\Http\Controllers\AdminReportsController::class, 'schedule'])->name('schedule');
        Route::delete('/schedule/{id}', [\App\Http\Controllers\AdminReportsController::class, 'destroy'])->name('destroy');
        Route::get('/download/{id}', [\App\Http\Controllers\AdminReportsController::class, 'downloadReport'])->name('download');
    });

    // Admin Settings
    Route::prefix('admin/settings')->name('admin.settings.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('index');
        Route::match(['put', 'post'], '/', [\App\Http\Controllers\AdminSettingsController::class, 'update'])->name('update');
        Route::post('/reset-demo', [\App\Http\Controllers\AdminSettingsController::class, 'resetDemoData'])->name('reset-demo');
        Route::post('/clear-cache', [\App\Http\Controllers\AdminSettingsController::class, 'clearCache'])->name('clear-cache');
        Route::post('/reset-database', [\App\Http\Controllers\AdminSettingsController::class, 'resetDatabase'])->name('reset-database');
        Route::post('/export-db', [\App\Http\Controllers\AdminSettingsController::class, 'exportDatabase'])->name('export-db');
    });

    // Admin Parents Management
    Route::prefix('admin/parents')->name('admin.parents.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminParentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\AdminParentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\AdminParentController::class, 'store'])->name('store');
        Route::get('/{parent}/edit', [\App\Http\Controllers\AdminParentController::class, 'edit'])->name('edit');
        Route::put('/{parent}', [\App\Http\Controllers\AdminParentController::class, 'update'])->name('update');
        Route::delete('/{parent}', [\App\Http\Controllers\AdminParentController::class, 'destroy'])->name('destroy');
    });
});

// Super Admin Routes
Route::prefix('super-admin')->name('super_admin.')->middleware('ensureRole:super_admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('dashboard');

    // Super Admin Manage Students
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/export', [\App\Http\Controllers\AdminStudentController::class, 'exportMasterList'])->name('export');
        Route::get('/search', [\App\Http\Controllers\AdminStudentController::class, 'search'])->name('search');
        Route::get('/sections', [\App\Http\Controllers\AdminStudentController::class, 'sectionsList'])->name('sections.list');
        Route::get('/generate-id', [\App\Http\Controllers\AdminStudentController::class, 'generateStudentId'])->name('generateId');
        Route::get('/search-for-sibling', [\App\Http\Controllers\AdminStudentController::class, 'searchForSibling'])->name('searchForSibling');
        Route::get('/', [\App\Http\Controllers\AdminStudentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\AdminStudentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\AdminStudentController::class, 'store'])->name('store');
        Route::post('/section', [\App\Http\Controllers\AdminStudentController::class, 'storeSection'])->name('storeSection');
        Route::post('/strand', [\App\Http\Controllers\AdminStudentController::class, 'storeStrand'])->name('storeStrand');
        Route::delete('/sections/{section}', [\App\Http\Controllers\AdminStudentController::class, 'destroySection'])->name('destroySection');
        Route::get('/{student}/edit', [\App\Http\Controllers\AdminStudentController::class, 'edit'])->name('edit');
        Route::get('/{student}/siblings', [\App\Http\Controllers\AdminStudentController::class, 'siblings'])->name('siblings');
        Route::post('/{student}/siblings/link', [\App\Http\Controllers\AdminStudentController::class, 'linkSibling'])->name('siblings.link');
        Route::post('/{student}/siblings/unlink', [\App\Http\Controllers\AdminStudentController::class, 'unlinkSibling'])->name('siblings.unlink');
        Route::post('/{student}/charges', [\App\Http\Controllers\AdminStudentController::class, 'addCharge'])->name('charges.add');
        Route::delete('/{student}/charges/{charge}', [\App\Http\Controllers\AdminStudentController::class, 'removeCharge'])->name('charges.remove');
        Route::post('/{student}/adjustments', [\App\Http\Controllers\AdminStudentController::class, 'storeAdjustment'])->name('adjustments.store');
        Route::post('/{student}/recalculate-fees', [\App\Http\Controllers\AdminStudentController::class, 'recalculateFees'])->name('recalculateFees');
        Route::put('/{student}', [\App\Http\Controllers\AdminStudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [\App\Http\Controllers\AdminStudentController::class, 'destroy'])->name('destroy');
        Route::patch('/{student}/archive', [\App\Http\Controllers\AdminStudentController::class, 'archive'])->name('archive');
        Route::patch('/{student}/unarchive', [\App\Http\Controllers\AdminStudentController::class, 'unarchive'])->name('unarchive');
        Route::patch('/{student}/status', [\App\Http\Controllers\AdminStudentController::class, 'changeStatus'])->name('changeStatus');
        Route::post('/promote-section', [\App\Http\Controllers\AdminStudentController::class, 'promoteSection'])->name('promoteSection');
        Route::get('/import-template', [\App\Http\Controllers\AdminStudentController::class, 'downloadImportTemplate'])->name('importTemplate');
        Route::post('/import', [\App\Http\Controllers\AdminStudentController::class, 'importStudents'])->name('import');
    });

    // Super Admin User Management (formerly Staff)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminStaffController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\AdminStaffController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\AdminStaffController::class, 'store'])->name('store');
        Route::get('/{user}', [\App\Http\Controllers\AdminStaffController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [\App\Http\Controllers\AdminStaffController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\AdminStaffController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\AdminStaffController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle', [\App\Http\Controllers\AdminStaffController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/activate', [\App\Http\Controllers\AdminStaffController::class, 'activate'])->name('activate');
        Route::post('/{user}/reset-password', [\App\Http\Controllers\AdminStaffController::class, 'resetPassword'])->name('reset-password');
    });

    // Super Admin Audit Logs
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdminAuditController::class, 'index'])->name('index');
        Route::get('/{log}', [\App\Http\Controllers\SuperAdminAuditController::class, 'show'])->name('show');
    });

    // Super Admin Global Settings
    Route::prefix('system-settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdminSettingsController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\SuperAdminSettingsController::class, 'update'])->name('update');
    });

    // Super Admin Bulk Operations
    Route::prefix('bulk-operations')->name('bulk.')->group(function () {
        Route::get('/', [\App\Http\Controllers\BulkOperationsController::class, 'index'])->name('index');
        Route::post('/promote', [\App\Http\Controllers\BulkOperationsController::class, 'promote'])->name('promote');
        Route::post('/archive', [\App\Http\Controllers\BulkOperationsController::class, 'archive'])->name('archive');
    });
});
