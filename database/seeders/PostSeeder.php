<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryPost;
use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::all();
        Post::factory(10000)->create()->each(function($post) use($categories) {
            $randomCategories = $categories->random(rand(1,3));
            foreach ($randomCategories as $category) {
                CategoryPost::create([
                    'post_id' => $post->id,
                    'category_id' => $category->id,
                ]);
            }
        });
    }
}
