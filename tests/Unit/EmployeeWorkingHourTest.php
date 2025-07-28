<?php

namespace Tests\Unit;

use App\Models\EmployeeWorkingHour;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeWorkingHourTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_employee()
    {
        $hour = EmployeeWorkingHour::factory()->create(['employee_id' => Employee::factory()->create()->id]);
        $this->assertInstanceOf(Employee::class, $hour->employee);
    }

    public function test_fillable_fields()
    {
        $employee = Employee::factory()->create();
        $data = [
            'employee_id' => $employee->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ];
        $hour = EmployeeWorkingHour::create($data);
        $this->assertEquals($data['employee_id'], $hour->employee_id);
        $this->assertEquals($data['day_of_week'], $hour->day_of_week);
        $this->assertEquals($data['start_time'], $hour->start_time);
        $this->assertEquals($data['end_time'], $hour->end_time);
    }
}
