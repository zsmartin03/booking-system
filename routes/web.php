<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatisticsController;
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
    Route::get('/', function () {
        return view('welcome', [
            'businesses' => \App\Models\Business::with('categories')
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->orderBy('reviews_count', 'desc')
                ->take(9)
                ->get()
        ]);
    })->name('home');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile/remove-avatar', [ProfileController::class, 'removeAvatar'])->name('profile.remove-avatar');

    Route::middleware('role:provider,admin')->group(function () {

        // Business management
        Route::get('/manage/businesses', [BusinessController::class, 'index'])->name('businesses.index');
        Route::get('/manage/businesses/create', [BusinessController::class, 'create'])->name('businesses.create');
        Route::post('/manage/businesses', [BusinessController::class, 'store'])->name('businesses.store');
        Route::get('/manage/businesses/{id}/edit', [BusinessController::class, 'edit'])->name('businesses.edit');
        Route::put('/manage/businesses/{id}', [BusinessController::class, 'update'])->name('businesses.update');
        Route::delete('/manage/businesses/{id}', [BusinessController::class, 'destroy'])->name('businesses.destroy');
        Route::delete('/manage/businesses/{id}/remove-logo', [BusinessController::class, 'removeLogo'])->name('businesses.remove-logo');

        Route::resource('business-working-hours', BusinessWorkingHourController::class)->except(['show']);
        Route::post('business-working-hours/bulk-update', [BusinessWorkingHourController::class, 'bulkUpdate'])->name('business-working-hours.bulk-update');

        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('employees', EmployeeController::class);

        Route::resource('employee-working-hours', EmployeeWorkingHourController::class)->except(['show']);
        Route::post('employee-working-hours/bulk-update', [EmployeeWorkingHourController::class, 'bulkUpdate'])->name('employee-working-hours.bulk-update');
        Route::resource('availability-exceptions', AvailabilityExceptionController::class)->except(['show']);

        // Settings management
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/reset', [SettingController::class, 'reset'])->name('settings.reset');

        // Statistics routes
        Route::get('/statistics', [StatisticsController::class, 'redirect'])->name('statistics.redirect');
        Route::get('/statistics/{business}', [StatisticsController::class, 'index'])->name('statistics.index');
        Route::get('/statistics/{business}/data', [StatisticsController::class, 'getData'])->name('statistics.data');
    });

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    });

    Route::get('/bookings/create', [BookingController::class, 'redirect'])->name('bookings.redirect');
    Route::get('/businesses/{business}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/manage', [BookingController::class, 'manage'])->name('bookings.manage');

    Route::resource('bookings', BookingController::class)->except(['create', 'store']);

    // Review routes
    Route::post('/businesses/{business}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/vote', [\App\Http\Controllers\ReviewController::class, 'vote'])->name('reviews.vote');
    Route::post('/reviews/{review}/respond', [\App\Http\Controllers\ReviewController::class, 'respond'])->name('reviews.respond');
    Route::put('/review-responses/{reviewResponse}', [\App\Http\Controllers\ReviewController::class, 'updateResponse'])->name('review-responses.update');
    Route::delete('/review-responses/{reviewResponse}', [\App\Http\Controllers\ReviewController::class, 'destroyResponse'])->name('review-responses.destroy');
});

// Public Business View
Route::get('/businesses/{id}', [BusinessController::class, 'show'])->name('businesses.show');
Route::get('/businesses', [BusinessController::class, 'publicIndex'])->name('businesses.public.index');

Route::get('/booking-slots', [\App\Http\Controllers\BookingController::class, 'availableSlots'])
    ->name('booking-slots')
    ->middleware(['auth', 'verified']);

Route::get('/business-schedule', [\App\Http\Controllers\BookingController::class, 'businessSchedule'])
    ->name('business-schedule')
    ->middleware(['auth', 'verified']);
