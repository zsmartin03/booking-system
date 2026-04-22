<?php

namespace Tests\Feature;

use App\Models\AvailabilityException;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Employee;
use App\Models\EmployeeWorkingHour;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_nulls_selected_service_if_it_belongs_to_another_business(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);

        $businessA = Business::factory()->create();
        $businessB = Business::factory()->create();

        $serviceOnB = Service::factory()->create(['business_id' => $businessB->id, 'active' => true]);
        Service::factory()->create(['business_id' => $businessA->id, 'active' => true]);

        $response = $this->actingAs($user)->get(route('bookings.create', [
            'business' => $businessA->id,
            'service_id' => $serviceOnB->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('selectedService', null);
    }

    public function test_store_rejects_booking_when_buffer_conflicts_with_existing_booking(): void
    {
        $client = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);

        $business = Business::factory()->create();
        Setting::setValue($business->id, 'holiday_mode', false);
        Setting::setValue($business->id, 'maintenance_mode', false);
        Setting::setValue($business->id, 'booking_advance_hours', 0);
        Setting::setValue($business->id, 'booking_advance_days', 365);
        Setting::setValue($business->id, 'booking_buffer_minutes', 10);

        $service = Service::factory()->create([
            'business_id' => $business->id,
            'duration' => 30,
            'price' => 50,
            'active' => true,
        ]);
        $employee = Employee::factory()->create([
            'business_id' => $business->id,
            'active' => true,
        ]);
        $employee->services()->sync([$service->id]);

        $start = now()->addDay()->setTime(10, 0, 0);
        $end = $start->copy()->addMinutes(30);

        EmployeeWorkingHour::factory()->create([
            'employee_id' => $employee->id,
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ]);

        Booking::factory()->create([
            'client_id' => $client->id,
            'service_id' => $service->id,
            'employee_id' => $employee->id,
            'start_time' => $start->copy()->subMinutes(15), // 09:45
            'end_time' => $start->copy()->subMinutes(5),    // 09:55
            'status' => 'confirmed',
            'total_price' => 50,
        ]);

        $response = $this->actingAs($client)->post(route('bookings.store'), [
            'service_id' => $service->id,
            'employee_id' => $employee->id,
            'start_time' => $start->format('Y-m-d\TH:i'),
            'notes' => 'test',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['start_time']);
    }

    public function test_available_slots_rounds_start_time_to_next_hour_when_ceil_hits_60_and_skips_unavailable_exception(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);

        $business = Business::factory()->create();
        $service = Service::factory()->create([
            'business_id' => $business->id,
            'duration' => 10,
            'active' => true,
        ]);
        $employee = Employee::factory()->create([
            'business_id' => $business->id,
            'active' => true,
            'name' => 'Emp 1',
        ]);
        $employee->services()->sync([$service->id]);

        // Pick a week start on a Monday so we know day 0. Use a fixed date to avoid timezone surprises.
        $weekStart = now()->startOfWeek(); // Monday
        $date = $weekStart->copy();        // offset 0

        EmployeeWorkingHour::factory()->create([
            'employee_id' => $employee->id,
            'day_of_week' => strtolower($date->format('l')),
            // 09:58 rounded up by 5-minute interval -> 10:00 (ceil to 60 triggers addHour)
            'start_time' => '09:58:00',
            'end_time' => '10:10:00',
        ]);

        // Make 10:00 unavailable so the first possible slot is not at 10:00 (it should be skipped)
        AvailabilityException::factory()->create([
            'employee_id' => $employee->id,
            'date' => $date->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '10:05:00',
            'type' => 'unavailable',
        ]);

        $response = $this->actingAs($user)->getJson(route('booking-slots', [
            'service_id' => $service->id,
            'employee_id' => $employee->id,
        ]));

        $response->assertOk();
        $slots = $response->json();

        $this->assertArrayHasKey($date->toDateString(), $slots);
        $times = array_map(fn ($s) => $s['time'], $slots[$date->toDateString()]);

        $this->assertNotContains($date->format('Y-m-d') . 'T10:00', $times);
    }

    public function test_redirect_404_when_no_business_has_active_services_and_redirects_when_one_exists(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);

        $this->actingAs($user)->get(route('bookings.redirect'))->assertNotFound();

        $business = Business::factory()->create();
        Service::factory()->create(['business_id' => $business->id, 'active' => true]);

        $this->actingAs($user)
            ->get(route('bookings.redirect'))
            ->assertRedirect(route('bookings.create', $business->id));
    }

    // Note: BookingController::store() has a narrow try/catch around the actual create.
    // We intentionally avoid inducing DB-level failures in feature tests, since failures
    // earlier in the method (overlap/buffer queries) would bypass that catch.
}

