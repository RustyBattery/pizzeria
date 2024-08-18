<?php

namespace App\Models;

use App\DTO\CartDTO;
use App\DTO\OrderInfoDTO;
use App\Exceptions\Cart\CartProductLimitException;
use App\Exceptions\Cart\CartProductOutStockException;
use App\Exceptions\Cart\DuplicateCartProductException;
use App\Http\Controllers\OrderController;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property Collection|Product[] $cart
 * @property Collection|Order[] $orders
 * @property Collection|UserAddress[] $addresses
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property string $is_admin
 * @method static where(string $string, false $false)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cart(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'carts')
            ->withPivot('count')
            ->with('category')
            ->withTimestamps();
    }

    /**
     * @return CartDTO
     */
    public function getCart(): CartDTO
    {
        return new CartDTO($this, $this->cart, $this->getCostCart(true), $this->getCountProductsInCart());
    }

    /**
     * @param Product $product
     * @return void
     * @throws CartProductLimitException
     * @throws CartProductOutStockException
     * @throws DuplicateCartProductException
     */
    public function addProductToCart(Product $product): void
    {
        if ($this->cart()->find($product->id)) {
            throw new DuplicateCartProductException();
        }
        if (!$product->in_stock) {
            throw new CartProductOutStockException();
        }
        $count_info = $this->getCountProductsInCart($product->category);
        if ($count_info->category->limit - $count_info->in_stock < 1) {
            throw new CartProductLimitException();
        }
        $this->cart()->attach($product->id);
    }

    /**
     * @param Product $product
     * @return void
     */
    public function removeProductFromCart(Product $product): void
    {
        $this->cart()->detach($product->id);
    }

    /**
     * @param Product $product
     * @param int $count
     * @return void
     * @throws CartProductLimitException
     * @throws CartProductOutStockException
     */
    public function changeProductCountInCart(Product $product, int $count): void
    {
        if (!$product->in_stock) {
            throw new CartProductOutStockException();
        }

        $info_cart_count = $this->getCountProductsInCart($product->category);

        $cur_count = $this->cart()->find($product->id)?->pivot->count ?? 0;
        $max_count = $info_cart_count->category->limit - ($info_cart_count->in_stock - $cur_count);
        if ($count > $max_count) {
            throw new CartProductLimitException();
        }
        $this->cart()->syncWithoutDetaching([$product->id => ['count' => $count]]);
    }

    /**
     * @param Category|null $category
     * @return object{category: object{id:int, name: string, limit: int}, in_stock: int, out_stock: int, total: int}|object[]
     */
    private function getCountProductsInCart(Category $category = null): object|array
    {
        if ($category) {
            $cart = $this->cart()->where('category_id', $category->id)->get();
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
        $cart_groups = $this->cart->groupBy('category');
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
     * @param bool $in_rubles
     * @return float|int
     */
    private function getCostCart(bool $in_rubles = false): float|int
    {
        $cart = $this->cart()->where('in_stock', true)->get();
        $cost = $cart->map(function ($product) {
            return $product->price * $product->pivot->count;
        })->sum();
        return $in_rubles ? bcdiv($cost / 100, 1, 2) : $cost;
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->with(['products', 'address']);
    }

    /**
     * @param OrderInfoDTO $order_info
     * @return void
     */
    public function createOrder(OrderInfoDTO $order_info): void
    {
        /** @var Order $order */
        $order = $this->orders()->create([
            'address_id' => $order_info->address_id,
            'phone' => $order_info->phone,
            'cost' => $this->getCostCart(),
            'email' => $order_info->email,
            'delivery_time' => $order_info->delivery_time,
        ]);
        $products = $this->cart()->where('in_stock', true)->get();
        foreach ($products as $product) {
            $order->products()->attach($product->id, ['price' => $product->price, 'count' => $product->pivot->count]);
            $this->cart()->detach($product->id);
        }
    }
}
