<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Category;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Services\GeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class BusinessControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_index_requires_provider_or_admin_role()
    {
        $user = User::factory()->create(['role' => 'client']);
        $this->actingAs($user);
        $response = $this->get(route('businesses.index'));
        $response->assertForbidden();
    }

    public function test_index_shows_businesses_for_provider()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $response = $this->get(route('businesses.index'));
        $response->assertOk();
        $response->assertViewHas('businesses');
    }

    public function test_public_index_lists_businesses()
    {
        $business = Business::factory()->create();
        $response = $this->get(route('businesses.public.index'));
        $response->assertOk();
        $response->assertViewHas('businesses');
    }

    public function test_create_requires_provider_or_admin_role()
    {
        $user = User::factory()->create(['role' => 'client']);
        $this->actingAs($user);
        $response = $this->get(route('businesses.create'));
        $response->assertForbidden();
    }

    public function test_create_shows_form_for_provider()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $this->actingAs($user);
        $response = $this->get(route('businesses.create'));
        $response->assertOk();
        $response->assertViewHas('categories');
    }

    public function test_store_creates_business_with_logo_and_categories()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create();
        $this->actingAs($user);

        // Mock the GeocodingService
        $mockGeocoder = Mockery::mock(GeocodingService::class);
        $mockGeocoder->shouldReceive('geocode')->andReturn([
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'formatted_address' => '123 Main St, New York, NY',
        ]);
        $this->app->instance(GeocodingService::class, $mockGeocoder);

        $file = UploadedFile::fake()->image('logo.jpg');
        $data = [
            'name' => 'Test Biz',
            'description' => 'Desc',
            'address' => '123 Main',
            'phone_number' => '1234567890',
            'email' => 'test@biz.com',
            'categories' => [$category->id],
            'logo' => $file,
        ];
        $response = $this->post(route('businesses.store'), $data);
        $response->assertRedirect(route('businesses.index'));
        $this->assertDatabaseHas('businesses', ['name' => 'Test Biz']);
        $this->assertTrue(Storage::disk('public')->exists('business-logos/' . $file->hashName()));
    }

    public function test_show_displays_business()
    {
        $business = Business::factory()->create();
        $response = $this->get(route('businesses.show', $business->id));
        $response->assertOk();
        $response->assertViewHas('business');
    }

    public function test_show_formats_service_prices_with_business_currency()
    {
        $business = Business::factory()->create();
        Setting::setValue($business->id, 'currency', 'HUF');
        Service::factory()->create([
            'business_id' => $business->id,
            'name' => 'Currency Test Service',
            'price' => 1200,
            'active' => true,
        ]);

        $response = $this->get(route('businesses.show', $business->id));

        $response->assertOk();
        $response->assertSee('1,200.00 Ft');
        $response->assertDontSee('1200.00 EUR');
    }

    public function test_edit_requires_owner_or_admin()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $other = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $other->id]);
        $this->actingAs($user);
        $response = $this->get(route('businesses.edit', $business->id));
        $response->assertForbidden();
    }

    public function test_edit_shows_form_for_owner()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $response = $this->get(route('businesses.edit', $business->id));
        $response->assertOk();
        $response->assertViewHas('business');
    }

    public function test_update_updates_business_and_logo()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        // Mock the GeocodingService
        $mockGeocoder = Mockery::mock(GeocodingService::class);
        $mockGeocoder->shouldReceive('geocode')->andReturn([
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'formatted_address' => '123 Main St, New York, NY',
        ]);
        $this->app->instance(GeocodingService::class, $mockGeocoder);

        $file = UploadedFile::fake()->image('logo2.jpg');
        $data = [
            'name' => 'Updated',
            'description' => 'Desc',
            'address' => '123 Main',
            'phone_number' => '1234567890',
            'email' => 'test@biz.com',
            'logo' => $file,
        ];
        $response = $this->put(route('businesses.update', $business->id), $data);
        $response->assertRedirect(route('businesses.index'));
        $this->assertDatabaseHas('businesses', ['name' => 'Updated']);
        $this->assertTrue(Storage::disk('public')->exists('business-logos/' . $file->hashName()));
    }

    public function test_destroy_deletes_business_and_logo()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id, 'logo' => 'business-logos/logo.jpg']);
        Storage::disk('public')->put('business-logos/logo.jpg', 'dummy');
        $this->actingAs($user);
        $response = $this->delete(route('businesses.destroy', $business->id));
        $response->assertRedirect(route('businesses.index'));
        $this->assertDatabaseMissing('businesses', ['id' => $business->id]);
        $this->assertFalse(Storage::disk('public')->exists('business-logos/logo.jpg'));
    }

    public function test_remove_logo_removes_logo_file()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id, 'logo' => 'business-logos/logo.jpg']);
        Storage::disk('public')->put('business-logos/logo.jpg', 'dummy');
        $this->actingAs($user);
        $response = $this->delete(route('businesses.remove-logo', $business->id));
        $response->assertRedirect(route('businesses.edit', $business->id));
        $this->assertDatabaseHas('businesses', ['id' => $business->id, 'logo' => null]);
        $this->assertFalse(Storage::disk('public')->exists('business-logos/logo.jpg'));
    }
}
