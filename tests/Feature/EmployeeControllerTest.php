<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function actingAsAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    private function actingAsBusinessProvider($business = null)
    {
        $provider = User::factory()->create(['role' => 'provider']);
        if ($business) {
            $business->user_id = $provider->id;
            $business->save();
        }
        $this->actingAs($provider);
        return $provider;
    }

    public function test_index_lists_employees_for_admin_and_owner()
    {
        $business = Business::factory()->create();
        $employees = Employee::factory()->count(2)->create(['business_id' => $business->id]);

        // Admin can view
        $admin = $this->actingAsAdmin();
        $response = $this->get(route('employees.index', ['business_id' => $business->id]));
        $response->assertStatus(200);
        foreach ($employees as $employee) {
            $response->assertSee($employee->name);
        }

        // Owner can view
        $provider = $this->actingAsBusinessProvider($business);
        $response = $this->get(route('employees.index', ['business_id' => $business->id]));
        $response->assertStatus(200);
        foreach ($employees as $employee) {
            $response->assertSee($employee->name);
        }
    }

    public function test_create_shows_form_for_admin_and_owner()
    {
        $business = Business::factory()->create();
        $admin = $this->actingAsAdmin();
        $response = $this->get(route('employees.create', ['business_id' => $business->id]));
        $response->assertStatus(200);
        $this->actingAsBusinessProvider($business);
        $response = $this->get(route('employees.create', ['business_id' => $business->id]));
        $response->assertStatus(200);
    }

    public function test_store_creates_employee_with_avatar()
    {
        $business = Business::factory()->create();
        $this->actingAsBusinessProvider($business);
        $file = UploadedFile::fake()->image('avatar.jpg');
        $data = [
            'name' => 'Test Employee',
            'email' => 'employee@example.com',
            'bio' => 'Bio',
            'avatar' => $file,
            'active' => true,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_id' => $business->id,
        ];
        $response = $this->post(route('employees.store'), $data);
        $response->assertRedirect(route('employees.index', ['business_id' => $business->id]));
        $this->assertDatabaseHas('employees', ['name' => 'Test Employee', 'business_id' => $business->id]);
        $this->assertTrue(Storage::disk('public')->exists('avatars/' . $file->hashName()));
    }

    public function test_edit_shows_form_for_admin_and_owner()
    {
        $business = Business::factory()->create();
        $employee = Employee::factory()->create(['business_id' => $business->id]);
        $admin = $this->actingAsAdmin();
        $response = $this->get(route('employees.edit', $employee->id));
        $response->assertStatus(200);
        $this->actingAsBusinessProvider($business);
        $response = $this->get(route('employees.edit', $employee->id));
        $response->assertStatus(200);
    }

    public function test_update_modifies_employee_and_avatar()
    {
        $business = Business::factory()->create();
        $employee = Employee::factory()->create(['business_id' => $business->id]);
        $this->actingAsBusinessProvider($business);
        $file = UploadedFile::fake()->image('avatar2.jpg');
        $data = [
            'name' => 'Updated Name',
            'email' => $employee->user->email,
            'bio' => 'Updated bio',
            'avatar' => $file,
            'active' => false,
        ];
        $response = $this->put(route('employees.update', $employee->id), $data);
        $response->assertRedirect(route('employees.index', ['business_id' => $business->id]));
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'name' => 'Updated Name']);
        $this->assertTrue(Storage::disk('public')->exists('avatars/' . $file->hashName()));
    }

    public function test_destroy_deletes_employee()
    {
        $business = Business::factory()->create();
        $employee = Employee::factory()->create(['business_id' => $business->id]);
        $this->actingAsBusinessProvider($business);
        $response = $this->delete(route('employees.destroy', $employee->id));
        $response->assertRedirect(route('employees.index', ['business_id' => $business->id]));
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_non_owner_cannot_access_other_business_employees()
    {
        $business = Business::factory()->create();
        $employee = Employee::factory()->create(['business_id' => $business->id]);
        $otherProvider = User::factory()->create(['role' => 'provider']);
        $this->actingAs($otherProvider);
        $response = $this->get(route('employees.index', ['business_id' => $business->id]));
        $response->assertStatus(404);
        $response = $this->get(route('employees.create', ['business_id' => $business->id]));
        $response->assertStatus(404);
        $response = $this->get(route('employees.edit', $employee->id));
        $response->assertStatus(404);
        $response = $this->put(route('employees.update', $employee->id), [
            'name' => 'X', 'email' => $employee->user->email
        ]);
        $response->assertStatus(404);
        $response = $this->delete(route('employees.destroy', $employee->id));
        $response->assertStatus(404);
    }
}
