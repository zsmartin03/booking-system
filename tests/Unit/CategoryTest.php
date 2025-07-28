<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships()
    {
        $category = Category::factory()->create();
        $business = Business::factory()->create();
        $category->businesses()->attach($business);
        $this->assertTrue($category->businesses->contains($business));
    }

    public function test_name_and_description_accessors()
    {
        $category = Category::factory()->create(['slug' => 'test-category']);
        $this->assertIsString($category->name);
        $this->assertIsString($category->translated_description);
    }

    public function test_has_translations()
    {
        $category = Category::factory()->create(['slug' => 'test-category']);
        $this->assertIsBool($category->hasTranslations());
    }

    public function test_badge_style_and_css_accessors()
    {
        $category = Category::factory()->create(['color' => '#FF0000']);
        $this->assertIsArray($category->badge_style);
        $this->assertIsString($category->badge_css);
        $this->assertIsString($category->badge_hover_css);
        $this->assertIsArray($category->tailwind_classes);
        $this->assertIsString($category->badge_classes);
        $this->assertIsString($category->badge_styles);
        $this->assertIsString($category->badge_hover_styles);
    }
}
