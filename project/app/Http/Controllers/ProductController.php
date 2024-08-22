<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * @param BaseRequest $request
     * @return JsonResponse
     */
    public function index(BaseRequest $request): JsonResponse
    {
        $data = $request->validatedAsObject();
        $products = Product::getAdvanced($data->filters, $data->search, $data->sort, $data->pagination);
        return response()->json(new ProductCollection($products));
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function get(Product $product): JsonResponse
    {
        return response()->json(['product' => ProductResource::make($product)]);
    }
}
