<?php

namespace Database\Factories;

use App\Models\ReviewVote;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewVoteFactory extends Factory
{
    protected $model = ReviewVote::class;

    public function definition()
    {
        return [
            'review_id' => Review::factory(),
            'user_id' => User::factory(),
            'is_upvote' => $this->faker->boolean(),
        ];
    }
}
