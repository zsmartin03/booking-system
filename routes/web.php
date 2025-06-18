<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BusinessWorkingHourController;
use App\Http\Controllers\EmployeeWorkingHourController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

if (App::environment('production')) {
    URL::forceScheme('https');
}

require __DIR__ . '/auth.php';

Route::middleware('guest')->group(function () {
    Route::view('/', 'welcome', ['businesses' => \App\Models\Business::all()])->name('home');
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

        // Business management
        Route::get('/manage/businesses', [BusinessController::class, 'index'])->name('businesses.index');
        Route::get('/manage/businesses/create', [BusinessController::class, 'create'])->name('businesses.create');
        Route::post('/manage/businesses', [BusinessController::class, 'store'])->name('businesses.store');
        Route::get('/manage/businesses/{id}/edit', [BusinessController::class, 'edit'])->name('businesses.edit');
        Route::put('/manage/businesses/{id}', [BusinessController::class, 'update'])->name('businesses.update');
        Route::delete('/manage/businesses/{id}', [BusinessController::class, 'destroy'])->name('businesses.destroy');

        Route::resource('business-working-hours', BusinessWorkingHourController::class)->except(['show']);

        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('employees', EmployeeController::class);

        Route::resource('employee-working-hours', EmployeeWorkingHourController::class)->except(['show']);
    });

    Route::middleware('role:client')->group(function () {
        Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    });

    Route::resource('bookings', BookingController::class)->except(['create', 'store']);
});

// Public Business View
Route::get('/businesses/{id}', [BusinessController::class, 'show'])->name('businesses.show');
Route::get('/businesses', [BusinessController::class, 'publicIndex'])->name('businesses.public.index');
