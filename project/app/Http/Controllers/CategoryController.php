<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(BaseRequest $request)
    {
        try {
            return response(['categories' => CategoryResource::collection(Category::get($request->validated()))], 200);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode() >= 200 && $e->getCode() <= 500 ? $e->getCode() : 400);
        }
    }
}
