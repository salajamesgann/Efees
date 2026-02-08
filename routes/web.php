<?php

use App\Http\Controllers\AdminFeeController;
use App\Http\Controllers\AdminParentController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AuthLoginController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentSettingsController;
use App\Http\Controllers\AdminStaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthLoginController::class, 'login'])->name('login');
Route::post('/authenticate', [AuthLoginController::class, 'authenticate'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, \App\Http\Middleware\VerifyCsrfToken::class])
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

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [AuthLoginController::class, 'changePassword'])->name('auth.password.change');
    Route::post('/change-password', [AuthLoginController::class, 'updatePassword'])->name('auth.password.update');
    Route::get('/user_dashboard', [AuthLoginController::class, 'user_dashboard'])->name('user_dashboard');
    Route::get('/student/fees/summary', [AuthLoginController::class, 'user_fee_summary'])->name('student.fees.summary');

    Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [AuthLoginController::class, 'user_dashboard'])->middleware('ensureRole:parent')->name('dashboard');
        Route::get('/metrics', [AuthLoginController::class, 'parent_metrics'])->middleware('ensureRole:parent')->name('metrics');
        Route::post('/link-student', [AuthLoginController::class, 'linkStudent'])->middleware('ensureRole:parent')->name('link_student');
        Route::post('/unlink-student', [AuthLoginController::class, 'unlinkStudent'])->middleware('ensureRole:parent')->name('unlink_student');
        Route::get('/receipt/{payment}', [\App\Http\Controllers\ParentPaymentController::class, 'showReceipt'])->middleware('ensureRole:parent')->name('receipts.download');
        Route::get('/history', [\App\Http\Controllers\ParentPaymentController::class, 'history'])->middleware('ensureRole:parent')->name('history');
        Route::get('/soa/{student}', [\App\Http\Controllers\ParentFeesController::class, 'soa'])->middleware('ensureRole:parent')->name('soa');
        Route::get('/pay', [\App\Http\Controllers\ParentPaymentController::class, 'show'])->middleware('ensureRole:parent')->name('pay');
        Route::post('/pay', [\App\Http\Controllers\ParentPaymentController::class, 'store'])->middleware('ensureRole:parent')->name('pay.store');
        Route::get('/pay/success', [\App\Http\Controllers\ParentPaymentController::class, 'success'])->middleware('ensureRole:parent')->name('pay.success');
        Route::get('/pay/cancel', [\App\Http\Controllers\ParentPaymentController::class, 'cancel'])->middleware('ensureRole:parent')->name('pay.cancel');
        Route::get('/student/{student}/fees', [\App\Http\Controllers\ParentFeesController::class, 'show'])->middleware('ensureRole:parent')->name('fees.show');

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

        // Student Details
        Route::get('/student-details/{student}', [\App\Http\Controllers\StaffStudentDetailsController::class, 'show'])->middleware('ensureRole:staff')->name('student_details');
        Route::post('/student-details/{student}/update-category', [\App\Http\Controllers\StaffStudentDetailsController::class, 'updateCategory'])->middleware('ensureRole:staff')->name('student_details.update_category');

        // Fee Records (Editing)
        Route::post('/fee-records/{record}', [\App\Http\Controllers\StaffRecordsController::class, 'update'])->middleware('ensureRole:staff')->name('fee_records.update');
        Route::post('/fee-records', [\App\Http\Controllers\StaffRecordsController::class, 'store'])->middleware('ensureRole:staff')->name('fee_records.store');
    });

    // Admin Audit Logs
    Route::prefix('admin/audit-logs')->name('admin.audit-logs.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminAuditLogController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\AdminAuditLogController::class, 'export'])->name('export');
    });

    // Admin SMS delivery status callback (Twilio)
    Route::post('/webhooks/sms/twilio', [\App\Http\Controllers\AdminSmsController::class, 'twilioCallback'])->name('webhooks.sms.twilio');

    // Admin Manage Students
    Route::prefix('admin/students')->name('admin.students.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/export', [AdminStudentController::class, 'exportMasterList'])->name('export');
        Route::get('/search', [AdminStudentController::class, 'search'])->name('search');
        Route::get('/sections', [AdminStudentController::class, 'sectionsList'])->name('sections.list');
        Route::get('/', [AdminStudentController::class, 'index'])->name('index');
        Route::get('/create', [AdminStudentController::class, 'create'])->name('create');
        Route::post('/', [AdminStudentController::class, 'store'])->name('store');
        Route::post('/section', [AdminStudentController::class, 'storeSection'])->name('storeSection');
        Route::post('/strand', [AdminStudentController::class, 'storeStrand'])->name('storeStrand');
        Route::delete('/sections/{section}', [AdminStudentController::class, 'destroySection'])->name('sections.destroy');
        Route::get('/{student}/edit', [AdminStudentController::class, 'edit'])->name('edit');
        Route::put('/{student}', [AdminStudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [AdminStudentController::class, 'destroy'])->name('destroy');
        Route::patch('/{student}/archive', [AdminStudentController::class, 'archive'])->name('archive');
        Route::patch('/{student}/unarchive', [AdminStudentController::class, 'unarchive'])->name('unarchive');
    });

    // Admin Parent Management - DEPRECATED (Moved to Unified User Management)
    // Route::prefix('admin/parents')->name('admin.parents.')->middleware('ensureRole:admin')->group(function () {
    //     Route::get('/', [AdminParentController::class, 'index'])->name('index');
    //     Route::get('/create', [AdminParentController::class, 'create'])->name('create');
    //     Route::post('/', [AdminParentController::class, 'store'])->name('store');
    //     Route::get('/{parent}/edit', [AdminParentController::class, 'edit'])->name('edit');
    //     Route::put('/{parent}', [AdminParentController::class, 'update'])->name('update');
    //     Route::delete('/{parent}', [AdminParentController::class, 'destroy'])->name('destroy');
    //     Route::patch('/{parent}/archive', [AdminParentController::class, 'archive'])->name('archive');
    //     Route::patch('/{parent}/unarchive', [AdminParentController::class, 'unarchive'])->name('unarchive');
    //     Route::post('/{parent}/link', [AdminParentController::class, 'link'])->name('link');
    //     Route::post('/{parent}/unlink', [AdminParentController::class, 'unlink'])->name('unlink');
    //     Route::patch('/{parent}/toggle-status', [AdminParentController::class, 'toggleStatus'])->name('toggle-status');
    //     Route::post('/{parent}/reset-password', [AdminParentController::class, 'resetPassword'])->name('reset-password');
    // });

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

    // Admin Fee Periods
    Route::prefix('admin/fees/periods')->name('admin.fees.periods.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminFeePeriodController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\AdminFeePeriodController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\AdminFeePeriodController::class, 'store'])->name('store');
        Route::get('/{period}/edit', [\App\Http\Controllers\AdminFeePeriodController::class, 'edit'])->name('edit');
        Route::put('/{period}', [\App\Http\Controllers\AdminFeePeriodController::class, 'update'])->name('update');
        Route::delete('/{period}', [\App\Http\Controllers\AdminFeePeriodController::class, 'destroy'])->name('destroy');
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

        // Fee Assignments
        Route::post('/assignments/generate', [AdminFeeController::class, 'generateFeeAssignments'])->name('assignments.generate');
        Route::post('/assignments/{feeAssignment}/recalculate', [AdminFeeController::class, 'recalculateStudentFees'])->name('assignments.recalculate');
    });

    // Admin User Management (formerly Staff)
    Route::prefix('admin/staff')->name('admin.staff.')->middleware('ensureRole:admin')->group(function () {
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

    // Admin User Management
    Route::prefix('admin/users')->name('admin.users.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\AdminUserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('destroy');
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

    // Admin Reports
    Route::prefix('admin/reports')->name('admin.reports.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminReportsController::class, 'index'])->name('index');
        Route::get('/metrics', [\App\Http\Controllers\AdminReportsController::class, 'metrics'])->name('metrics');
        Route::post('/export/csv', [\App\Http\Controllers\AdminReportsController::class, 'exportCsv'])->name('export.csv');
        Route::post('/schedule', [\App\Http\Controllers\AdminReportsController::class, 'schedule'])->name('schedule');
        Route::delete('/schedule/{id}', [\App\Http\Controllers\AdminReportsController::class, 'destroy'])->name('destroy');
        Route::get('/download/{id}', [\App\Http\Controllers\AdminReportsController::class, 'downloadReport'])->name('download');
    });

    // Admin Settings
    Route::prefix('admin/settings')->name('admin.settings.')->middleware('ensureRole:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\AdminSettingsController::class, 'update'])->name('update');
        Route::post('/reset-demo', [\App\Http\Controllers\AdminSettingsController::class, 'resetDemoData'])->name('reset-demo');
        Route::post('/clear-cache', [\App\Http\Controllers\AdminSettingsController::class, 'clearCache'])->name('clear-cache');
        Route::post('/reset-database', [\App\Http\Controllers\AdminSettingsController::class, 'resetDatabase'])->name('reset-database');
        Route::post('/export-db', [\App\Http\Controllers\AdminSettingsController::class, 'exportDatabase'])->name('export-db');
    });

    // Student Profile Routes
    Route::middleware(['auth', 'ensureRole:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile');
        Route::get('/settings', [StudentSettingsController::class, 'index'])->name('settings');
        Route::post('/settings/password', [StudentSettingsController::class, 'updatePassword'])->name('settings.password');
    });
});
