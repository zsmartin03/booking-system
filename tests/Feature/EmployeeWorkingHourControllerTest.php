<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Employee;
use App\Models\EmployeeWorkingHour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeWorkingHourControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    private function actingAsProvider($business = null)
    {
        $provider = User::factory()->create(['role' => 'provider']);
        if ($business) {
            $business->user_id = $provider->id;
            $business->save();
        }
        $this->actingAs($provider);
        return $provider;
    }

    private function createEmployeeWithBusiness()
    {
        $business = Business::factory()->create();
        $employee = Employee::factory()->create(['business_id' => $business->id]);
        return [$business, $employee];
    }

    public function test_index_shows_working_hours_for_admin_and_provider()
    {
        [$business, $employee] = $this->createEmployeeWithBusiness();
        EmployeeWorkingHour::factory()->create([
            'employee_id' => $employee->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ]);
        $this->actingAsAdmin();
        $response = $this->get(route('employee-working-hours.index', ['employee_id' => $employee->id]));
        $response->assertStatus(200);
        $this->actingAsProvider($business);
        $response = $this->get(route('employee-working-hours.index', ['employee_id' => $employee->id]));
        $response->assertStatus(200);
    }

    public function test_create_shows_form_for_admin_and_provider()
    {
        [$business, $employee] = $this->createEmployeeWithBusiness();
        $this->actingAsAdmin();
        $response = $this->get(route('employee-working-hours.create', ['employee_id' => $employee->id]));
        $response->assertStatus(200);
        $this->actingAsProvider($business);
        $response = $this->get(route('employee-working-hours.create', ['employee_id' => $employee->id]));
        $response->assertStatus(200);
    }

    public function test_store_creates_working_hour()
    {
        [$business, $employee] = $this->createEmployeeWithBusiness();
        $this->actingAsProvider($business);
        $data = [
            'employee_id' => $employee->id,
            'day_of_week' => 'tuesday',
            'start_time' => '10:00',
            'end_time' => '18:00',
        ];
        $response = $this->post(route('employee-working-hours.store'), $data);
        $response->assertRedirect(route('employee-working-hours.index', ['employee_id' => $employee->id]));
        $this->assertDatabaseHas('employee_working_hours', [
            'employee_id' => $employee->id,
            'day_of_week' => 'tuesday',
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
        ]);
    }

    public function test_edit_shows_form_for_admin_and_provider()
    {
        [$business, $employee] = $this->createEmployeeWithBusiness();
        $ewh = EmployeeWorkingHour::factory()->create(['employee_id' => $employee->id]);
        $this->actingAsAdmin();
        $response = $this->get(route('employee-working-hours.edit', $ewh->id));
        $response->assertStatus(200);
        $this->actingAsProvider($business);
        $response = $this->get(route('employee-working-hours.edit', $ewh->id));
        $response->assertStatus(200);
    }

    public function test_update_modifies_working_hour()
    {
        [$business, $employee] = $this->createEmployeeWithBusiness();
        $ewh = EmployeeWorkingHour::factory()->create([
            'employee_id' => $employee->id,
            'day_of_week' => 'wednesday',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
        ]);
        $this->actingAsProvider($business);
        $data = [
            'day_of_week' => 'wednesday',
            'start_time' => '09:00',
            'end_time' => '17:00',
        ];
        $response = $this->put(route('employee-working-hours.update', $ewh->id), $data);
        $response->assertRedirect(route('employee-working-hours.index', ['employee_id' => $employee->id]));
        $this->assertDatabaseHas('employee_working_hours', [
            'id' => $ewh->id,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ]);
    }

    public function test_destroy_deletes_working_hour()
    {
        [$business, $employee] = $this->createEmployeeWithBusiness();
        $ewh = EmployeeWorkingHour::factory()->create(['employee_id' => $employee->id]);
        $this->actingAsProvider($business);
        $response = $this->delete(route('employee-working-hours.destroy', $ewh->id));
        $response->assertRedirect(route('employee-working-hours.index', ['employee_id' => $employee->id]));
        $this->assertDatabaseMissing('employee_working_hours', ['id' => $ewh->id]);
    }

    public function test_bulk_update_sets_and_removes_working_hours()
    {
        [$business, $employee] = $this->createEmployeeWithBusiness();
        $this->actingAsProvider($business);
        $business->workingHours()->create([
            'day_of_week' => 'friday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ]);
        $data = [
            'employee_id' => $employee->id,
            'working_hours' => [
                'friday' => [
                    'enabled' => true,
                    'start_time' => '10:00',
                    'end_time' => '16:00',
                ],
                'monday' => [
                    'enabled' => false,
                ],
            ],
        ];
        $response = $this->post(route('employee-working-hours.bulk-update'), $data);
        $response->assertRedirect(route('employee-working-hours.index', ['employee_id' => $employee->id]));
        $this->assertDatabaseHas('employee_working_hours', [
            'employee_id' => $employee->id,
            'day_of_week' => 'friday',
            'start_time' => '10:00:00',
            'end_time' => '16:00:00',
        ]);
        $this->assertDatabaseMissing('employee_working_hours', [
            'employee_id' => $employee->id,
            'day_of_week' => 'monday',
        ]);
    }

    public function test_non_provider_cannot_access_other_employee_working_hours()
    {
        [$business, $employee] = $this->createEmployeeWithBusiness();
        $ewh = EmployeeWorkingHour::factory()->create(['employee_id' => $employee->id]);
        $otherProvider = User::factory()->create(['role' => 'provider']);
        $this->actingAs($otherProvider);
        $response = $this->get(route('employee-working-hours.index', ['employee_id' => $employee->id]));
        $response->assertStatus(404);
        $response = $this->get(route('employee-working-hours.create', ['employee_id' => $employee->id]));
        $response->assertStatus(404);
        $response = $this->get(route('employee-working-hours.edit', $ewh->id));
        $response->assertStatus(404);
        $response = $this->put(route('employee-working-hours.update', $ewh->id), [
            'day_of_week' => 'monday', 'start_time' => '09:00', 'end_time' => '17:00',
        ]);
        $response->assertStatus(404);
        $response = $this->delete(route('employee-working-hours.destroy', $ewh->id));
        $response->assertStatus(404);
        $response = $this->post(route('employee-working-hours.bulk-update'), [
            'employee_id' => $employee->id,
            'working_hours' => [
                'monday' => [
                    'enabled' => true,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                ],
            ],
        ]);
        $response->assertStatus(404);
    }
}
