<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition()
    {
        return [
            'business_id' => Business::factory(),
            'key' => $this->faker->randomElement([
                'holiday_mode',
                'maintenance_mode',
                'booking_advance_hours',
                'booking_advance_days',
                'booking_buffer_minutes',
            ]),
            'value' => $this->faker->randomElement(['0', '1', '24', '7', '15']),
        ];
    }
}
