<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        try {
            return response(new ProductCollection(Product::paginate(10)), 200);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode() >= 200 && $e->getCode() <= 500 ? $e->getCode() : 400);
        }
    }

    public function get(Product $product)
    {
        try {
            return response(['product' => ProductResource::make($product)], 200);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode() >= 200 && $e->getCode() <= 500 ? $e->getCode() : 400);
        }
    }
}
