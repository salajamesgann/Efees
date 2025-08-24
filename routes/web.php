<?php

use App\Http\Controllers\AuthLoginController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('welcome'); });

Route::get('/login', [AuthLoginController::class, 'login'])->name('login');

Route::get('/signup', [AuthLoginController::class, 'signup'])->name('signup');

Route::get('/user_dashboard', [AuthLoginController::class, 'user_dashboard'])->name('signup');


