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
                'name' => 'Beauty & Wellness',
                'slug' => 'beauty-wellness',
                'description' => 'Hair salons, spas, beauty treatments, massage therapy',
                'color' => '#EC4899'
            ],
            [
                'name' => 'Health & Medical',
                'slug' => 'health-medical',
                'description' => 'Medical practices, clinics, dental offices, therapy',
                'color' => '#10B981'
            ],
            [
                'name' => 'Automotive',
                'slug' => 'automotive',
                'description' => 'Car services, repair shops, maintenance',
                'color' => '#F59E0B'
            ],
            [
                'name' => 'Food & Dining',
                'slug' => 'food-dining',
                'description' => 'Restaurants, cafes, catering services',
                'color' => '#EF4444'
            ],
            [
                'name' => 'Education & Training',
                'slug' => 'education-training',
                'description' => 'Tutoring, courses, workshops, training sessions',
                'color' => '#8B5CF6'
            ],
            [
                'name' => 'Professional Services',
                'slug' => 'professional-services',
                'description' => 'Legal, accounting, consulting, business services',
                'color' => '#3B82F6'
            ],
            [
                'name' => 'Home & Maintenance',
                'slug' => 'home-maintenance',
                'description' => 'Cleaning, repair, maintenance, landscaping',
                'color' => '#059669'
            ],
            [
                'name' => 'Fitness & Sports',
                'slug' => 'fitness-sports',
                'description' => 'Gyms, personal training, sports facilities',
                'color' => '#DC2626'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
