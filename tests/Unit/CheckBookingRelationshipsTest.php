<?php

namespace Tests\Unit;

use App\Console\Commands\CheckBookingRelationships;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CheckBookingRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_with_no_bookings_outputs_error()
    {
        $this->artisan('booking:check-relationships')
            ->expectsOutput('No bookings found in the database.')
            ->assertExitCode(1);
    }
}
