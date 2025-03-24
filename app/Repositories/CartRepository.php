<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Class CartRepository
 * @package App\Repositories
 */
class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    protected $model;

    public function __construct(
        Cart $model
    ) {
        $this->model = $model;
    }

    public function findByCustomerId($customerId)
    {
        return $this->model->where('customer_id', $customerId)->first();
    }
    public function getCartProductInfo($user, $productId, $uuid)
    {
        $user = Auth::guard('customer')->user();

        // Lấy user ID, nếu không phải là đối tượng thì lấy ID từ user
        $customerId = is_object($user) ? $user->id : $user;

        // Truy vấn giỏ hàng và sản phẩm liên kết với giỏ hàng
        $result = DB::table('carts')
            ->leftJoin('cart_product', function ($join) use ($productId, $uuid) {
                $join->on('carts.id', '=', 'cart_product.cart_id')
                    ->where('cart_product.product_id', '=', $productId);

                // Kiểm tra điều kiện uuid
                if (is_null($uuid) || $uuid === '') {
                    $join->where(function ($query) {
                        $query->whereNull('cart_product.uuid')
                            ->orWhere('cart_product.uuid', '');
                    });
                } else {
                    $join->where('cart_product.uuid', '=', $uuid);
                }
            })
            ->where('carts.customer_id', $user->id)
            ->select(
                'carts.id as cart_id',
                'carts.customer_id',
                'cart_product.product_id',
                'cart_product.uuid',
                'cart_product.qty',
                'cart_product.price'
            )
            ->first();

        // Kiểm tra nếu không có kết quả
        if (!$result) {
            Log::warning("Không tìm thấy giỏ hàng hoặc sản phẩm cho user_id: {$customerId}");
            return ['cart' => null, 'product' => null];
        }

        // Tạo đối tượng cart
        $cart = (object) [
            'id'          => $result->cart_id,
            'customer_id' => $result->customer_id
        ];

        // Tạo đối tượng product nếu có sản phẩm
        $product = $result->product_id ? (object) [
            'product_id' => $result->product_id,
            'uuid'       => $result->uuid,
            'qty'        => $result->qty,
            'price'      => $result->price
        ] : null;

        // Log thông tin
        Log::info('Thông tin giỏ hàng:', ['cart' => $cart]);
        Log::info('Thông tin sản phẩm trong giỏ hàng:', ['product' => $product]);

        return [
            'cart' => $cart,
            'product' => $product
        ];
    }


    public function getQuantityByUserProductVariant($userId, $productId, $variantId = null)
    {
        $query = DB::table('cart_product')
            ->join('carts', 'carts.id', '=', 'cart_product.cart_id')
            ->where('carts.customer_id', $userId)
            ->where('cart_product.product_id', $productId);

        if ($variantId !== null) {
            $query->where('cart_product.variant_id', $variantId);
        } else {
            $query->whereNull('cart_product.variant_id');
        }

        $quantity = $query->sum('cart_product.qty');

        return $quantity;
    }



    public function create(array $payload = [])
    {
        Log::info("INFO", ['INFORCAART' => $payload]);
        $model = $this->model->create($payload);
        return $model->fresh();
    }
}
