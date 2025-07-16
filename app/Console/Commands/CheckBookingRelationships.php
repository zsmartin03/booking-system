<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class CheckBookingRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:check-relationships {booking_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if a booking has all required relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookingId = $this->argument('booking_id');

        if ($bookingId) {
            $booking = Booking::find($bookingId);
            if (!$booking) {
                $this->error("Booking with ID {$bookingId} not found.");
                return 1;
            }
            $this->checkBooking($booking);
        } else {
            $bookings = Booking::latest()->take(5)->get();
            if ($bookings->isEmpty()) {
                $this->error("No bookings found in the database.");
                return 1;
            }

            foreach ($bookings as $booking) {
                $this->checkBooking($booking);
                $this->line('-----------------------------------------');
            }
        }

        return 0;
    }

    private function checkBooking(Booking $booking)
    {
        $this->info("Checking booking #{$booking->id}");
        $this->line("Status: {$booking->status}");

        if ($booking->client) {
            $this->info("✓ Client: {$booking->client->name} ({$booking->client->email})");
        } else {
            $this->error("✗ Client not found");
        }

        if ($booking->service) {
            $this->info("✓ Service: {$booking->service->name}");

            if ($booking->service->business) {
                $this->info("✓ Business: {$booking->service->business->name}");
                $this->info("✓ Email notifications: Always enabled");
            } else {
                $this->error("✗ Business not found for this service");
            }
        } else {
            $this->error("✗ Service not found");
        }

        if ($booking->employee) {
            $this->info("✓ Employee: {$booking->employee->name}");
        } else {
            $this->error("✗ Employee not found");
        }
    }
}
