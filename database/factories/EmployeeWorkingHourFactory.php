<?php

namespace Database\Factories;

use App\Models\EmployeeWorkingHour;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeWorkingHourFactory extends Factory
{
    protected $model = EmployeeWorkingHour::class;

    public function definition()
    {
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        return [
            'employee_id' => null, // set in test
            'day_of_week' => $this->faker->randomElement($days),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ];
    }
}
