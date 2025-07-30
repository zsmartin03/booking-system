<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Review;
use App\Models\ReviewVote;
use App\Models\ReviewResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_and_hidden_attributes()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret',
            'phone_number' => '1234567890',
            'role' => 'admin',
            'avatar' => 'avatar.png',
        ]);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('1234567890', $user->phone_number);
        $this->assertEquals('admin', $user->role);
        $this->assertEquals('avatar.png', $user->avatar);
        $array = $user->toArray();
        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    public function test_casts_method()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'password' => 'secret',
        ]);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
        $this->assertNotEquals('secret', $user->password); // Should be hashed
        $this->assertTrue(password_verify('secret', $user->password));
    }

    public function test_businesses_relationship()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->businesses->contains($business));
    }

    public function test_employees_relationship()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->employees->contains($employee));
    }

    public function test_employee_relationship()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $this->assertEquals($employee->id, $user->employee->id);
    }

    public function test_client_bookings_relationship()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['client_id' => $user->id]);
        $this->assertTrue($user->clientBookings->contains($booking));
    }

    public function test_notifications_relationship()
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->notifications->contains($notification));
    }

    public function test_reviews_relationship()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->reviews->contains($review));
    }

    public function test_review_votes_relationship()
    {
        $user = User::factory()->create();
        $vote = ReviewVote::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->reviewVotes->contains($vote));
    }

    public function test_review_responses_relationship()
    {
        $user = User::factory()->create();
        $response = ReviewResponse::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->reviewResponses->contains($response));
    }

    public function test_has_booking_with_business()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $service = \App\Models\Service::factory()->create(['business_id' => $business->id]);
        $booking = Booking::factory()->create([
            'client_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'confirmed',
        ]);
        $this->assertTrue($user->hasBookingWithBusiness($business->id));
        $booking->update(['status' => 'cancelled']);
        $this->assertFalse($user->hasBookingWithBusiness($business->id));
    }

    public function test_owns_business()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['user_id' => $user->id]);
        $otherBusiness = Business::factory()->create();
        $this->assertTrue($user->ownsBusiness($business->id));
        $this->assertFalse($user->ownsBusiness($otherBusiness->id));
    }

    public function test_is_employee_of_business()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id, 'business_id' => $business->id]);
        $this->assertTrue($user->isEmployeeOfBusiness($business->id));
        $otherBusiness = Business::factory()->create();
        $this->assertFalse($user->isEmployeeOfBusiness($otherBusiness->id));
    }

    public function test_is_affiliated_with_business()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['user_id' => $user->id]);
        $employee = Employee::factory()->create(['user_id' => $user->id, 'business_id' => $business->id]);
        $otherBusiness = Business::factory()->create();
        $this->assertTrue($user->isAffiliatedWithBusiness($business->id));
        $this->assertFalse($user->isAffiliatedWithBusiness($otherBusiness->id));
    }

    public function test_get_avatar_url_returns_url_or_null()
    {
        $user = User::factory()->create(['avatar' => 'avatar.png']);
        $this->assertStringContainsString('storage/avatar.png', $user->getAvatarUrl());
        $user->avatar = null;
        $this->assertNull($user->getAvatarUrl());
    }

    public function test_get_avatar_or_initials()
    {
        $user = User::factory()->create(['name' => 'Jane Doe', 'avatar' => 'avatar.png']);
        $result = $user->getAvatarOrInitials();
        $this->assertEquals('Jane Doe', $result['name']);
        $this->assertEquals('J', $result['initials']);
        $this->assertStringContainsString('storage/avatar.png', $result['avatar']);
    }

    public function test_send_email_verification_notification_skips_example_com()
    {
        $user = User::factory()->create(['email' => 'skip@example.com']);
        NotificationFacade::fake();
        $user->sendEmailVerificationNotification();
        NotificationFacade::assertNothingSent();
    }

    public function test_send_email_verification_notification_sends_for_non_example_com()
    {
        $user = User::factory()->create(['email' => 'real@domain.com']);
        NotificationFacade::fake();
        $user->sendEmailVerificationNotification();
        NotificationFacade::assertSentTo($user, VerifyEmail::class);
    }
}
