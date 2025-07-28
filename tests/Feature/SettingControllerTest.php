<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Setting;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_redirects_if_no_business_id()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $this->actingAs($user);
        $response = $this->get(route('settings.index'));
        $response->assertRedirect(route('businesses.index'));
        $response->assertSessionHas('error');
    }

    public function test_index_displays_settings_for_owner()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $response = $this->get(route('settings.index', ['business_id' => $business->id]));
        $response->assertStatus(200);
        $response->assertViewIs('settings.index');
        $response->assertViewHas('business', $business);
        $response->assertViewHas('settings');
        $response->assertViewHas('availableCurrencies');
    }

    public function test_index_forbidden_for_non_owner_provider()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $other = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $other->id]);
        $this->actingAs($user);
        $response = $this->get(route('settings.index', ['business_id' => $business->id]));
        $response->assertStatus(403);
    }

    public function test_update_settings_successfully()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $currencies = array_keys(Service::getAvailableCurrencies());
        $data = [
            'business_id' => $business->id,
            'booking_advance_hours' => 24,
            'booking_advance_days' => 30,
            'currency' => $currencies[0],
            'allow_cancellation_hours' => 12,
            'business_timezone' => 'UTC',
            'booking_buffer_minutes' => 10,
            'holiday_mode' => 'on',
            'maintenance_mode' => 'on',
            'booking_confirmation_required' => 'on',
        ];
        $response = $this->put(route('settings.update'), $data);
        $response->assertRedirect(route('settings.index', ['business_id' => $business->id]));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('settings', [
            'business_id' => $business->id,
            'key' => 'holiday_mode',
            'value' => '1',
        ]);
    }

    public function test_update_forbidden_for_non_owner_provider()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $other = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $other->id]);
        $this->actingAs($user);
        $currencies = array_keys(Service::getAvailableCurrencies());
        $data = [
            'business_id' => $business->id,
            'booking_advance_hours' => 24,
            'booking_advance_days' => 30,
            'currency' => $currencies[0],
            'allow_cancellation_hours' => 12,
            'business_timezone' => 'UTC',
            'booking_buffer_minutes' => 10,
        ];
        $response = $this->put(route('settings.update'), $data);
        $response->assertStatus(403);
    }

    public function test_update_validation_fails()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $data = [
            'business_id' => $business->id,
            'booking_advance_hours' => -1, // invalid
            'booking_advance_days' => 0, // invalid
            'currency' => 'FAKE', // invalid
            'allow_cancellation_hours' => 9999, // invalid
            'business_timezone' => '', // invalid
            'booking_buffer_minutes' => -5, // invalid
        ];
        $response = $this->put(route('settings.update'), $data);
        $response->assertSessionHasErrors([
            'booking_advance_hours',
            'booking_advance_days',
            'currency',
            'allow_cancellation_hours',
            'business_timezone',
            'booking_buffer_minutes',
        ]);
    }

    public function test_reset_settings_successfully()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        Setting::factory()->create(['business_id' => $business->id, 'key' => 'holiday_mode', 'value' => '1']);
        $this->actingAs($user);
        $response = $this->post(route('settings.reset'), ['business_id' => $business->id]);
        $response->assertRedirect(route('settings.index', ['business_id' => $business->id]));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('settings', [
            'business_id' => $business->id,
            'key' => 'holiday_mode',
        ]);
    }

    public function test_reset_forbidden_for_non_owner_provider()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $other = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $other->id]);
        $this->actingAs($user);
        $response = $this->post(route('settings.reset'), ['business_id' => $business->id]);
        $response->assertStatus(403);
    }
}
