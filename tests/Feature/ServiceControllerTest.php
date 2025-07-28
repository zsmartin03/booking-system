<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_index_displays_services_for_owner()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $this->actingAs($user);
        $response = $this->get(route('services.index', ['business_id' => $business->id]));
        $response->assertStatus(200);
        $response->assertViewIs('services.index');
        $response->assertViewHas('business', $business);
        $response->assertViewHas('services', function($services) use ($service) {
            return $services->contains($service);
        });
    }

    public function test_create_displays_create_form()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $employee = Employee::factory()->create(['business_id' => $business->id, 'active' => true]);
        $this->actingAs($user);
        $response = $this->get(route('services.create', ['business_id' => $business->id]));
        $response->assertStatus(200);
        $response->assertViewIs('services.create');
        $response->assertViewHas('business', $business);
        $response->assertViewHas('employees', function($employees) use ($employee) {
            return $employees->contains($employee);
        });
    }

    public function test_store_creates_service_and_assigns_employees()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $employee = Employee::factory()->create(['business_id' => $business->id, 'active' => true]);
        $this->actingAs($user);
        $data = [
            'name' => 'Test Service',
            'description' => 'A test service',
            'price' => 100,
            'duration' => 30,
            'active' => true,
            'employees' => [$employee->id],
        ];
        $response = $this->post(route('services.store', ['business_id' => $business->id]), $data);
        $response->assertRedirect(route('services.index', ['business_id' => $business->id]));
        $this->assertDatabaseHas('services', [
            'name' => 'Test Service',
            'business_id' => $business->id,
        ]);
        $service = Service::where('name', 'Test Service')->first();
        $this->assertTrue($service->employees->contains($employee));
    }

    public function test_store_fails_if_duration_not_divisible_by_5()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $employee = Employee::factory()->create(['business_id' => $business->id, 'active' => true]);
        $this->actingAs($user);
        $data = [
            'name' => 'Bad Service',
            'description' => 'Bad duration',
            'price' => 50,
            'duration' => 17,
            'active' => true,
            'employees' => [$employee->id],
        ];
        $response = $this->post(route('services.store', ['business_id' => $business->id]), $data);
        $response->assertSessionHasErrors('duration');
    }

    public function test_edit_displays_edit_form()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $employee = Employee::factory()->create(['business_id' => $business->id, 'active' => true]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $service->employees()->attach($employee);
        $this->actingAs($user);
        $response = $this->get(route('services.edit', $service->id));
        $response->assertStatus(200);
        $response->assertViewIs('services.edit');
        $response->assertViewHas('service', $service);
        $response->assertViewHas('business', $business);
        $response->assertViewHas('employees', function($employees) use ($employee) {
            return $employees->contains($employee);
        });
    }

    public function test_update_modifies_service_and_employees()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $employee1 = Employee::factory()->create(['business_id' => $business->id, 'active' => true]);
        $employee2 = Employee::factory()->create(['business_id' => $business->id, 'active' => true]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $service->employees()->attach($employee1);
        $this->actingAs($user);
        $data = [
            'name' => 'Updated Service',
            'description' => 'Updated desc',
            'price' => 200,
            'duration' => 60,
            'active' => false,
            'employees' => [$employee2->id],
        ];
        $response = $this->put(route('services.update', $service->id), $data);
        $response->assertRedirect(route('services.index', ['business_id' => $business->id]));
        $service->refresh();
        $this->assertEquals('Updated Service', $service->name);
        $this->assertTrue($service->employees->contains($employee2));
        $this->assertFalse($service->employees->contains($employee1));
    }

    public function test_update_fails_if_duration_not_divisible_by_5()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $employee = Employee::factory()->create(['business_id' => $business->id, 'active' => true]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $this->actingAs($user);
        $data = [
            'name' => 'Bad Update',
            'description' => 'Bad duration',
            'price' => 50,
            'duration' => 13,
            'active' => true,
            'employees' => [$employee->id],
        ];
        $response = $this->put(route('services.update', $service->id), $data);
        $response->assertSessionHasErrors('duration');
    }

    public function test_destroy_deletes_service()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $business = Business::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $this->actingAs($user);
        $response = $this->delete(route('services.destroy', $service->id));
        $response->assertRedirect(route('services.index', ['business_id' => $business->id]));
        $this->assertDatabaseMissing('services', [
            'id' => $service->id
        ]);
    }
}
