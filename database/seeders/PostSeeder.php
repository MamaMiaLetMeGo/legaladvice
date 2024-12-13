<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first admin user (or create one if needed)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Get categories
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        $posts = [
            [
                'title' => 'Understanding Power of Attorney',
                'slug' => 'understanding-power-of-attorney',
                'body_content' => '<p>A power of attorney (POA) is a legal document that allows someone to make decisions on your behalf. This comprehensive guide explains the different types of POAs and their uses.</p>',
                'author_id' => $admin->id,
                'status' => 'published',
                'published_date' => now(),
                'categories' => ['power-of-attorney']
            ],
            [
                'title' => 'Essential Guide to Lease Agreements',
                'slug' => 'essential-guide-to-lease-agreements',
                'body_content' => '<p>Learn everything you need to know about lease agreements, including key terms, legal requirements, and common pitfalls to avoid.</p>',
                'author_id' => $admin->id,
                'status' => 'published',
                'published_date' => now()->subDays(1),
                'categories' => ['lease-agreements']
            ],
            [
                'title' => 'Real Estate Law Basics',
                'slug' => 'real-estate-law-basics',
                'body_content' => '<p>An introduction to real estate law covering property rights, transactions, and legal obligations for buyers and sellers.</p>',
                'author_id' => $admin->id,
                'status' => 'published',
                'published_date' => now()->subDays(2),
                'categories' => ['real-estate']
            ]
        ];

        foreach ($posts as $postData) {
            $categorySlugs = $postData['categories'];
            unset($postData['categories']);

            $post = Post::create($postData);

            // Attach categories
            $categoriesToAttach = Category::whereIn('slug', $categorySlugs)->get();
            $post->categories()->attach($categoriesToAttach);
        }
    }
}
