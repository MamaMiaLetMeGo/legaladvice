<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Power of Attorney',
                'slug' => 'power-of-attorney',
                'description' => 'Lawyers with knowledge in regards to all that encompasses Power of Attorney including forms.',
                'is_featured' => true,
            ],
            [
                'name' => 'Lease Agreements',
                'slug' => 'lease-agreements',
                'description' => 'Recevie legal advice if you need help or assistence with drafting a lease agreement.',
                'is_featured' => true,
            ],
            [
                'name' => 'Real Estate',
                'slug' => 'real-estate',
                'description' => 'Receive legal advice if you need help or assistence with drafting a lease agreement.',
                'is_featured' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}