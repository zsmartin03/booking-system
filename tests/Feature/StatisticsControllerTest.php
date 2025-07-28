<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StatisticsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_redirects_to_create_business_if_none_exist()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $this->actingAs($user);
        $response = $this->get(route('statistics.redirect'));
        $response->assertRedirect(route('businesses.create'));
        $response->assertSessionHas('error');
    }

    public function test_redirects_to_first_business_if_exists()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $response = $this->get(route('statistics.redirect'));
        $response->assertRedirect(route('statistics.index', ['business' => $business->id]));
    }

    public function test_index_for_provider_with_access()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $response = $this->get(route('statistics.index', ['business' => $business->id]));
        $response->assertStatus(200);
        $response->assertViewIs('statistics.index');
        $response->assertViewHas('business', $business);
        $response->assertViewHas('businesses');
        $response->assertViewHas('totalBookings');
        $response->assertViewHas('totalRevenue');
        $response->assertViewHas('totalCustomers');
        $response->assertViewHas('mostBookedServices');
        $response->assertViewHas('chartData');
        $response->assertViewHas('period');
    }

    public function test_index_for_admin_sees_all_businesses()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $business1 = Business::factory()->create();
        $business2 = Business::factory()->create();
        $this->actingAs($admin);
        $response = $this->get(route('statistics.index', ['business' => $business1->id]));
        $response->assertStatus(200);
        $response->assertViewHas('businesses', function($businesses) use ($business1, $business2) {
            return $businesses->contains($business1) && $businesses->contains($business2);
        });
    }

    public function test_index_for_provider_without_access_forbidden()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $other = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $other->id]);
        $this->actingAs($user);
        $response = $this->get(route('statistics.index', ['business' => $business->id]));
        $response->assertStatus(403);
    }

    public function test_index_for_client_forbidden()
    {
        $user = User::factory()->create(['role' => 'client']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $response = $this->get(route('statistics.index', ['business' => $business->id]));
        $response->assertStatus(403);
    }

    public function test_get_data_returns_json_for_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $business = Business::factory()->create();
        $this->actingAs($admin);
        $response = $this->get(route('statistics.data', ['business' => $business->id]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'bookings' => ['labels', 'data'],
            'revenue' => ['labels', 'data'],
        ]);
    }

    public function test_get_data_forbidden_for_client()
    {
        $user = User::factory()->create(['role' => 'client']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $response = $this->get(route('statistics.data', ['business' => $business->id]));
        $response->assertStatus(403);
    }

    public function test_get_data_forbidden_for_provider_without_access()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $other = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $other->id]);
        $this->actingAs($user);
        $response = $this->get(route('statistics.data', ['business' => $business->id]));
        $response->assertStatus(403);
        $response->assertJson(['error' => 'You do not have access to this business.']);
    }
}
