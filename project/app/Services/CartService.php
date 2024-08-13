<?php

namespace App\Services;

use App\DTO\CartDTO;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class CartService
{
    /**
     * @param User $user
     * @return CartDTO
     */
    public function getCart(User $user): CartDTO
    {
        return new CartDTO($user, $user->cart, $this->getCostCart($user), $this->getCountProductsInCart($user));
    }


    /**
     * @param User $user
     * @param Category|null $category
     * @return object|object[]
     */
    private function getCountProductsInCart(User $user, Category $category = null): object|array
    {
        if ($category) {
            $cart = $user->cart()->where('category_id', $category->id)->get();
            [$products_in_stock, $products_out_stock] = $cart->partition(function (Product $product) {
                return $product->in_stock;
            });
            return (object)[
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'limit' => $category->limit,
                ],
                'in_stock' => $products_in_stock->sum('pivot.count'),
                'out_stock' => $products_out_stock->sum('pivot.count'),
                'total' => $products_in_stock->sum('pivot.count') + $products_out_stock->sum('pivot.count')
            ];
        }
        $result = [];
        $cart_groups = $user->cart->groupBy('category');
        foreach ($cart_groups as $key => $cart_group) {
            [$products_in_stock, $products_out_stock] = $cart_group->partition(function (Product $product) {
                return $product->in_stock;
            });
            try {
                $category = json_decode($key, false, 512, JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
                Log::info($e->getMessage(), ['exception' => $e]);
                continue;
            }
            $result[] = (object)[
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'limit' => $category->limit,
                ],
                'in_stock' => $products_in_stock->sum('pivot.count'),
                'out_stock' => $products_out_stock->sum('pivot.count'),
                'total' => $products_in_stock->sum('pivot.count') + $products_out_stock->sum('pivot.count')
            ];
        }
        return $result;
    }

    /**
     * @param User $user
     * @param bool $in_rubles
     * @return float|int
     */
    private function getCostCart(User $user, bool $in_rubles = false): float|int
    {
        $cart = $user->cart()->where('in_stock', true)->get();
        $cost = $cart->map(function ($product) {
            return $product->price * $product->pivot->count;
        })->sum();
        return $in_rubles ? bcdiv($cost / 100, 1, 2) : $cost;
    }

}
