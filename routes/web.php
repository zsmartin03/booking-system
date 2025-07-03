<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BusinessWorkingHourController;
use App\Http\Controllers\EmployeeWorkingHourController;
use App\Http\Controllers\AvailabilityExceptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

if (App::environment('production')) {
    URL::forceScheme('https');
}

require __DIR__ . '/auth.php';

// Language switching route
Route::post('/locale', function (Request $request) {
    $supportedLocales = ['en', 'hu'];
    $locale = $request->input('locale');

    if (in_array($locale, $supportedLocales)) {
        session(['locale' => $locale]);
    }

    return response()->json(['success' => true, 'locale' => session('locale')]);
})->name('locale.switch');

// Route to set locale from localStorage on initial page load
Route::get('/locale/init/{locale}', function ($locale) {
    $supportedLocales = ['en', 'hu'];

    if (in_array($locale, $supportedLocales)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.init');

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
        Route::resource('availability-exceptions', AvailabilityExceptionController::class)->except(['show']);

        // Settings management
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/reset', [SettingController::class, 'reset'])->name('settings.reset');
    });

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    });

    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/manage', [BookingController::class, 'manage'])->name('bookings.manage');

    Route::resource('bookings', BookingController::class)->except(['create', 'store']);
});

// Public Business View
Route::get('/businesses/{id}', [BusinessController::class, 'show'])->name('businesses.show');
Route::get('/businesses', [BusinessController::class, 'publicIndex'])->name('businesses.public.index');

Route::get('/booking-slots', [\App\Http\Controllers\BookingController::class, 'availableSlots'])
    ->name('booking-slots')
    ->middleware(['auth', 'verified']);

Route::get('/debug/booking-slots', function (Request $request) {
    $service = \App\Models\Service::findOrFail($request->service_id);
    $employees = $service->employees()->where('active', true)->get();

    $debug = [
        'service' => $service->toArray(),
        'employees_count' => $employees->count(),
        'employees' => $employees->map(function ($emp) {
            return [
                'id' => $emp->id,
                'name' => $emp->name,
                'working_hours_count' => $emp->workingHours()->count(),
                'working_hours' => $emp->workingHours()->get()->toArray()
            ];
        })
    ];

    return response()->json($debug);
});
