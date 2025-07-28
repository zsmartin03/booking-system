<?php

namespace Database\Factories;

use App\Models\BusinessWorkingHour;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessWorkingHourFactory extends Factory
{
    protected $model = BusinessWorkingHour::class;

    public function definition()
    {
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        return [
            'business_id' => Business::factory(),
            'day_of_week' => $this->faker->randomElement($days),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ];
    }
}
