<?php

namespace Tests\Unit;

use App\Models\ReviewResponse;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_fields_are_assignable()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);
        $data = [
            'review_id' => $review->id,
            'user_id' => $user->id,
            'response' => 'Thank you for your feedback!',
        ];
        $response = ReviewResponse::create($data);
        $this->assertDatabaseHas('review_responses', ['id' => $response->id] + $data);
    }

    public function test_review_relationship()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);
        $response = ReviewResponse::factory()->create([
            'review_id' => $review->id,
            'user_id' => $user->id,
        ]);
        $this->assertInstanceOf(Review::class, $response->review);
        $this->assertEquals($review->id, $response->review->id);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);
        $response = ReviewResponse::factory()->create([
            'review_id' => $review->id,
            'user_id' => $user->id,
        ]);
        $this->assertInstanceOf(User::class, $response->user);
        $this->assertEquals($user->id, $response->user->id);
    }

    public function test_factory_creates_valid_instance()
    {
        $response = ReviewResponse::factory()->create();
        $this->assertInstanceOf(ReviewResponse::class, $response);
        $this->assertNotNull($response->review_id);
        $this->assertNotNull($response->user_id);
        $this->assertNotEmpty($response->response);
    }
}
