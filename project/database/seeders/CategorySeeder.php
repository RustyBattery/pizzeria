<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Image;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $image = Image::factory(1)->create()[0];
        Category::create(['name' => 'Pizza', 'limit' => 10, 'image_id' => $image->id]);
        Category::create(['name' => 'Drink', 'limit' => 20]);
    }
}
