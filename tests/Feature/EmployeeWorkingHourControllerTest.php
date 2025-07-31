<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeWorkingHour;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EmployeeWorkingHourControllerTest extends TestCase
{
    protected $admin;
    protected $owner;
    protected $business;
    protected $employee;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->owner = User::factory()->create(['role' => 'provider']);
        $this->business = Business::factory()->create(['user_id' => $this->owner->id]);
        $this->employee = Employee::factory()->create(['business_id' => $this->business->id]);
    }

    public function test_admin_can_view_employee_working_hours_index()
    {
        Auth::login($this->admin);
        $response = $this->getJson(route('employee-working-hours.index', ['employee_id' => $this->employee->id]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'employee',
            'businessWorkingHours',
            'employeeWorkingHours',
        ]);
    }

    public function test_owner_can_view_employee_working_hours_index()
    {
        Auth::login($this->owner);
        $response = $this->getJson(route('employee-working-hours.index', ['employee_id' => $this->employee->id]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'employee',
            'businessWorkingHours',
            'employeeWorkingHours',
        ]);
    }

    public function test_admin_can_create_employee_working_hour()
    {
        Auth::login($this->admin);
        $data = [
            'employee_id' => $this->employee->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00',
            'end_time' => '17:00',
        ];
        $response = $this->post(route('employee-working-hours.store'), $data);
        $response->assertRedirect(route('employee-working-hours.index', ['employee_id' => $this->employee->id]));
        $this->assertDatabaseHas('employee_working_hours', [
            'employee_id' => $this->employee->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
    }

    public function test_admin_can_edit_employee_working_hour()
    {
        Auth::login($this->admin);
        $ewh = EmployeeWorkingHour::factory()->create([
            'employee_id' => $this->employee->id,
            'day_of_week' => 'tuesday',
            'start_time' => '10:00',
            'end_time' => '18:00',
        ]);
        $response = $this->getJson(route('employee-working-hours.edit', $ewh->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'employee',
            'workingHour',
        ]);
    }

    public function test_admin_can_update_employee_working_hour()
    {
        Auth::login($this->admin);
        $ewh = EmployeeWorkingHour::factory()->create([
            'employee_id' => $this->employee->id,
            'day_of_week' => 'wednesday',
            'start_time' => '08:00',
            'end_time' => '16:00',
        ]);
        $data = [
            'day_of_week' => 'wednesday',
            'start_time' => '09:00',
            'end_time' => '17:00',
        ];
        $response = $this->put(route('employee-working-hours.update', $ewh->id), $data);
        $response->assertRedirect(route('employee-working-hours.index', ['employee_id' => $this->employee->id]));
        $this->assertDatabaseHas('employee_working_hours', [
            'id' => $ewh->id,
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);
    }

    public function test_admin_can_delete_employee_working_hour()
    {
        Auth::login($this->admin);
        $ewh = EmployeeWorkingHour::factory()->create([
            'employee_id' => $this->employee->id,
            'day_of_week' => 'thursday',
            'start_time' => '07:00',
            'end_time' => '15:00',
        ]);
        $response = $this->delete(route('employee-working-hours.destroy', $ewh->id));
        $response->assertRedirect(route('employee-working-hours.index', ['employee_id' => $this->employee->id]));
        $this->assertDatabaseMissing('employee_working_hours', [
            'id' => $ewh->id,
        ]);
    }

    public function test_bulk_update_employee_working_hours()
    {
        Auth::login($this->admin);
        $businessWorkingHour = $this->business->workingHours()->create([
            'day_of_week' => 'friday',
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
        ]);
        $data = [
            'employee_id' => $this->employee->id,
            'working_hours' => [
                'friday' => [
                    'enabled' => true,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                ],
            ],
        ];
        $response = $this->post(route('employee-working-hours.bulkUpdate'), $data);
        $response->assertRedirect(route('employee-working-hours.index', ['employee_id' => $this->employee->id]));
        $this->assertDatabaseHas('employee_working_hours', [
            'employee_id' => $this->employee->id,
            'day_of_week' => 'friday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ]);
    }
}
