<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user for authentication
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'client']);
    }

    public function test_admin_can_view_category_index()
    {
        $response = $this->actingAs($this->admin)->get(route('categories.index'));
        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_view_category_index()
    {
        $response = $this->actingAs($this->user)->get(route('categories.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_create_form()
    {
        $response = $this->actingAs($this->admin)->get(route('categories.create'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_category()
    {
        $data = [
            'slug' => 'test-category',
            'name_en' => 'Test Category',
            'name_hu' => 'Teszt Kategória',
            'description_en' => 'English description',
            'description_hu' => 'Magyar leírás',
            'color' => '#123ABC',
        ];
        $response = $this->actingAs($this->admin)->post(route('categories.store'), $data);
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'slug' => 'test-category',
            'color' => '#123ABC',
        ]);
    }

    public function test_non_admin_cannot_create_category()
    {
        $data = [
            'slug' => 'test-category',
            'name_en' => 'Test Category',
            'name_hu' => 'Teszt Kategória',
            'description_en' => 'English description',
            'description_hu' => 'Magyar leírás',
            'color' => '#123ABC',
        ];
        $response = $this->actingAs($this->user)->post(route('categories.store'), $data);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('categories', [
            'slug' => 'test-category',
        ]);
    }

    public function test_admin_can_edit_category()
    {
        $category = Category::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('categories.edit', $category->id));
        $response->assertStatus(200);
    }

    public function test_admin_can_update_category()
    {
        $category = Category::factory()->create([
            'slug' => 'old-slug',
            'color' => '#000000',
        ]);
        $data = [
            'slug' => 'new-slug',
            'name_en' => 'Updated EN',
            'name_hu' => 'Frissített HU',
            'description_en' => 'Updated desc EN',
            'description_hu' => 'Frissített leírás HU',
            'color' => '#FFFFFF',
        ];
        $response = $this->actingAs($this->admin)->put(route('categories.update', $category->id), $data);
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'slug' => 'new-slug',
            'color' => '#FFFFFF',
        ]);
    }

    public function test_admin_can_delete_category_without_businesses()
    {
        $category = Category::factory()->create();
        $response = $this->actingAs($this->admin)->delete(route('categories.destroy', $category->id));
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_cannot_delete_category_with_businesses()
    {
        $category = Category::factory()->create();
        $business = \App\Models\Business::factory()->create();
        $category->businesses()->attach($business->id);
        $response = $this->actingAs($this->admin)->delete(route('categories.destroy', $category->id));
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }
}
