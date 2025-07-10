<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Cache;

class BookingObserver
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * Create a new observer instance.
     *
     * @param NotificationService $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Booking "created" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function created(Booking $booking)
    {
        $this->notificationService->sendBookingCreatedNotifications($booking);
    }

    /**
     * Handle the Booking "updating" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function updating(Booking $booking)
    {
        // Store the original status in the cache with a unique key for this booking
        $previousStatus = $booking->getOriginal('status');
        $cacheKey = "booking_{$booking->id}_previous_status";
        Cache::put($cacheKey, $previousStatus, now()->addMinutes(5));
    }

    /**
     * Handle the Booking "updated" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function updated(Booking $booking)
    {
        // Retrieve the previous status from cache
        $cacheKey = "booking_{$booking->id}_previous_status";
        $previousStatus = Cache::get($cacheKey);

        // Only send notification if status has changed
        if ($previousStatus !== null && $previousStatus !== $booking->status) {
            $this->notificationService->sendBookingStatusUpdatedNotifications($booking, $previousStatus);
        }

        Cache::forget($cacheKey);
    }
}
