<?php

use App\Helpers\RoleHelper;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DynamicPageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    if (Auth::check()) {
        $homeRoute = RoleHelper::getUserHomeRoute(Auth::user());

        if ($homeRoute) {
            if (str_starts_with($homeRoute, '/')) {
                return redirect()->to($homeRoute);
            }

            if (Route::has($homeRoute)) {
                return redirect()->route($homeRoute);
            }
        }

        return redirect()->route('login');
    }

    return redirect()->route('welcome');
});

Route::middleware(['auth'])->get('/page/{slug}', [DynamicPageController::class, 'show']);

Route::middleware(['auth'])->get('/home', function () {
    $homeRoute = RoleHelper::getUserHomeRoute(Auth::user());

    if ($homeRoute) {
        if (str_starts_with($homeRoute, '/')) {
            return redirect()->to($homeRoute);
        }

        if (Route::has($homeRoute)) {
            return redirect()->route($homeRoute);
        }
    }

    return redirect()->route('login');
});
