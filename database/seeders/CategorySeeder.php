<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'description' => 'Latest tech news and updates'
            ],
            [
                'name' => 'Business',
                'description' => 'Business and finance insights'
            ],
            [
                'name' => 'Lifestyle',
                'description' => 'Tips for better living'
            ],
            [
                'name' => 'Travel',
                'description' => 'Travel guides and experiences'
            ],
            [
                'name' => 'Health & Wellness',
                'description' => 'Health tips and wellness advice'
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'slug' => \Str::slug($category['name'])
                ]
            );
        }
    }
}