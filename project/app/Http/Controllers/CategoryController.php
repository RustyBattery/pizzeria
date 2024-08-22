<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * @param BaseRequest $request
     * @return JsonResponse
     */
    public function index(BaseRequest $request): JsonResponse
    {
        $data = $request->validatedAsObject();
        $categories = Category::getAdvanced($data->filters, $data->search, $data->sort, $data->pagination);
        return response()->json(new CategoryCollection($categories));
    }
}
