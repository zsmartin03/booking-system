<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\AvailabilityException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityExceptionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $provider;
    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->provider = User::factory()->create(['role' => 'provider']);
        $business = \App\Models\Business::factory()->create(['user_id' => $this->provider->id]);
        $this->employee = Employee::factory()->create([
            'user_id' => $this->provider->id,
            'business_id' => $business->id,
        ]);
    }

    public function test_index_as_admin()
    {
        $exception = AvailabilityException::factory()->create(['employee_id' => $this->employee->id]);
        $response = $this->actingAs($this->admin)->get(route('availability-exceptions.index', ['employee_id' => $this->employee->id]));
        $response->assertOk();
        $response->assertViewHas('exceptions');
    }

    public function test_index_as_provider()
    {
        $exception = AvailabilityException::factory()->create(['employee_id' => $this->employee->id]);
        $response = $this->actingAs($this->provider)->get(route('availability-exceptions.index', ['employee_id' => $this->employee->id]));
        $response->assertOk();
        $response->assertViewHas('exceptions');
    }

    public function test_create_view()
    {
        $response = $this->actingAs($this->provider)->get(route('availability-exceptions.create', ['employee_id' => $this->employee->id]));
        $response->assertOk();
        $response->assertViewHas('employee');
    }

    public function test_store_creates_exception()
    {
        $data = [
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'type' => 'unavailable',
            'note' => 'Test note',
            'employee_id' => $this->employee->id,
        ];
        $response = $this->actingAs($this->provider)->post(route('availability-exceptions.store'), $data);
        $response->assertRedirect(route('availability-exceptions.index', ['employee_id' => $this->employee->id]));
        $this->assertDatabaseHas('availability_exceptions', [
            'employee_id' => $this->employee->id,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
        ]);
    }

    public function test_store_rejects_overlap()
    {
        AvailabilityException::factory()->create([
            'employee_id' => $this->employee->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);
        $data = [
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:30',
            'end_time' => '10:30',
            'type' => 'unavailable',
            'note' => 'Overlap',
            'employee_id' => $this->employee->id,
        ];
        $response = $this->actingAs($this->provider)->post(route('availability-exceptions.store'), $data);
        $response->assertSessionHasErrors('time');
    }

    public function test_update_edits_exception()
    {
        $exception = AvailabilityException::factory()->create(['employee_id' => $this->employee->id]);
        $data = [
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '11:00',
            'end_time' => '12:00',
            'type' => 'available',
            'note' => 'Updated',
        ];
        $response = $this->actingAs($this->provider)->put(route('availability-exceptions.update', $exception->id), $data);
        $response->assertRedirect(route('availability-exceptions.index', ['employee_id' => $this->employee->id]));
        $this->assertDatabaseHas('availability_exceptions', [
            'id' => $exception->id,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'type' => $data['type'],
        ]);
    }

    public function test_destroy_deletes_exception()
    {
        $exception = AvailabilityException::factory()->create(['employee_id' => $this->employee->id]);
        $response = $this->actingAs($this->provider)->delete(route('availability-exceptions.destroy', $exception->id));
        $response->assertRedirect(route('availability-exceptions.index', ['employee_id' => $this->employee->id]));
        $this->assertDatabaseMissing('availability_exceptions', ['id' => $exception->id]);
    }
}
