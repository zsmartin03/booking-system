<?php

namespace Tests\Unit;

use App\Models\AvailabilityException;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityExceptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_employee()
    {
        $exception = AvailabilityException::factory()->create();
        $this->assertInstanceOf(Employee::class, $exception->employee);
    }

    public function test_fillable_fields()
    {
        $employee = Employee::factory()->create();
        $data = [
            'employee_id' => $employee->id,
            'date' => '2025-07-28',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'type' => 'unavailable',
            'note' => 'Vacation',
        ];
        $exception = AvailabilityException::create($data);
        $this->assertEquals($data['employee_id'], $exception->employee_id);
        $this->assertEquals($data['date'], $exception->date);
        $this->assertEquals($data['start_time'], $exception->start_time);
        $this->assertEquals($data['end_time'], $exception->end_time);
        $this->assertEquals($data['type'], $exception->type);
        $this->assertEquals($data['note'], $exception->note);
    }
}
