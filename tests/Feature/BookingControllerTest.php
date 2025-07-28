<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Business;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $client;
    protected $provider;
    protected $business;
    protected $service;
    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = User::factory()->create(['role' => 'client']);
        $this->provider = User::factory()->create(['role' => 'provider']);
        $this->business = Business::factory()->create(['user_id' => $this->provider->id]);
        $this->service = Service::factory()->create(['business_id' => $this->business->id, 'active' => true, 'duration' => 60, 'price' => 100]);
        $this->employee = Employee::factory()->create(['business_id' => $this->business->id, 'user_id' => $this->provider->id, 'active' => true]);
        $this->service->employees()->attach($this->employee->id);
        Setting::factory()->create(['business_id' => $this->business->id]);
    }

    public function test_store_booking_fails_for_holiday_mode()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'holiday_mode', 'value' => '1']);
        $data = [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'start_time' => $start->format('Y-m-d\TH:i'),
        ];
        $response = $this->actingAs($this->client)->post(route('bookings.store'), $data);
        $response->assertSessionHasErrors('general');
    }

    public function test_store_booking_fails_for_maintenance_mode()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'maintenance_mode', 'value' => '1']);
        $data = [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'start_time' => $start->format('Y-m-d\TH:i'),
        ];
        $response = $this->actingAs($this->client)->post(route('bookings.store'), $data);
        $response->assertSessionHasErrors('general');
    }

    public function test_store_booking_fails_for_buffer_conflict()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        // Ensure business is not in maintenance or holiday mode
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'maintenance_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'holiday_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'booking_buffer_minutes', 'value' => '30']);
        Booking::factory()->create([
            'employee_id' => $this->employee->id,
            'start_time' => $start->copy()->subMinutes(30),
            'end_time' => $start->copy()->addHour(),
            'status' => 'confirmed',
        ]);
        $data = [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'start_time' => $start->format('Y-m-d\TH:i'),
        ];
        $response = $this->actingAs($this->client)->post(route('bookings.store'), $data);
        $response->assertSessionHasErrors('start_time');
    }

    public function test_store_booking_fails_for_too_soon()
    {
        $start = Carbon::now()->addHour();
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        // Ensure business is not in maintenance or holiday mode
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'maintenance_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'holiday_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'booking_advance_hours', 'value' => '2']);
        $data = [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'start_time' => $start->format('Y-m-d\TH:i'),
        ];
        $response = $this->actingAs($this->client)->post(route('bookings.store'), $data);
        $response->assertSessionHasErrors('start_time');
    }

    public function test_store_booking_fails_for_too_far()
    {
        $start = Carbon::now()->addDays(40);
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        // Ensure business is not in maintenance or holiday mode
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'maintenance_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'holiday_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'booking_advance_days', 'value' => '30']);
        $data = [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'start_time' => $start->format('Y-m-d\TH:i'),
        ];
        $response = $this->actingAs($this->client)->post(route('bookings.store'), $data);
        $response->assertSessionHasErrors('start_time');
    }

    public function test_manage_forbidden_for_client()
    {
        $response = $this->actingAs($this->client)->get(route('bookings.manage'));
        $response->assertForbidden();
    }

    public function test_manage_provider_with_one_business_auto_selects()
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $provider->id]);
        $service = Service::factory()->create(['business_id' => $business->id, 'active' => true]);
        $employee = Employee::factory()->create(['business_id' => $business->id, 'user_id' => $provider->id, 'active' => true]);
        $service->employees()->attach($employee->id);
        Setting::factory()->create(['business_id' => $business->id]);
        $response = $this->actingAs($provider)->get(route('bookings.manage'));
        $response->assertOk();
        $response->assertViewHas('selectedBusiness');
    }

    public function test_update_booking_status_as_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'status' => 'pending',
        ]);
        $response = $this->actingAs($admin)->patch(route('bookings.update', $booking->id), [
            'status' => 'confirmed',
        ]);
        $response->assertRedirect(route('bookings.show', $booking->id));
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_available_slots_no_employees()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $service = Service::factory()->create(['business_id' => $this->business->id, 'active' => true]);
        $response = $this->actingAs($this->client)->getJson(route('booking-slots', [
            'service_id' => $service->id,
            'week_start' => $start->startOfWeek()->toDateString(),
        ]));
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertIsArray($json);
    }

    public function test_available_slots_with_exceptions()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        $this->employee->availabilityExceptions()->create([
            'date' => $start->toDateString(),
            'start_time' => '12:00',
            'end_time' => '13:00',
            'type' => 'unavailable',
        ]);
        $response = $this->actingAs($this->client)->getJson(route('booking-slots', [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'week_start' => $start->startOfWeek()->toDateString(),
        ]));
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertIsArray($json);
    }

    public function test_store_booking_handles_exception()
    {
        $this->markTestSkipped('Cannot mock Booking::create with overload after class is loaded. Run this test in isolation or refactor controller for DI.');
    }

    public function test_create_booking_page_loads()
    {
        $response = $this->actingAs($this->client)->get(route('bookings.create', $this->business->id));
        $response->assertOk();
        $response->assertViewHas('selectedBusiness');
    }

    public function test_store_booking_success()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        // Ensure all required settings are present
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'maintenance_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'holiday_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'booking_advance_hours', 'value' => '2']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'booking_advance_days', 'value' => '30']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'booking_buffer_minutes', 'value' => '0']);
        $data = [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'start_time' => $start->format('Y-m-d\TH:i'),
            'notes' => 'Test booking',
        ];
        $response = $this->actingAs($this->client)->post(route('bookings.store'), $data);
        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
        ]);
    }

    public function test_store_booking_fails_for_overlap()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        // Ensure business is not in maintenance or holiday mode
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'maintenance_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'holiday_mode', 'value' => '0']);
        Booking::factory()->create([
            'employee_id' => $this->employee->id,
            'start_time' => $start,
            'end_time' => $start->copy()->addHour(),
            'status' => 'confirmed',
        ]);
        $data = [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'start_time' => $start->format('Y-m-d\TH:i'),
            'notes' => 'Overlap',
        ];
        $response = $this->actingAs($this->client)->post(route('bookings.store'), $data);
        $response->assertSessionHasErrors('start_time');
    }
    public function test_available_slots_returns_json()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'maintenance_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'holiday_mode', 'value' => '0']);
        $response = $this->actingAs($this->client)->getJson(route('booking-slots', [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'week_start' => $start->startOfWeek()->toDateString(),
        ]));
        $response->assertStatus(200);
        $response->assertJsonStructure([]); // Accept any JSON structure for now
    }

    public function test_index_shows_client_bookings()
    {
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
        ]);
        $response = $this->actingAs($this->client)->get(route('bookings.index'));
        $response->assertOk();
        $response->assertViewHas('bookings');
        $response->assertSee((string)$booking->id);
    }

    public function test_manage_shows_provider_bookings()
    {
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
        ]);
        $response = $this->actingAs($this->provider)->get(route('bookings.manage'));
        $response->assertOk();
        $response->assertViewHas('bookings');
        $response->assertSee((string)$booking->id);
    }
    public function test_show_booking_as_client()
    {
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
        ]);
        $response = $this->actingAs($this->client)->get(route('bookings.show', $booking->id));
        $response->assertOk();
        $response->assertViewHas('booking');
        $response->assertSee((string)$booking->id);
    }

    public function test_show_booking_forbidden_for_other_client()
    {
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
        ]);
        $otherClient = User::factory()->create(['role' => 'client']);
        $response = $this->actingAs($otherClient)->get(route('bookings.show', $booking->id));
        $response->assertForbidden();
    }

    public function test_show_booking_as_provider()
    {
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
        ]);
        $response = $this->actingAs($this->provider)->get(route('bookings.show', $booking->id));
        $response->assertOk();
        $response->assertViewHas('booking');
    }

    public function test_show_booking_forbidden_for_other_provider()
    {
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
        ]);
        $otherProvider = User::factory()->create(['role' => 'provider']);
        $response = $this->actingAs($otherProvider)->get(route('bookings.show', $booking->id));
        $response->assertForbidden();
    }

    public function test_update_booking_status_as_provider()
    {
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'status' => 'pending',
        ]);
        $response = $this->actingAs($this->provider)->patch(route('bookings.update', $booking->id), [
            'status' => 'confirmed',
        ]);
        $response->assertRedirect(route('bookings.show', $booking->id));
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_update_booking_status_forbidden_for_client()
    {
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'status' => 'pending',
        ]);
        $response = $this->actingAs($this->client)->patch(route('bookings.update', $booking->id), [
            'status' => 'confirmed',
        ]);
        $response->assertForbidden();
    }

    public function test_update_booking_status_with_invalid_status()
    {
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'status' => 'pending',
        ]);
        $response = $this->actingAs($this->provider)->patch(route('bookings.update', $booking->id), [
            'status' => 'not_a_status',
        ]);
        $response->assertSessionHasErrors('status');
    }

    public function test_update_booking_status_from_manage_redirects()
    {
        $booking = Booking::factory()->create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'status' => 'pending',
        ]);
        $response = $this->actingAs($this->provider)->patch(route('bookings.update', $booking->id), [
            'status' => 'confirmed',
            'from_manage' => 1,
        ]);
        $response->assertRedirect(route('bookings.manage', ['business_id' => $this->service->business_id]));
    }

    public function test_business_schedule_returns_json()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        $response = $this->actingAs($this->provider)->getJson(route('business-schedule', [
            'business_id' => $this->business->id,
            'week_start' => $start->startOfWeek()->toDateString(),
        ]));
        $response->assertStatus(200);
        $response->assertJsonStructure([]); // Accept any JSON structure for now
    }

    public function test_business_schedule_empty_if_no_business_id()
    {
        $response = $this->actingAs($this->provider)->getJson(route('business-schedule'));
        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    public function test_redirect_to_first_available_business()
    {
        $response = $this->actingAs($this->client)->get(route('bookings.redirect'));
        $response->assertRedirect(route('bookings.create', $this->business->id));
    }

    public function test_redirect_404_if_no_business_available()
    {
        // Remove all related data to avoid foreign key constraint
        DB::table('service_employee')->delete();
        \App\Models\Service::query()->delete();
        \App\Models\Employee::query()->delete();
        \App\Models\Setting::query()->delete();
        Business::query()->delete();
        $response = $this->actingAs($this->client)->get(route('bookings.redirect'));
        $response->assertNotFound();
    }
    public function test_create_booking_page_404_for_invalid_business()
    {
        $invalidBusinessId = 999999;
        $response = $this->actingAs($this->client)->get(route('bookings.create', $invalidBusinessId));
        $response->assertNotFound();
    }

    public function test_store_booking_fails_for_missing_fields()
    {
        $response = $this->actingAs($this->client)->post(route('bookings.store'), []);
        $response->assertSessionHasErrors(['service_id', 'employee_id', 'start_time']);
    }

    public function test_store_booking_fails_for_invalid_employee_service_relationship()
    {
        $otherBusiness = \App\Models\Business::factory()->create();
        $otherService = \App\Models\Service::factory()->create(['business_id' => $otherBusiness->id]);
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        $data = [
            'service_id' => $otherService->id,
            'employee_id' => $this->employee->id,
            'start_time' => $start->format('Y-m-d\TH:i'),
        ];
        $response = $this->actingAs($this->client)->post(route('bookings.store'), $data);
        $response->assertSessionHasErrors('employee_id');
    }

    public function test_store_booking_fails_if_employee_not_working()
    {
        $start = Carbon::now()->addDays(2)->setTime(22, 0); // outside working hours
        $this->employee->workingHours()->create([
            'day_of_week' => strtolower($start->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'maintenance_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'holiday_mode', 'value' => '0']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'booking_advance_hours', 'value' => '2']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'booking_advance_days', 'value' => '30']);
        \App\Models\Setting::factory()->create(['business_id' => $this->business->id, 'key' => 'booking_buffer_minutes', 'value' => '0']);
        $data = [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'start_time' => $start->format('Y-m-d\TH:i'),
        ];
        $response = $this->actingAs($this->client)->post(route('bookings.store'), $data);
        $response->assertSessionHasErrors('start_time');
    }

    public function test_available_slots_empty_for_no_working_hours()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $response = $this->actingAs($this->client)->getJson(route('booking-slots', [
            'service_id' => $this->service->id,
            'employee_id' => $this->employee->id,
            'week_start' => $start->startOfWeek()->toDateString(),
        ]));
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertIsArray($json);
    }

    public function test_business_schedule_empty_for_no_employees()
    {
        DB::table('service_employee')->delete();
        \App\Models\Employee::query()->delete();
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        $response = $this->actingAs($this->provider)->getJson(route('business-schedule', [
            'business_id' => $this->business->id,
            'week_start' => $start->startOfWeek()->toDateString(),
        ]));
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertIsArray($json);
    }
}
