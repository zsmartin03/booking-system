<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceControllerCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_rejects_duration_not_divisible_by_five(): void
    {
        $provider = User::factory()->create([
            'role' => 'provider',
            'email_verified_at' => now(),
        ]);
        $business = Business::factory()->create(['user_id' => $provider->id]);

        $response = $this->actingAs($provider)->post(route('services.store'), [
            'business_id' => $business->id,
            'name' => 'Test Service',
            'description' => 'Test description',
            'price' => 10,
            'duration' => 7,
            'active' => true,
            'employees' => [],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['duration']);
        $this->assertDatabaseMissing('services', ['business_id' => $business->id, 'name' => 'Test Service']);
    }

    public function test_update_without_employees_detaches_existing_assignments(): void
    {
        $provider = User::factory()->create([
            'role' => 'provider',
            'email_verified_at' => now(),
        ]);
        $business = Business::factory()->create(['user_id' => $provider->id]);
        $employee = Employee::factory()->create([
            'business_id' => $business->id,
            'active' => true,
        ]);
        $service = Service::factory()->create([
            'business_id' => $business->id,
            'duration' => 30,
        ]);

        $service->employees()->sync([$employee->id]);
        $this->assertDatabaseHas('service_employee', [
            'service_id' => $service->id,
            'employee_id' => $employee->id,
        ]);

        $response = $this->actingAs($provider)->put(route('services.update', $service->id), [
            'name' => 'Updated Service',
            'description' => 'Updated description',
            'price' => 20,
            'duration' => 30,
            'active' => true,
            // no employees key -> should detach
        ]);

        $response->assertRedirect(route('services.index', ['business_id' => $business->id]));
        $this->assertDatabaseMissing('service_employee', [
            'service_id' => $service->id,
            'employee_id' => $employee->id,
        ]);
    }
}

