<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Contoh route dashboard per role (bisa disesuaikan)
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index']);
});
