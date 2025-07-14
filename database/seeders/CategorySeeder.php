<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'beauty-wellness',
                'color' => '#EC4899'
            ],
            [
                'slug' => 'health-medical',
                'color' => '#10B981'
            ],
            [
                'slug' => 'automotive',
                'color' => '#F59E0B'
            ],
            [
                'slug' => 'food-dining',
                'color' => '#EF4444'
            ],
            [
                'slug' => 'education-training',
                'color' => '#8B5CF6'
            ],
            [
                'slug' => 'professional-services',
                'color' => '#3B82F6'
            ],
            [
                'slug' => 'home-maintenance',
                'color' => '#059669'
            ],
            [
                'slug' => 'fitness-sports',
                'color' => '#DC2626'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
