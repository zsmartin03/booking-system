<?php

namespace App\Services;

use App\Mail\BookingCreated;
use App\Mail\BookingStatusUpdated;
use App\Models\Booking;
use App\Models\Notification as NotificationModel;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send notifications for a new booking.
     *
     * @param Booking $booking
     * @return void
     */
    public function sendBookingCreatedNotifications(Booking $booking)
    {
        if ($this->shouldSendEmail($booking)) {
            Mail::to($booking->client->email)
                ->send(new BookingCreated($booking));
        }

        $this->createNotification(
            $booking->client_id,
            $booking->id,
            'Booking Created',
            "Your booking for {$booking->service->name} on {$booking->start_time->format('M d, Y')} has been created."
        );

        $businessOwnerId = $booking->service->business->user_id;
        if ($businessOwnerId) {
            $this->createNotification(
                $businessOwnerId,
                $booking->id,
                'New Booking',
                "A new booking has been created for {$booking->service->name} on {$booking->start_time->format('M d, Y')}."
            );
        }

        if ($booking->employee->user_id) {
            $this->createNotification(
                $booking->employee->user_id,
                $booking->id,
                'New Booking Assigned',
                "You have been assigned a new booking for {$booking->service->name} on {$booking->start_time->format('M d, Y')}."
            );
        }
    }

    /**
     * Send notifications for a booking status update.
     *
     * @param Booking $booking
     * @param string $previousStatus
     * @return void
     */
    public function sendBookingStatusUpdatedNotifications(Booking $booking, string $previousStatus)
    {
        if ($this->shouldSendEmail($booking)) {
            Mail::to($booking->client->email)
                ->send(new BookingStatusUpdated($booking, $previousStatus));
        }

        $this->createNotification(
            $booking->client_id,
            $booking->id,
            'Booking Status Updated',
            "Your booking for {$booking->service->name} status has been updated from {$previousStatus} to {$booking->status}."
        );

        $businessOwnerId = $booking->service->business->user_id;
        if ($businessOwnerId) {
            $this->createNotification(
                $businessOwnerId,
                $booking->id,
                'Booking Status Changed',
                "A booking for {$booking->service->name} has been updated from {$previousStatus} to {$booking->status}."
            );
        }

        if ($booking->employee->user_id) {
            $this->createNotification(
                $booking->employee->user_id,
                $booking->id,
                'Booking Status Changed',
                "A booking assigned to you for {$booking->service->name} has been updated from {$previousStatus} to {$booking->status}."
            );
        }
    }

    /**
     * Create a notification record in the database.
     *
     * @param int $userId
     * @param int $bookingId
     * @param string $title
     * @param string $content
     * @return void
     */
    private function createNotification($userId, $bookingId, $title, $content)
    {
        NotificationModel::create([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'title' => $title,
            'content' => $content,
            'is_read' => false,
            'sent_at' => now(),
        ]);
    }

    /**
     * Determine if email notifications should be sent.
     *
     * @param Booking $booking
     * @return bool
     */
    private function shouldSendEmail(Booking $booking)
    {
        // For development, always send emails regardless of settings
        return true;

        $emailNotificationsEnabled = $booking->service->business->settings()
            ->where('key', 'notification_email')
            ->first()
            ?->value ?? false;

        return (bool) $emailNotificationsEnabled;
    }
}
