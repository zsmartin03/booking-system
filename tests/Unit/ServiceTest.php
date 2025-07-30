<?php

namespace Tests\Unit;

use App\Models\Service;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_and_casts()
    {
        $business = Business::factory()->create();
        $service = Service::create([
            'business_id' => $business->id,
            'name' => 'Test Service',
            'description' => 'Desc',
            'price' => 99.99,
            'duration' => 60,
            'active' => true,
        ]);
        $this->assertDatabaseHas('services', ['id' => $service->id]);
        $this->assertIsBool($service->active);
        $this->assertEquals(99.99, (float)$service->price);
    }

    public function test_business_relationship()
    {
        $business = Business::factory()->create();
        $service = Service::factory()->create(['business_id' => $business->id]);
        $this->assertInstanceOf(Business::class, $service->business);
        $this->assertEquals($business->id, $service->business->id);
    }

    public function test_employees_relationship()
    {
        $service = Service::factory()->create();
        $employee = Employee::factory()->create();
        $service->employees()->attach($employee);
        $this->assertTrue($service->employees->contains($employee));
    }

    public function test_bookings_relationship()
    {
        $service = Service::factory()->create();
        $booking = Booking::factory()->create(['service_id' => $service->id]);
        $this->assertTrue($service->bookings->contains($booking));
    }

    public function test_get_available_currencies()
    {
        $currencies = Service::getAvailableCurrencies();
        $this->assertIsArray($currencies);
        $this->assertArrayHasKey('USD', $currencies);
        $this->assertArrayHasKey('HUF', $currencies);
    }

    public function test_format_price_with_symbol_currency()
    {
        $formatted = Service::formatPrice(100, 'USD');
        $this->assertEquals('$100.00', $formatted);
    }

    public function test_format_price_with_text_currency()
    {
        $formatted = Service::formatPrice(100, 'HUF');
        $this->assertEquals('100.00 Ft', $formatted);
    }

    public function test_format_price_with_unknown_currency()
    {
        $formatted = Service::formatPrice(100, 'XYZ');
        $this->assertEquals('100.00 XYZ', $formatted);
    }
}
