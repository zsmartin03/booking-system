<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        $start = Carbon::now()->addDays(2)->setTime(10, 0);
        return [
            'client_id' => User::factory(),
            'service_id' => Service::factory(),
            'employee_id' => Employee::factory(),
            'start_time' => $start,
            'end_time' => $start->copy()->addHour(),
            'status' => 'pending',
            'notes' => $this->faker->sentence(),
            'total_price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
