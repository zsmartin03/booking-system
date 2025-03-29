<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BookingController;

require __DIR__ . '/auth.php';

Route::middleware('guest')->group(function () {
    Route::view('/', 'welcome')->name('home');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    Route::middleware('role:provider,admin')->group(function () {
        Route::resource('businesses', BusinessController::class)->except(['show']);
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('employees', EmployeeController::class);

        Route::post('/business-hours', [BusinessController::class, 'updateHours'])->name('business.hours.update');
        Route::post('/employee-hours', [EmployeeController::class, 'updateHours'])->name('employee.hours.update');
    });

    Route::middleware('role:client')->group(function () {
        Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    });

    Route::resource('bookings', BookingController::class)->except(['create', 'store']);
});

// Public Business View
Route::get('/business/{id}', [BusinessController::class, 'show'])->name('business.show');
