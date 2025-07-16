<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Employee;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use DatabaseTransactions;

    protected $businessOwner;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a business owner with verified email
        $this->businessOwner = User::create([
            'name' => 'Business Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'role' => 'provider',
        ]);
        $this->businessOwner->markEmailAsVerified();

        // Create a client with verified email
        $this->client = User::create([
            'name' => 'Client User',
            'email' => 'client@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);
        $this->client->markEmailAsVerified();
    }

    private function createVerifiedUser($name, $email, $role)
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => $role,
        ]);
        $user->markEmailAsVerified();
        return $user;
    }

    public function test_business_owner_cannot_create_review_for_own_business()
    {
        // Create a business
        $business = Business::create([
            'user_id' => $this->businessOwner->id,
            'name' => 'Test Business',
            'description' => 'A test business',
            'address' => '123 Test St',
            'phone_number' => '123-456-7890',
            'email' => 'test@business.com'
        ]);

        // Authenticate as the business owner
        $this->actingAs($this->businessOwner);

        // Try to create a review for their own business
        $response = $this->postJson("/businesses/{$business->id}/reviews", [
            'rating' => 5,
            'comment' => 'This is a test review'
        ]);

        // Should be forbidden
        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Business owners and employees cannot review their own business'
        ]);

        // Verify no review was created
        $this->assertDatabaseMissing('reviews', [
            'business_id' => $business->id,
            'user_id' => $this->businessOwner->id,
        ]);
    }

    public function test_employee_cannot_create_review_for_own_business()
    {
        // Create a business
        $business = Business::create([
            'user_id' => $this->businessOwner->id,
            'name' => 'Test Business',
            'description' => 'A test business',
            'address' => '123 Test St',
            'phone_number' => '123-456-7890',
            'email' => 'test@business.com'
        ]);

        // Create an employee for the business
        $employee = $this->createVerifiedUser('Test Employee', 'employee@test.com', 'employee');

        Employee::create([
            'business_id' => $business->id,
            'user_id' => $employee->id,
            'name' => 'Test Employee',
            'email' => 'employee@test.com',
            'active' => true
        ]);

        // Authenticate as the employee
        $this->actingAs($employee);

        // Try to create a review for the business
        $response = $this->postJson("/businesses/{$business->id}/reviews", [
            'rating' => 5,
            'comment' => 'This is a test review'
        ]);

        // Should be forbidden
        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Business owners and employees cannot review their own business'
        ]);

        // Verify no review was created
        $this->assertDatabaseMissing('reviews', [
            'business_id' => $business->id,
            'user_id' => $employee->id,
        ]);
    }

    public function test_business_owner_can_create_review_for_other_business()
    {
        // Create another business owned by a different user
        $anotherOwner = $this->createVerifiedUser('Another Owner', 'another@owner.com', 'provider');

        $anotherBusiness = Business::create([
            'user_id' => $anotherOwner->id,
            'name' => 'Another Business',
            'description' => 'Another test business',
            'address' => '456 Another St',
            'phone_number' => '987-654-3210',
            'email' => 'another@business.com'
        ]);

        // Authenticate as the first business owner
        $this->actingAs($this->businessOwner);

        // Try to create a review for the other business
        $response = $this->postJson("/businesses/{$anotherBusiness->id}/reviews", [
            'rating' => 4,
            'comment' => 'Great service from a fellow business owner!'
        ]);

        // Should be successful
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Review submitted successfully!'
        ]);

        // Verify the review was created
        $this->assertDatabaseHas('reviews', [
            'business_id' => $anotherBusiness->id,
            'user_id' => $this->businessOwner->id,
            'rating' => 4,
            'comment' => 'Great service from a fellow business owner!'
        ]);
    }

    public function test_employee_can_create_review_for_other_business()
    {
        // Create a business
        $business = Business::create([
            'user_id' => $this->businessOwner->id,
            'name' => 'Test Business',
            'description' => 'A test business',
            'address' => '123 Test St',
            'phone_number' => '123-456-7890',
            'email' => 'test@business.com'
        ]);

        // Create an employee for the business
        $employee = $this->createVerifiedUser('Test Employee', 'employee@test.com', 'employee');

        Employee::create([
            'business_id' => $business->id,
            'user_id' => $employee->id,
            'name' => 'Test Employee',
            'email' => 'employee@test.com',
            'active' => true
        ]);

        // Create another business owned by a different user
        $anotherOwner = $this->createVerifiedUser('Another Owner', 'another@owner.com', 'provider');

        $anotherBusiness = Business::create([
            'user_id' => $anotherOwner->id,
            'name' => 'Another Business',
            'description' => 'Another test business',
            'address' => '456 Another St',
            'phone_number' => '987-654-3210',
            'email' => 'another@business.com'
        ]);

        // Authenticate as the employee
        $this->actingAs($employee);

        // Try to create a review for the other business (not their own)
        $response = $this->postJson("/businesses/{$anotherBusiness->id}/reviews", [
            'rating' => 5,
            'comment' => 'Excellent service! Highly recommend!'
        ]);

        // Should be successful
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Review submitted successfully!'
        ]);

        // Verify the review was created
        $this->assertDatabaseHas('reviews', [
            'business_id' => $anotherBusiness->id,
            'user_id' => $employee->id,
            'rating' => 5,
            'comment' => 'Excellent service! Highly recommend!'
        ]);
    }

    public function test_business_owner_can_vote_on_reviews_for_other_business()
    {
        // Create another business and owner
        $anotherOwner = $this->createVerifiedUser('Another Owner', 'another@owner.com', 'provider');

        $anotherBusiness = Business::create([
            'user_id' => $anotherOwner->id,
            'name' => 'Another Business',
            'description' => 'Another test business',
            'address' => '456 Another St',
            'phone_number' => '987-654-3210',
            'email' => 'another@business.com'
        ]);

        // Create a review on the other business by a regular client
        $review = Review::create([
            'business_id' => $anotherBusiness->id,
            'user_id' => $this->client->id,
            'rating' => 4,
            'comment' => 'Good service',
            'has_booking' => false
        ]);

        // Authenticate as the first business owner
        $this->actingAs($this->businessOwner);

        // Try to vote on the review
        $response = $this->postJson("/reviews/{$review->id}/vote", [
            'is_upvote' => true
        ]);

        // Should be successful
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify the vote was created
        $this->assertDatabaseHas('review_votes', [
            'review_id' => $review->id,
            'user_id' => $this->businessOwner->id,
            'is_upvote' => true
        ]);
    }

    public function test_employee_can_vote_on_reviews_for_other_business()
    {
        // Create a business with an employee
        $business = Business::create([
            'user_id' => $this->businessOwner->id,
            'name' => 'Test Business',
            'description' => 'A test business',
            'address' => '123 Test St',
            'phone_number' => '123-456-7890',
            'email' => 'test@business.com'
        ]);

        $employee = $this->createVerifiedUser('Test Employee', 'employee@test.com', 'employee');

        Employee::create([
            'business_id' => $business->id,
            'user_id' => $employee->id,
            'name' => 'Test Employee',
            'email' => 'employee@test.com',
            'active' => true
        ]);

        // Create another business and owner
        $anotherOwner = $this->createVerifiedUser('Another Owner', 'another@owner.com', 'provider');

        $anotherBusiness = Business::create([
            'user_id' => $anotherOwner->id,
            'name' => 'Another Business',
            'description' => 'Another test business',
            'address' => '456 Another St',
            'phone_number' => '987-654-3210',
            'email' => 'another@business.com'
        ]);

        // Create a review on the other business by a regular client
        $review = Review::create([
            'business_id' => $anotherBusiness->id,
            'user_id' => $this->client->id,
            'rating' => 4,
            'comment' => 'Good service',
            'has_booking' => false
        ]);

        // Authenticate as the employee
        $this->actingAs($employee);

        // Try to vote on the review
        $response = $this->postJson("/reviews/{$review->id}/vote", [
            'is_upvote' => false
        ]);

        // Should be successful
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify the vote was created
        $this->assertDatabaseHas('review_votes', [
            'review_id' => $review->id,
            'user_id' => $employee->id,
            'is_upvote' => false
        ]);
    }

    public function test_business_owner_cannot_vote_on_reviews_for_own_business()
    {
        // Create a business
        $business = Business::create([
            'user_id' => $this->businessOwner->id,
            'name' => 'Test Business',
            'description' => 'A test business',
            'address' => '123 Test St',
            'phone_number' => '123-456-7890',
            'email' => 'test@business.com'
        ]);

        // Create a review on the business by a regular client
        $review = Review::create([
            'business_id' => $business->id,
            'user_id' => $this->client->id,
            'rating' => 4,
            'comment' => 'Good service',
            'has_booking' => false
        ]);

        // Authenticate as the business owner
        $this->actingAs($this->businessOwner);

        // Try to vote on the review
        $response = $this->postJson("/reviews/{$review->id}/vote", [
            'is_upvote' => true
        ]);

        // Should be forbidden
        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Business owners and employees cannot vote on reviews for their own business'
        ]);

        // Verify no vote was created
        $this->assertDatabaseMissing('review_votes', [
            'review_id' => $review->id,
            'user_id' => $this->businessOwner->id,
        ]);
    }

    public function test_employee_cannot_vote_on_reviews_for_own_business()
    {
        // Create a business
        $business = Business::create([
            'user_id' => $this->businessOwner->id,
            'name' => 'Test Business',
            'description' => 'A test business',
            'address' => '123 Test St',
            'phone_number' => '123-456-7890',
            'email' => 'test@business.com'
        ]);

        // Create an employee for the business
        $employee = $this->createVerifiedUser('Test Employee', 'employee@test.com', 'employee');

        Employee::create([
            'business_id' => $business->id,
            'user_id' => $employee->id,
            'name' => 'Test Employee',
            'email' => 'employee@test.com',
            'active' => true
        ]);

        // Create a review on the business by a regular client
        $review = Review::create([
            'business_id' => $business->id,
            'user_id' => $this->client->id,
            'rating' => 4,
            'comment' => 'Good service',
            'has_booking' => false
        ]);

        // Authenticate as the employee
        $this->actingAs($employee);

        // Try to vote on the review
        $response = $this->postJson("/reviews/{$review->id}/vote", [
            'is_upvote' => true
        ]);

        // Should be forbidden
        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Business owners and employees cannot vote on reviews for their own business'
        ]);

        // Verify no vote was created
        $this->assertDatabaseMissing('review_votes', [
            'review_id' => $review->id,
            'user_id' => $employee->id,
        ]);
    }

    public function test_client_can_create_review_for_any_business()
    {
        // Create a business
        $business = Business::create([
            'user_id' => $this->businessOwner->id,
            'name' => 'Test Business',
            'description' => 'A test business',
            'address' => '123 Test St',
            'phone_number' => '123-456-7890',
            'email' => 'test@business.com'
        ]);

        // Authenticate as the client
        $this->actingAs($this->client);

        // Try to create a review for the business
        $response = $this->postJson("/businesses/{$business->id}/reviews", [
            'rating' => 5,
            'comment' => 'Excellent service!'
        ]);

        // Should be successful
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Review submitted successfully!'
        ]);

        // Verify the review was created
        $this->assertDatabaseHas('reviews', [
            'business_id' => $business->id,
            'user_id' => $this->client->id,
            'rating' => 5,
            'comment' => 'Excellent service!'
        ]);
    }

    public function test_client_can_vote_on_reviews_for_business()
    {
        // Create a business owner and two clients
        $business = Business::create([
            'user_id' => $this->businessOwner->id,
            'name' => 'Test Business',
            'description' => 'Test description',
            'address' => 'Test address',
            'phone_number' => '1234567890',
            'email' => 'test@business.com',
        ]);

        $client1 = $this->createVerifiedUser('Client 1', 'client1@test.com', 'client');
        $client2 = $this->createVerifiedUser('Client 2', 'client2@test.com', 'client');

        // Create a review by client1
        $review = Review::create([
            'business_id' => $business->id,
            'user_id' => $client1->id,
            'rating' => 4,
            'comment' => 'Good service!',
            'has_booking' => false
        ]);

        // Act as client2 trying to vote
        $response = $this->actingAs($client2)
            ->postJson("/reviews/{$review->id}/vote", [
                'is_upvote' => true,
            ]);

        // Assert that the request is successful
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verify vote was created
        $this->assertDatabaseHas('review_votes', [
            'review_id' => $review->id,
            'user_id' => $client2->id,
            'is_upvote' => true,
        ]);
    }
}
