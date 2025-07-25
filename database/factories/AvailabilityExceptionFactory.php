<?php

namespace Database\Factories;

use App\Models\AvailabilityException;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvailabilityExceptionFactory extends Factory
{
    protected $model = AvailabilityException::class;

    public function definition()
    {
        return [
            'employee_id' => Employee::factory(),
            'date' => $this->faker->date(),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),
            'type' => $this->faker->randomElement(['available', 'unavailable']),
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}
