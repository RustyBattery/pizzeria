<?php

namespace App\Services;

use App\DTO\CartDTO;
use App\Exceptions\Cart\CartProductLimitException;
use App\Exceptions\Cart\CartProductOutStockException;
use App\Exceptions\Cart\DuplicateCartProductException;
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
     * @param Product $product
     * @return void
     * @throws CartProductLimitException
     * @throws CartProductOutStockException
     * @throws DuplicateCartProductException
     */
    public function addProductToCart(User $user, Product $product): void
    {
        if ($user->cart()->find($product->id)) {
            throw new DuplicateCartProductException();
        }
        if (!$product->in_stock) {
            throw new CartProductOutStockException();
        }
        $count_info = $this->getCountProductsInCart($user, $product->category);
        if ($count_info->category->limit - $count_info->in_stock < 1) {
            throw new CartProductLimitException();
        }
        $user->cart()->attach($product->id);
    }

    /**
     * @param User $user
     * @param Product $product
     * @return void
     */
    public function removeProductFromCart(User $user, Product $product): void
    {
        $user->cart()->detach($product->id);
    }

    /**
     * @param User $user
     * @param Product $product
     * @param int $count
     * @return void
     * @throws CartProductLimitException
     * @throws CartProductOutStockException
     */
    public function changeProductCountInCart(User $user, Product $product, int $count): void
    {
        if (!$product->in_stock) {
            throw new CartProductOutStockException();
        }

        $info_cart_count = $this->getCountProductsInCart($user, $product->category);

        $cur_count = $user->cart()->find($product->id)?->pivot->count ?? 0;
        $max_count = $info_cart_count->category->limit - ($info_cart_count->in_stock - $cur_count);
        if ($count > $max_count) {
            throw new CartProductLimitException();
        }
        $user->cart()->syncWithoutDetaching([$product->id => ['count' => $count]]);
    }


    /**
     * @param User $user
     * @param Category|null $category
     * @return object{category: object{id:int, name: string, limit: int}, in_stock: int, out_stock: int, total: int}|object[]
     */
    private function getCountProductsInCart(User $user, Category $category = null): object|array
    {
        if ($category) {
            $cart = $user->cart()->where('category_id', $category->id)->get();
            [$products_in_stock, $products_out_stock] = $cart->partition(function (Product $product) {
                return $product->in_stock;
            });
            return (object)[
                'category' => (object)[
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
