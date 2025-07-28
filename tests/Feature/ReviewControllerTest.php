<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Review;
use App\Models\ReviewResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Optionally seed permissions/roles if needed
    }

    public function test_store_review_successfully()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $this->actingAs($user);
        // Simulate user has booking with business
        // Create a booking for this user with a service belonging to the business
        $service = \App\Models\Service::factory()->create(['business_id' => $business->id]);
        \App\Models\Booking::factory()->create([
            'client_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'confirmed',
        ]);

        $response = $this->postJson(route('reviews.store', $business), [
            'rating' => 5,
            'comment' => 'Great service!'
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'business_id' => $business->id,
            'rating' => 5,
            'comment' => 'Great service!'
        ]);
    }

    public function test_store_review_fails_for_affiliated_user()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['user_id' => $user->id]); // user is owner
        $this->actingAs($user);

        $response = $this->postJson(route('reviews.store', $business), [
            'rating' => 4,
            'comment' => 'Nice!'
        ]);
        $response->assertStatus(403);
    }

    public function test_store_review_fails_if_already_reviewed()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $this->actingAs($user);
        $service = \App\Models\Service::factory()->create(['business_id' => $business->id]);
        \App\Models\Booking::factory()->create([
            'client_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'confirmed',
        ]);
        Review::factory()->create(['user_id' => $user->id, 'business_id' => $business->id]);

        $response = $this->postJson(route('reviews.store', $business), [
            'rating' => 3,
            'comment' => 'Duplicate review!'
        ]);
        $response->assertStatus(400);
    }

    public function test_vote_on_review()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('reviews.vote', $review), [
            'is_upvote' => true
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_vote_fails_for_affiliated_user()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        // Make user owner of the business
        $review->business->user_id = $user->id;
        $review->business->save();
        $this->actingAs($user);

        $response = $this->postJson(route('reviews.vote', $review), [
            'is_upvote' => true
        ]);
        $response->assertStatus(403);
    }

    public function test_respond_to_review()
    {
        $owner = User::factory()->create();
        $review = Review::factory()->create();
        $review->business->user_id = $owner->id;
        $review->business->save();
        $this->actingAs($owner);

        $response = $this->postJson(route('reviews.respond', $review), [
            'response' => 'Thank you!'
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_respond_fails_for_non_owner()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('reviews.respond', $review), [
            'response' => 'Not allowed!'
        ]);
        $response->assertStatus(403);
    }

    public function test_update_review()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->putJson(route('reviews.update', $review), [
            'rating' => 4,
            'comment' => 'Updated comment.'
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => 'Updated comment.'
        ]);
    }

    public function test_update_review_fails_for_other_user()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $this->actingAs($user);

        $response = $this->putJson(route('reviews.update', $review), [
            'rating' => 2,
            'comment' => 'Should not update.'
        ]);
        $response->assertStatus(403);
    }

    public function test_delete_review()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->deleteJson(route('reviews.destroy', $review));
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id
        ]);
    }

    public function test_delete_review_fails_for_other_user()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $this->actingAs($user);

        $response = $this->deleteJson(route('reviews.destroy', $review));
        $response->assertStatus(403);
    }

    public function test_update_response()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $responseModel = ReviewResponse::factory()->create([
            'review_id' => $review->id,
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);

        $response = $this->putJson(route('review-responses.update', $responseModel), [
            'response' => 'Updated response.'
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('review_responses', [
            'id' => $responseModel->id,
            'response' => 'Updated response.'
        ]);
    }

    public function test_update_response_fails_for_other_user()
    {
        $user = User::factory()->create();
        $responseModel = ReviewResponse::factory()->create();
        $this->actingAs($user);

        $response = $this->putJson(route('review-responses.update', $responseModel), [
            'response' => 'Should not update.'
        ]);
        $response->assertStatus(403);
    }

    public function test_delete_response()
    {
        $user = User::factory()->create();
        $responseModel = ReviewResponse::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->deleteJson(route('review-responses.destroy', $responseModel));
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('review_responses', [
            'id' => $responseModel->id
        ]);
    }

    public function test_delete_response_fails_for_other_user()
    {
        $user = User::factory()->create();
        $responseModel = ReviewResponse::factory()->create();
        $this->actingAs($user);

        $response = $this->deleteJson(route('review-responses.destroy', $responseModel));
        $response->assertStatus(403);
    }
}
