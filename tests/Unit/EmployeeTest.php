<?php

namespace Tests\Unit;

use App\Models\Employee;
use App\Models\Business;
use App\Models\User;
use App\Models\Service;
use App\Models\EmployeeWorkingHour;
use App\Models\AvailabilityException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships()
    {
        $business = Business::factory()->create();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['business_id' => $business->id, 'user_id' => $user->id]);
        $service = Service::factory()->create();
        $employee->services()->attach($service);
        $workingHour = EmployeeWorkingHour::factory()->create(['employee_id' => $employee->id]);
        $exception = AvailabilityException::factory()->create(['employee_id' => $employee->id]);

        $this->assertInstanceOf(Business::class, $employee->business);
        $this->assertInstanceOf(User::class, $employee->user);
        $this->assertTrue($employee->services->contains($service));
        $this->assertTrue($employee->workingHours->contains($workingHour));
        $this->assertTrue($employee->availabilityExceptions->contains($exception));
    }

    public function test_can_provide_service()
    {
        $employee = Employee::factory()->create(['active' => true]);
        $service = Service::factory()->create();
        $employee->services()->attach($service);
        $this->assertTrue($employee->canProvideService($service->id));
        $this->assertFalse($employee->canProvideService($service->id + 1));
    }

    public function test_get_working_hours_for_day()
    {
        $employee = Employee::factory()->create();
        $workingHour = EmployeeWorkingHour::factory()->create(['employee_id' => $employee->id, 'day_of_week' => 'monday']);
        $hours = $employee->getWorkingHoursForDay('monday');
        $this->assertTrue($hours->contains($workingHour));
    }

    public function test_is_available_at_and_get_availability_exceptions_for_date()
    {
        $employee = Employee::factory()->create(['active' => true]);
        $date = Carbon::parse('2025-07-28');
        $workingHour = EmployeeWorkingHour::factory()->create(['employee_id' => $employee->id, 'day_of_week' => 'monday', 'start_time' => '09:00', 'end_time' => '17:00']);
        $exception = AvailabilityException::factory()->create(['employee_id' => $employee->id, 'date' => '2025-07-28', 'start_time' => '12:00', 'end_time' => '13:00', 'type' => 'unavailable']);
        $this->assertTrue($employee->isAvailableAt($date, '10:00'));
        $this->assertFalse($employee->isAvailableAt($date, '12:30'));
        $exceptions = $employee->getAvailabilityExceptionsForDate($date);
        $this->assertTrue($exceptions->contains($exception));
    }
}
