<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        foreach ($categories as $category){
            Product::factory(rand(15, 20))->for($category)->has(ProductInfo::factory(3), 'infos')->has(Image::factory(3), 'images')->create();
        }
    }
}
