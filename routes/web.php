<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:admin,provider'])->group(function () {
    Route::resource('businesses', BusinessController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('employees', EmployeeController::class);
});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__ . '/auth.php';
