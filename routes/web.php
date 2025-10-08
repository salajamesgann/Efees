<?php

use App\Http\Controllers\AuthLoginController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentSettingsController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminStaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('welcome'); });

// Authentication Routes
Route::get('/login', [AuthLoginController::class, 'login'])->name('login');
Route::post('/authenticate', [AuthLoginController::class, 'authenticate'])->name('authenticate');

Route::get('/signup', [AuthLoginController::class, 'signup'])->name('signup');
Route::post('/register', [AuthLoginController::class, 'register'])->name('register');

Route::post('/logout', [AuthLoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/user_dashboard', [AuthLoginController::class, 'user_dashboard'])->name('user_dashboard');
    Route::get('/admin_dashboard', [AuthLoginController::class, 'admin_dashboard'])->name('admin_dashboard');
    // Staff Dashboard and actions
    Route::get('/staff_dashboard', [StaffDashboardController::class, 'index'])->name('staff_dashboard');
    Route::post('/staff/remind/{student}', [StaffDashboardController::class, 'remind'])->name('staff.remind');
    Route::post('/staff/approve/{student}', [StaffDashboardController::class, 'approve'])->name('staff.approve');


    // Student Profile Management
    Route::get('/student/profile', [StudentProfileController::class, 'show'])->name('student.profile.show');
    Route::get('/student/settings', [StudentProfileController::class, 'settings'])->name('student.settings');
    Route::post('/student/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');

    // Admin Manage Students
    Route::get('/admin/students', [AdminStudentController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/create', [AdminStudentController::class, 'create'])->name('admin.students.create');
    Route::post('/admin/students', [AdminStudentController::class, 'store'])->name('admin.students.store');
    Route::get('/admin/students/{student}/edit', [AdminStudentController::class, 'edit'])->name('admin.students.edit');
    Route::put('/admin/students/{student}', [AdminStudentController::class, 'update'])->name('admin.students.update');
    Route::delete('/admin/students/{student}', [AdminStudentController::class, 'destroy'])->name('admin.students.destroy');

    // Admin Staff Account Management
    Route::prefix('admin/staff')->name('admin.staff.')->group(function () {
        Route::get('/', [AdminStaffController::class, 'index'])->name('index');
        Route::get('/create', [AdminStaffController::class, 'create'])->name('create');
        Route::post('/', [AdminStaffController::class, 'store'])->name('store');
        Route::get('/{staff}', [AdminStaffController::class, 'show'])->name('show');
        Route::get('/{staff}/edit', [AdminStaffController::class, 'edit'])->name('edit');
        Route::put('/{staff}', [AdminStaffController::class, 'update'])->name('update');
        Route::patch('/{staff}/toggle-status', [AdminStaffController::class, 'toggleStatus'])->name('toggle-status');
    });
});
