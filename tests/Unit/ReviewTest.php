<?php

namespace Tests\Unit;

use App\Models\Review;
use App\Models\Business;
use App\Models\User;
use App\Models\ReviewResponse;
use App\Models\ReviewVote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_and_casts()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $review = Review::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Great!',
            'has_booking' => true,
        ]);
        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
        $this->assertIsInt($review->rating);
        $this->assertIsBool($review->has_booking);
    }

    public function test_business_relationship()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $review = Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
        ]);
        $this->assertInstanceOf(Business::class, $review->business);
        $this->assertEquals($business->id, $review->business->id);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $review = Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
        ]);
        $this->assertInstanceOf(User::class, $review->user);
        $this->assertEquals($user->id, $review->user->id);
    }

    public function test_response_relationship()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $review = Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
        ]);
        $response = ReviewResponse::factory()->create([
            'review_id' => $review->id,
            'user_id' => $user->id,
            'response' => 'Thanks!'
        ]);
        $this->assertInstanceOf(ReviewResponse::class, $review->response);
        $this->assertEquals($response->id, $review->response->id);
    }

    public function test_votes_relationship_and_counts()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $review = Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
        ]);
        $upvoter = User::factory()->create();
        $downvoter = User::factory()->create();
        ReviewVote::factory()->create(['review_id' => $review->id, 'user_id' => $upvoter->id, 'is_upvote' => true]);
        ReviewVote::factory()->create(['review_id' => $review->id, 'user_id' => $downvoter->id, 'is_upvote' => false]);
        $this->assertEquals(1, $review->upvotes_count);
        $this->assertEquals(1, $review->downvotes_count);
        $this->assertEquals(0, $review->net_votes);
    }

    public function test_user_has_voted_and_vote_type()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $review = Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
        ]);
        $voter = User::factory()->create();
        ReviewVote::factory()->create(['review_id' => $review->id, 'user_id' => $voter->id, 'is_upvote' => true]);
        $this->assertTrue($review->userHasVoted($voter->id));
        $this->assertFalse($review->userHasVoted($user->id));
        $this->assertTrue($review->getUserVoteType($voter->id));
        $this->assertNull($review->getUserVoteType($user->id));
    }

    public function test_factory_creates_valid_instance()
    {
        $review = Review::factory()->create();
        $this->assertInstanceOf(Review::class, $review);
        $this->assertNotNull($review->business_id);
        $this->assertNotNull($review->user_id);
        $this->assertIsInt($review->rating);
        $this->assertIsBool($review->has_booking);
    }
}
