<?php

namespace Database\Factories;

use App\Models\ReviewResponse;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewResponseFactory extends Factory
{
    protected $model = ReviewResponse::class;

    public function definition()
    {
        return [
            'review_id' => Review::factory(),
            'user_id' => User::factory(),
            'response' => $this->faker->sentence(12),
        ];
    }
}
