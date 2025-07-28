<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessWorkingHour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessWorkingHourControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $provider;
    protected $admin;
    protected $business;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = User::factory()->create(['role' => 'provider']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->business = Business::factory()->create(['user_id' => $this->provider->id]);
    }

    public function test_index_requires_owner_or_admin()
    {
        $user = User::factory()->create(['role' => 'client']);
        $response = $this->actingAs($user)->get(route('business-working-hours.index', ['business_id' => $this->business->id]));
        $response->assertForbidden();
    }

    public function test_index_shows_working_hours_for_owner()
    {
        $hour = BusinessWorkingHour::create([
            'business_id' => $this->business->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ]);
        $response = $this->actingAs($this->provider)->get(route('business-working-hours.index', ['business_id' => $this->business->id]));
        $response->assertOk();
        $response->assertViewHas('workingHours');
    }


    public function test_store_creates_working_hour()
    {
        $data = [
            'business_id' => $this->business->id,
            'day_of_week' => 'tuesday',
            'start_time' => '10:00',
            'end_time' => '18:00',
        ];
        $response = $this->actingAs($this->provider)->post(route('business-working-hours.store'), $data);
        $response->assertRedirect(route('business-working-hours.index', ['business_id' => $this->business->id]));
        $this->assertDatabaseHas('business_working_hours', [
            'business_id' => $this->business->id,
            'day_of_week' => 'tuesday',
        ]);
    }


    public function test_update_updates_working_hour()
    {
        $hour = BusinessWorkingHour::create([
            'business_id' => $this->business->id,
            'day_of_week' => 'friday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ]);
        $data = [
            'day_of_week' => 'friday',
            'start_time' => '10:00',
            'end_time' => '18:00',
        ];
        $response = $this->actingAs($this->provider)->put(route('business-working-hours.update', $hour->id), $data);
        $response->assertRedirect(route('business-working-hours.index', ['business_id' => $this->business->id]));
        $this->assertDatabaseHas('business_working_hours', [
            'id' => $hour->id,
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
        ]);
    }

    public function test_destroy_deletes_working_hour()
    {
        $hour = BusinessWorkingHour::create([
            'business_id' => $this->business->id,
            'day_of_week' => 'saturday',
            'start_time' => '11:00:00',
            'end_time' => '19:00:00',
        ]);
        $response = $this->actingAs($this->provider)->delete(route('business-working-hours.destroy', $hour->id));
        $response->assertRedirect(route('business-working-hours.index', ['business_id' => $this->business->id]));
        $this->assertDatabaseMissing('business_working_hours', [
            'id' => $hour->id,
        ]);
    }

    public function test_bulk_update_validates_and_updates()
    {
        $employee = \App\Models\Employee::factory()->create(['business_id' => $this->business->id]);
        $data = [
            'business_id' => $this->business->id,
            'working_hours' => [
                'monday' => ['enabled' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
                'tuesday' => ['enabled' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
                'wednesday' => ['enabled' => false],
                'thursday' => ['enabled' => false],
                'friday' => ['enabled' => false],
                'saturday' => ['enabled' => false],
                'sunday' => ['enabled' => false],
            ],
        ];
        $response = $this->actingAs($this->provider)->post(route('business-working-hours.bulk-update'), $data);
        $response->assertRedirect(route('business-working-hours.index', ['business_id' => $this->business->id]));
        $this->assertDatabaseHas('business_working_hours', [
            'business_id' => $this->business->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ]);
    }
}
