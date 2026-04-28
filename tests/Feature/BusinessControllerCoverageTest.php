<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use App\Services\GeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class BusinessControllerCoverageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_public_index_can_filter_by_category_sort_and_min_rating(): void
    {
        $category = Category::factory()->create(['slug' => 'hair']);
        $business = Business::factory()->create();
        $business->categories()->attach([$category->id]);

        $reviewer = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);
        Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $reviewer->id,
            'rating' => 4,
        ]);

        $response = $this->get(route('businesses.public.index', [
            'category' => 'hair',
            'sort' => 'rating_low',
            'min_rating' => 3.5,
        ]));

        $response->assertOk();
        $response->assertViewHas('businesses');
        $response->assertViewHas('categories');
    }

    public function test_public_index_can_sort_by_reviews_count(): void
    {
        $business = Business::factory()->create();
        $reviewer = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);
        Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $reviewer->id,
            'rating' => 5,
        ]);

        $response = $this->get(route('businesses.public.index', [
            'sort' => 'reviews_count',
        ]));

        $response->assertOk();
        $response->assertViewHas('businesses');
    }

    public function test_public_index_can_sort_by_rating_high_and_by_name(): void
    {
        $business = Business::factory()->create();
        $reviewer = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);
        Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $reviewer->id,
            'rating' => 5,
        ]);

        $this->get(route('businesses.public.index', ['sort' => 'rating_high']))
            ->assertOk()
            ->assertViewHas('businesses');

        $this->get(route('businesses.public.index', ['sort' => 'name']))
            ->assertOk()
            ->assertViewHas('businesses');
    }

    public function test_public_index_can_sort_by_best(): void
    {
        $business = Business::factory()->create();
        $reviewer = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);
        Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $reviewer->id,
            'rating' => 5,
        ]);

        $response = $this->get(route('businesses.public.index', ['sort' => 'best']));
        $response->assertOk();
        $response->assertViewHas('businesses');
    }

    public function test_show_returns_json_when_ajax_and_includes_pagination(): void
    {
        $business = Business::factory()->create();
        $client = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);

        Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $client->id,
            'rating' => 5,
        ]);

        $response = $this->actingAs($client)->getJson(route('businesses.show', $business->id), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'html',
            'pagination' => ['current_page', 'last_page', 'per_page', 'total', 'from', 'to'],
        ]);
    }

    public function test_show_includes_related_businesses_when_categories_present(): void
    {
        $category = Category::factory()->create();

        $business = Business::factory()->create();
        $business->categories()->attach([$category->id]);

        $related = Business::factory()->create();
        $related->categories()->attach([$category->id]);

        $reviewer = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);
        Review::factory()->create([
            'business_id' => $related->id,
            'user_id' => $reviewer->id,
            'rating' => 4,
        ]);

        $response = $this->get(route('businesses.show', $business->id));
        $response->assertOk();
        $response->assertViewHas('relatedBusinesses');
    }

    public function test_show_can_filter_reviews_by_rating_and_verified_booking_and_sort_by_rating_high(): void
    {
        $business = Business::factory()->create();
        $client = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);

        Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $client->id,
            'rating' => 5,
            'has_booking' => true,
        ]);

        $response = $this->actingAs($client)->get(route('businesses.show', $business->id, [
            'rating' => 5,
            'booking' => 'verified',
            'sort' => 'rating_high',
        ]));

        $response->assertOk();
        $response->assertViewHas('otherReviews');
    }

    public function test_show_helpful_sort_uses_votes_join_path(): void
    {
        $business = Business::factory()->create();
        $client = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);
        $other = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);

        // One review by "other" so it goes through the helpful join path.
        Review::factory()->create([
            'business_id' => $business->id,
            'user_id' => $other->id,
            'rating' => 4,
            'has_booking' => false,
        ]);

        $response = $this->actingAs($client)->get(route('businesses.show', $business->id, [
            'sort' => 'helpful',
        ]));

        $response->assertOk();
        $response->assertViewHas('otherReviews');
    }

    public function test_remove_logo_redirects_with_info_when_no_logo_exists(): void
    {
        $provider = User::factory()->create(['role' => 'provider', 'email_verified_at' => now()]);
        $business = Business::factory()->create(['user_id' => $provider->id, 'logo' => null]);

        $response = $this->actingAs($provider)->delete(route('businesses.remove-logo', $business->id));
        $response->assertRedirect(route('businesses.edit', $business->id));
        $response->assertSessionHas('info');
    }

    public function test_store_fails_when_geocoding_fails_and_no_coordinates_provided(): void
    {
        $provider = User::factory()->create(['role' => 'provider', 'email_verified_at' => now()]);

        $mockGeocoder = Mockery::mock(GeocodingService::class);
        $mockGeocoder->shouldReceive('geocode')->andReturn(null);
        $this->app->instance(GeocodingService::class, $mockGeocoder);

        $response = $this->actingAs($provider)->post(route('businesses.store'), [
            'name' => 'Geo Fail Biz',
            'description' => 'Desc',
            'address' => 'Unknown address',
            'phone_number' => '1234567890',
            'email' => 'test@biz.com',
            'categories' => [],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['address']);
        $this->assertDatabaseMissing('businesses', ['name' => 'Geo Fail Biz']);
    }

    public function test_store_uses_reverse_geocode_when_coordinates_provided(): void
    {
        $provider = User::factory()->create(['role' => 'provider', 'email_verified_at' => now()]);

        $mockGeocoder = Mockery::mock(GeocodingService::class);
        $mockGeocoder->shouldReceive('reverseGeocode')->andReturn([
            'formatted_address' => '1 Test Street, Test City',
        ]);
        $mockGeocoder->shouldNotReceive('geocode');
        $this->app->instance(GeocodingService::class, $mockGeocoder);

        $response = $this->actingAs($provider)->post(route('businesses.store'), [
            'name' => 'Geo OK Biz',
            'description' => 'Desc',
            'address' => 'Ignored',
            'phone_number' => '1234567890',
            'email' => 'test@biz.com',
            'latitude' => 47.0,
            'longitude' => 19.0,
            'categories' => [],
        ]);

        $response->assertRedirect(route('businesses.index'));
        $this->assertDatabaseHas('businesses', [
            'name' => 'Geo OK Biz',
            'address' => '1 Test Street, Test City',
        ]);
    }

    public function test_update_detaches_categories_when_missing_and_returns_error_when_geocoding_fails(): void
    {
        $provider = User::factory()->create(['role' => 'provider', 'email_verified_at' => now()]);
        $category = Category::factory()->create();

        $business = Business::factory()->create(['user_id' => $provider->id]);
        $business->categories()->attach([$category->id]);

        $mockGeocoder = Mockery::mock(GeocodingService::class);
        $mockGeocoder->shouldReceive('geocode')->andReturn([
            'latitude' => 40.0,
            'longitude' => -70.0,
            'formatted_address' => 'Updated Address',
        ]);
        $this->app->instance(GeocodingService::class, $mockGeocoder);

        $response = $this->actingAs($provider)->put(route('businesses.update', $business->id), [
            'name' => 'Updated Biz',
            'description' => 'Desc',
            'address' => 'Some address',
            'phone_number' => '1234567890',
            'email' => 'test@biz.com',
            // no categories key -> should detach
        ]);

        $response->assertRedirect(route('businesses.index'));
        $this->assertDatabaseMissing('business_category', [
            'business_id' => $business->id,
            'category_id' => $category->id,
        ]);

        $mockGeocoder2 = Mockery::mock(GeocodingService::class);
        $mockGeocoder2->shouldReceive('geocode')->andReturn(null);
        $this->app->instance(GeocodingService::class, $mockGeocoder2);

        $response2 = $this->actingAs($provider)->put(route('businesses.update', $business->id), [
            'name' => 'Updated Biz Again',
            'description' => 'Desc',
            'address' => 'Bad address',
            'phone_number' => '1234567890',
            'email' => 'test@biz.com',
        ]);

        $response2->assertStatus(302);
        $response2->assertSessionHasErrors(['address']);
    }
}

