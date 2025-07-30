<?php

namespace Tests\Unit;

use App\Models\Business;
use App\Models\User;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $employee = Employee::factory()->create(['business_id' => $business->id]);
        $setting = Setting::factory()->create(['business_id' => $business->id]);
        $category = Category::factory()->create();
        $business->categories()->attach($category);
        $review = Review::factory()->create(['business_id' => $business->id]);

        $this->assertInstanceOf(User::class, $business->user);
        $this->assertTrue($business->services->contains($service));
        $this->assertTrue($business->employees->contains($employee));
        $this->assertTrue($business->settings->contains($setting));
        $this->assertTrue($business->categories->contains($category));
        $this->assertTrue($business->reviews->contains($review));
    }

    public function test_accessors()
    {
        $business = Business::factory()->create(['logo' => 'logos/test.png']);
        $this->assertNotNull($business->logo_url);
        $this->assertIsString($business->logo_url);
    }

    public function test_average_rating_and_reviews_count()
    {
        $business = Business::factory()->create();
        Review::factory()->count(3)->create(['business_id' => $business->id, 'rating' => 4]);
        $this->assertEquals(4, $business->average_rating);
        $this->assertEquals(3, $business->reviews_count);
    }

    public function test_statistics_attributes()
    {
        $business = Business::factory()->create();
        $service = Service::factory()->create(['business_id' => $business->id]);
        $booking = Booking::factory()->create(['service_id' => $service->id, 'status' => 'completed', 'total_price' => 100]);
        $this->assertEquals(1, $business->total_bookings);
        $this->assertEquals(100, $business->total_revenue);
        $this->assertEquals(1, $business->total_customers);
    }

    public function test_most_booked_services()
    {
        $business = Business::factory()->create();
        $service1 = Service::factory()->create(['business_id' => $business->id]);
        $service2 = Service::factory()->create(['business_id' => $business->id]);
        Booking::factory()->count(3)->create(['service_id' => $service1->id]);
        Booking::factory()->count(1)->create(['service_id' => $service2->id]);
        $mostBooked = $business->most_booked_services;
        $service1FromResult = $mostBooked->firstWhere('id', $service1->id);
        $service2FromResult = $mostBooked->firstWhere('id', $service2->id);
        $this->assertNotNull($service1FromResult);
        $this->assertNotNull($service2FromResult);
        $this->assertEquals(3, $service1FromResult->bookings_count);
        $this->assertEquals(1, $service2FromResult->bookings_count);
    }
    public function test_get_bookings_per_period_month()
    {
        $business = Business::factory()->create();
        $service = Service::factory()->create(['business_id' => $business->id]);
        Booking::factory()->create(['service_id' => $service->id, 'start_time' => now()]);
        $result = $business->getBookingsPerPeriod('month', 12);
        $this->assertTrue($result->count() >= 1);
    }

    public function test_get_bookings_per_period_day()
    {
        $business = Business::factory()->create();
        $service = Service::factory()->create(['business_id' => $business->id]);
        Booking::factory()->create(['service_id' => $service->id, 'start_time' => now()]);
        $result = $business->getBookingsPerPeriod('day', 12);
        $this->assertTrue($result->count() >= 1);
    }

    public function test_get_bookings_per_period_week()
    {
        $business = Business::factory()->create();
        $service = Service::factory()->create(['business_id' => $business->id]);
        Booking::factory()->create(['service_id' => $service->id, 'start_time' => now()]);
        $result = $business->getBookingsPerPeriod('week', 12);
        $this->assertTrue($result->count() >= 1);
    }

    public function test_get_revenue_per_period_month()
    {
        $business = Business::factory()->create();
        $service = Service::factory()->create(['business_id' => $business->id]);
        Booking::factory()->create(['service_id' => $service->id, 'start_time' => now(), 'status' => 'completed', 'total_price' => 100]);
        $result = $business->getRevenuePerPeriod('month', 12);
        $this->assertTrue($result->count() >= 1);
    }

    public function test_get_revenue_per_period_day()
    {
        $business = Business::factory()->create();
        $service = Service::factory()->create(['business_id' => $business->id]);
        Booking::factory()->create(['service_id' => $service->id, 'start_time' => now(), 'status' => 'completed', 'total_price' => 100]);
        $result = $business->getRevenuePerPeriod('day', 12);
        $this->assertTrue($result->count() >= 1);
    }

    public function test_get_revenue_per_period_week()
    {
        $business = Business::factory()->create();
        $service = Service::factory()->create(['business_id' => $business->id]);
        Booking::factory()->create(['service_id' => $service->id, 'start_time' => now(), 'status' => 'completed', 'total_price' => 100]);
        $result = $business->getRevenuePerPeriod('week', 12);
        $this->assertTrue($result->count() >= 1);
    }
}
