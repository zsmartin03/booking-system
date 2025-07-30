<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'booking_id' => Booking::factory(),
            'title' => $this->faker->sentence,
            'content' => $this->faker->sentence,
            'is_read' => $this->faker->boolean,
            'sent_at' => now(),
            'read_at' => null,
        ];
    }
}
