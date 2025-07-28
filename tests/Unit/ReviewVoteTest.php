<?php

namespace Tests\Unit;

use App\Models\ReviewVote;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewVoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_vote_belongs_to_review()
    {
        $vote = ReviewVote::factory()->create();
        $this->assertInstanceOf(Review::class, $vote->review);
    }

    public function test_review_vote_belongs_to_user()
    {
        $vote = ReviewVote::factory()->create();
        $this->assertInstanceOf(User::class, $vote->user);
    }

    public function test_is_upvote_casts_to_boolean()
    {
        $vote = ReviewVote::factory()->create(['is_upvote' => 1]);
        $this->assertIsBool($vote->is_upvote);
    }

    public function test_fillable_fields()
    {
        $data = [
            'review_id' => Review::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'is_upvote' => true,
        ];
        $vote = ReviewVote::create($data);
        $this->assertEquals($data['review_id'], $vote->review_id);
        $this->assertEquals($data['user_id'], $vote->user_id);
        $this->assertTrue($vote->is_upvote);
    }
}
