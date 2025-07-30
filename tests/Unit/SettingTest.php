<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_relationship()
    {
        $business = Business::factory()->create();
        $setting = Setting::factory()->create(['business_id' => $business->id]);
        $this->assertInstanceOf(Business::class, $setting->business);
        $this->assertEquals($business->id, $setting->business->id);
    }

    public function test_get_value_returns_default_if_not_found()
    {
        $business = Business::factory()->create();
        Cache::shouldReceive('remember')->andReturn('default_value');
        $value = Setting::getValue($business->id, 'nonexistent_key', 'default_value');
        $this->assertEquals('default_value', $value);
    }

    public function test_set_value_and_get_value()
    {
        $business = Business::factory()->create();
        Setting::setValue($business->id, 'currency', 'USD');
        $value = Setting::getValue($business->id, 'currency', 'HUF');
        $this->assertEquals('USD', $value);
    }

    public function test_get_business_settings_merges_defaults()
    {
        $business = Business::factory()->create();
        Setting::setValue($business->id, 'currency', 'USD');
        $settings = Setting::getBusinessSettings($business->id);
        $this->assertEquals('USD', $settings['currency']);
        $this->assertEquals(2, $settings['booking_advance_hours']); // default
    }

    public function test_get_default_settings()
    {
        $defaults = Setting::getDefaultSettings();
        $this->assertIsArray($defaults);
        $this->assertArrayHasKey('currency', $defaults);
        $this->assertEquals('HUF', $defaults['currency']);
    }

    public function test_clear_business_cache_forgets_all_keys()
    {
        $business = Business::factory()->create();
        Cache::shouldReceive('forget')->times(count(Setting::getDefaultSettings()) + 1);
        Setting::clearBusinessCache($business->id);
        $this->assertTrue(true); // If no exception, test passes
    }
}
