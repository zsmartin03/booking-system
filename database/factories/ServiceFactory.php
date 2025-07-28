<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition()
    {
        return [
            'business_id' => Business::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'duration' => $this->faker->numberBetween(15, 120),
            'active' => true,
        ];
    }
}
