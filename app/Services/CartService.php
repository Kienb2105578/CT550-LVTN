<?php

namespace App\Services;

use App\Services\Interfaces\CartServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Repositories\Interfaces\CartRepositoryInterface as CartRepository;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface  as ProductVariantRepository;
use Cart;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductVariant;
use App\Models\Product;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class CartService  implements CartServiceInterface
{

    protected $productRepository;
    protected $productVariantRepository;
    protected $promotionRepository;
    protected $orderRepository;
    protected $productService;
    protected $cartRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
        OrderRepository $orderRepository,
        ProductService $productService,
        CartRepository $cartRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
        $this->orderRepository = $orderRepository;
        $this->productService = $productService;
        $this->cartRepository = $cartRepository;
    }

    public function saveCartToDatabase($carts)
    {
        if ($carts->isEmpty()) {
            return;
        }

        DB::beginTransaction();
        try {
            $user = Auth::guard('customer')->user();
            $cart = $this->cartRepository->findByCustomerId($user->id);

            // Nếu giỏ hàng đã tồn tại thì xóa cứng luôn
            if ($cart) {
                DB::table('carts')->where('id', $cart->id)->delete();
                $cart = null;
            }

            // Tạo giỏ hàng mới
            $payload['customer_id'] = $user->id;
            $cart = $this->cartRepository->create($payload);

            foreach ($carts as $cartItem) {
                // Tách product_id và uuid từ cartItem->id
                $idParts = explode('_', $cartItem->id);
                $productId = $idParts[0] ?? null;
                $uuid = $idParts[1] ?? '';
                $variantId = $cartItem->options['variant_id'] ?? null;

                // Đảm bảo không có giá trị null
                if (!$productId) {
                    Log::error("Lỗi: Không thể xác định product_id từ rowId: " . $cartItem->id);
                    continue; // Bỏ qua sản phẩm lỗi
                }

                // Thêm mới sản phẩm vào giỏ hàng
                DB::table('cart_product')->insert([
                    'cart_id'    => $cart->id,
                    'product_id' => $productId,
                    'uuid'       => $uuid,
                    'variant_id' => $variantId, // Thêm variant_id
                    'name'       => $cartItem->name,
                    'qty'        => $cartItem->qty,
                    'price'      => $cartItem->price,
                    'option'     => json_encode($cartItem->options),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Ghi log kiểm tra
                Log::info('Sản phẩm đã được thêm mới vào giỏ hàng:', [
                    'cart_id'    => $cart->id,
                    'product_id' => $productId,
                    'uuid'       => $uuid,
                    'variant_id' => $variantId,
                ]);
                Cart::instance('shopping')->destroy();
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi lưu giỏ hàng:', [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
            ]);
        }
    }


    public function createCartToDatabase($data)
    {
        DB::beginTransaction();
        try {
            Log::info('Dữ liệu nhận DATA:', $data);
            $user = Auth::guard('customer')->user();
            $cartProductInfo = $this->cartRepository->getCartProductInfo($user, $data['product_id'], $data['uuid']);
            $cart = $cartProductInfo['cart'] ?? $this->cartRepository->create(['customer_id' => $user->id]);
            $existingProduct = $cartProductInfo['product'];
            $uuid = $data['uuid'] ?? null;
            $variantId = $data['variant_id'] ?? null;
            Log::info($variantId);

            if ($existingProduct) {
                $newQty = $existingProduct->qty + $data['qty'];
                DB::table('cart_product')
                    ->where('cart_id', $cart->id)
                    ->where('product_id', $data['product_id'])
                    ->where(function ($query) use ($uuid) {
                        if (empty($uuid)) {
                            $query->whereNull('uuid')
                                ->orWhere('uuid', '');
                        } else {
                            $query->where('uuid', $uuid);
                        }
                    })
                    ->update([
                        'qty'        => $newQty,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('cart_product')->insert([
                    'cart_id'    => $cart->id,
                    'product_id' => $data['product_id'],
                    'variant_id' => $variantId,
                    'uuid'       => $uuid,
                    'name'       => $data['name'],
                    'qty'        => $data['qty'],
                    'price'      => $data['price'],
                    'option'     => isset($data['options']) ? json_encode($data['options']) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi lưu giỏ hàng vào database: ' . $e->getMessage());
            return false;
        }
    }



    public function createCart($request)
    {
        try {
            $payload = $request->input();
            $user = Auth::guard('customer')->user();

            if ($user) {
                $product = $this->productRepository->findById($payload['id'], ['*'], []);

                $data = [
                    'product_id' => $product->id,
                    'name'       => $product->name,
                    'qty'        => $payload['quantity'],
                ];

                if (isset($payload['attribute_id']) && count($payload['attribute_id'])) {
                    $attributeId = sortAttributeId($payload['attribute_id']);
                    $variant = $this->productVariantRepository->findVariant($attributeId, $product->id);
                    $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
                    $variantPrice = getVariantPrice($variant, $variantPromotion);

                    $data['uuid']  = $variant->uuid;
                    $data['variant_id'] = $variant->id;
                    $data['name']  = $product->name . ' ' . $variant->name;
                    $data['price'] = ($variantPrice['priceSale'] > 0) ? $variantPrice['priceSale'] : $variantPrice['price'];
                    $data['options'] = [
                        'attribute' => $payload['attribute_id'],
                    ];
                } else {
                    $product = $this->productService->combineProductAndPromotion([$product->id], $product, true);
                    $price = getPrice($product);
                    $data['uuid'] = null;
                    $data['variant_id'] = null;
                    $data['price'] = ($price['priceSale'] > 0) ? $price['priceSale'] : $price['price'];
                }


                $this->createCartToDatabase($data);
            } else {
                $product = $this->productRepository->findById($payload['id'], ['*'], []);
                $data = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'qty' => $payload['quantity'],
                ];
                if (isset($payload['attribute_id']) && count($payload['attribute_id'])) {
                    $attributeId = sortAttributeId($payload['attribute_id']);
                    $variant = $this->productVariantRepository->findVariant($attributeId, $product->id);
                    Log::info("SESSION", ['variant', $variant]);
                    $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
                    $variantPrice = getVariantPrice($variant, $variantPromotion);

                    $data['id'] =  $product->id . '_' . $variant->uuid;
                    $data['variant_id'] = $variant->id;
                    $data['name'] = $product->name . ' ' . $variant->name;
                    $data['price'] = ($variantPrice['priceSale'] > 0) ? $variantPrice['priceSale'] : $variantPrice['price'];
                    $data['options'] = [
                        'attribute' => $payload['attribute_id'],
                        'variant_id' => $variant->id,
                    ];
                } else {
                    $product = $this->productService->combineProductAndPromotion([$product->id], $product, true);
                    $price = getPrice($product);
                    $data['variant_id'] = null;
                    $data['price'] = ($price['priceSale'] > 0) ? $price['priceSale'] : $price['price'];
                }
                Log::info("DATTA", ['DTA', $data]);

                Cart::instance('shopping')->add($data);
                Log::info("Add cartsession: ", ['cart_content' => Cart::instance('shopping')->content()]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Lỗi khi tạo giỏ hàng: " . $e->getMessage(), ['code' => $e->getCode()]);
            die();
            return false;
        }
    }



    public function updateCart($request)
    {
        try {
            $payload = $request->input();
            $user = Auth::guard('customer')->user();

            if ($user) {
                $rowIdParts = explode('_', $payload['rowId']);
                if (count($rowIdParts) == 3) {
                    list($cartId, $productId, $uuid) = $rowIdParts;
                    DB::table('cart_product')
                        ->where('cart_id', $cartId)
                        ->where('product_id', $productId)
                        ->where('uuid', $uuid)
                        ->update(['qty' => $payload['qty']]);
                } elseif (count($rowIdParts) == 2) {
                    list($cartId, $productId) = $rowIdParts;
                    DB::table('cart_product')
                        ->where('cart_id', $cartId)
                        ->where('product_id', $productId)
                        ->where(function ($query) {
                            $query->whereNull('uuid')
                                ->orWhere('uuid', '');
                        })
                        ->update(['qty' => $payload['qty']]);
                }

                $cartCaculate = $this->cartAndPromotion();

                if (count($rowIdParts) == 3) {
                    list(
                        $cartId,
                        $productId,
                        $uuid
                    ) = $rowIdParts;
                } elseif (count($rowIdParts) == 2) {
                    list(
                        $cartId,
                        $productId
                    ) = $rowIdParts;
                    $uuid = '';
                }

                $carts = $this->convertCartFormCart($user->id);
                $carts = collect($carts);
                $carts = $carts->map(function ($cart) {
                    return (object) $cart;
                });

                Log::info('Carts: ', ['carts' => $carts]);

                // Tìm sản phẩm trong giỏ hàng
                $cartItem = $carts->first(function ($cart) use ($cartId, $productId, $uuid) {
                    return $cart->cartId == $cartId && $cart->productId == $productId && $cart->uuid == $uuid;
                });

                // Tính tổng giá trị của sản phẩm (bao gồm giảm giá)
                $cartCaculate['cartItemSubTotal'] = $cartItem->qty * $cartItem->price;
                $cartCaculate['cartItemOriginalSubTotal'] = $cartItem->qty * $cartItem->priceOriginal;

                $totalDiscountPromotion = 0;

                foreach ($carts as $cart) {
                    // Nếu giá gốc khác giá bán, tính giảm giá
                    Log::info($cart->priceOriginal);
                    Log::info($cart->price);
                    if ($cart->priceOriginal !== $cart->price) {
                        $discountAmount = ($cart->priceOriginal - $cart->price) * $cart->qty;
                        $totalDiscountPromotion += $discountAmount;
                        Log::info("Bên trong");
                    }
                    Log::info("Bên ngoài");
                }

                $cartCaculate['carts'] = $carts;
                $cartCaculate['totalDiscountPromotion'] = $totalDiscountPromotion;

                Log::info('Promotion: ', ['cartCaculate' => $cartCaculate]);

                return $cartCaculate;
            } else {
                Cart::instance('shopping')->update($payload['rowId'], $payload['qty']);
                $cartCaculate = $this->cartAndPromotion();
                $cartItem = Cart::instance('shopping')->get($payload['rowId']);

                Log::info('Cart item details:', ['cartItem' => $cartItem]);

                $cartCaculate['cartItemSubTotal'] = $cartItem->qty * $cartItem->price;
                $cartCaculate['cartItemOriginalSubTotal'] = $cartItem->qty * $cartItem->priceOriginal;

                // Tính tổng số tiền được giảm của toàn bộ giỏ hàng
                $totalDiscountPromotion = 0;
                // Lấy nội dung giỏ hàng
                $cartContent = Cart::instance('shopping')->content();

                // Log nội dung giỏ hàng
                Log::info('Cart content:', ['cartContent' => $cartContent]);

                foreach (Cart::instance('shopping')->content() as $cart) {
                    $discountAmount = ($cart->priceOriginal - $cart->price) * $cart->qty;
                    $totalDiscountPromotion += $discountAmount;
                }

                // Log tổng số tiền giảm giá
                Log::info('Total discount promotion: ', ['totalDiscountPromotion' => $totalDiscountPromotion]);

                // Cập nhật giỏ hàng và trả về kết quả
                $cartCaculate['carts'] = Cart::instance('shopping')->content();
                $cartCaculate['totalDiscountPromotion'] = $totalDiscountPromotion;

                Log::info('Total discount promotion: ', ['cartCaculate' => $cartCaculate]);
                return $cartCaculate;
            }
        } catch (\Exception $e) {
            echo $e->getMessage() . $e->getCode();
            die();
            return false;
        }
    }



    public function deleteCart($request)
    {
        try {
            $payload = $request->input();

            Log::info('Delete Cart Payload:', $payload);

            $user = Auth::guard('customer')->user();
            if ($user) {
                $rowIdParts = explode('_', $payload['rowId']);

                if (count($rowIdParts) == 3) {
                    list($cartId, $productId, $uuid) = $rowIdParts;

                    if (is_null($uuid) || $uuid === '') {
                        DB::table('cart_product')
                            ->where('cart_id', $cartId)
                            ->where('product_Id', $productId)
                            ->where(function ($query) {
                                $query->whereNull('uuid')
                                    ->orWhere('uuid', '');
                            })
                            ->delete();
                    } else {
                        DB::table('cart_product')
                            ->where('cart_id', $cartId)
                            ->where('product_id', $productId)
                            ->where('uuid', $uuid)
                            ->delete();
                    }
                } elseif (count($rowIdParts) == 2) {
                    list($cartId, $productId) = $rowIdParts;
                    DB::table('cart_product')
                        ->where('cart_id', $cartId)
                        ->where('product_id', $productId)
                        ->where(function ($query) {
                            $query->whereNull('uuid')
                                ->orWhere('uuid', '');
                        })
                        ->delete();
                }
            } else {
                Cart::instance('shopping')->remove($payload['rowId']);
            }

            // Cập nhật lại giỏ hàng và khuyến mãi
            $cartCaculate = $this->cartAndPromotion();
            return $cartCaculate;
        } catch (\Exception $e) {
            Log::error('Error deleting cart item: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }


    public function order($request, $system)
    {
        DB::beginTransaction();
        try {
            $payload = $this->request($request);
            Log::info('Payload:', ['payload' => $payload]);
            $order = $this->orderRepository->create($payload, ['products']);

            if ($order->id > 0) {
                $orders = $this->createOrderProduct($payload, $order, $request);

                $this->mail($order, $system);
                $user = Auth::guard('customer')->user();
                if ($user) {
                    $cart = $this->cartRepository->findByCustomerId($user->id);
                    DB::table('carts')->where('id', $cart->id)->delete();
                } else {
                    Log::info("ORDER SESSION");
                    Cart::instance('shopping')->destroy();
                }
            }

            DB::commit();
            return ['order' => $order, 'flag' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi khi tạo đơn hàng: " . $e->getMessage());
            echo $e->getMessage();
            die();
        }
    }



    private function mail($order, $sytem)
    {
        $to = $order->email;
        $cc = $sytem['contact_email'];
        $user = Auth::guard('customer')->user();
        if ($user) {
            $carts = $this->convertCartFormCart($user->id);
            $carts = collect($carts);
            $carts = $carts->map(function ($cart) {
                return (object) $cart;
            });
        } else {
            $carts = Cart::instance('shopping')->content();
            $carts = $this->remakeCart($carts);
        }
        $cartCaculate = $this->cartAndPromotion();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);
        $data = [
            'order' => $order,
            'carts' => $carts,
            'cartCaculate' => $cartCaculate,
            'cartPromotion' => $cartPromotion
        ];

        \Mail::to($to)->cc($cc)->send(new OrderMail($data));
    }


    public function getBatch($productId, $variantId)
    {
        $query = DB::table('inventory_batches')
            ->where('product_id', $productId)
            ->where('quantity', '>', 0)
            ->where('publish', 2)
            ->orderBy('created_at', 'asc');

        if (is_null($variantId)) {
            $query->whereNull('variant_id');
        } else {
            $query->where('variant_id', $variantId);
        }

        return $query->first();
    }



    private function createOrderProduct($payload, $order, $request)
    {
        $user = Auth::guard('customer')->user();

        $temp = [];
        $stockMovements = [];

        if ($user) {

            $carts = $this->convertCartFormCart($user->id);
            $carts = collect($carts)->map(fn($cart) => (object) $cart);

            if (!is_null($carts)) {
                foreach ($carts as $val) {
                    $qtyRemaining = $val->qty;
                    while ($qtyRemaining > 0) {
                        $batch = $this->getBatch($val->productId, $val->variant_id);

                        $batchQty = min($batch->quantity, $qtyRemaining);

                        $temp[] = [
                            'product_id'    => $val->productId,
                            'uuid'          => $val->uuid,
                            'name'          => $val->name,
                            'qty'           => $batchQty,
                            'price'         => $val->price,
                            'priceOriginal' => $val->priceOriginal,
                            'option'        => json_encode($val->options),
                            'batch_id'      => $batch->id,
                            'variant_id'    => $val->variant_id,
                        ];

                        DB::table('inventory_batches')
                            ->where('id', $batch->id)
                            ->decrement('quantity', $batchQty);

                        $qtyRemaining -= $batchQty;
                    }
                }
            }
        } else {

            $carts = Cart::instance('shopping')->content();
            $carts = $this->remakeCart($carts);

            if (!is_null($carts)) {
                foreach ($carts as $val) {
                    $extract = explode('_', $val->id);
                    $qtyRemaining = $val->qty;

                    while ($qtyRemaining > 0) {
                        $batch = $this->getBatch($extract[0], $val->options->variant_id);

                        $batchQty = min($batch->quantity, $qtyRemaining);

                        $temp[] = [
                            'product_id'    => $extract[0],
                            'uuid'          => isset($extract[1]) ? $extract[1] : null,
                            'name'          => $val->name,
                            'qty'           => $batchQty,
                            'price'         => $val->price,
                            'priceOriginal' => $val->priceOriginal,
                            'option'        => json_encode($val->options),
                            'batch_id'      => $batch->id,
                            'variant_id'    => $val->options->variant_id,
                        ];
                        DB::table('inventory_batches')
                            ->where('id', $batch->id)
                            ->decrement('quantity', $batchQty);
                        $qtyRemaining -= $batchQty;
                    }
                }
            }
        }
        $order->products()->sync($temp);
    }



    public function convertCartToArray($cart)
    {
        return $cart->map(function ($cartItem) {
            // Kiểm tra nếu id có dạng "168_13648759968" (có dấu "_")
            if (strpos($cartItem->id, '_') !== false) {
                [$productId, $uuid] = explode('_', $cartItem->id, 2);
            } else {
                $productId = $cartItem->id;
                $uuid = null;
            }

            // Tìm trong bảng products trước
            $product = $this->productRepository->findById($productId);
            if ($product) {
                $image = $product->image;
            } else {
                // Nếu không tìm thấy trong products, tìm trong product_variants với uuid
                $variant = $this->productVariantRepository->findProductVariant($productId, $uuid);
                $image = $variant ? $variant->image : 'frontend/resources/img/no_image.png';
            }

            return [
                'rowId' => $cartItem->rowId,
                'id' => $cartItem->id,
                'qty' => $cartItem->qty,
                'name' => $cartItem->name,
                'price' => $cartItem->price,
                'options' => $cartItem->options,
                'priceOriginal' => $cartItem->priceOriginal ?? $cartItem->options->priceOriginal ?? $cartItem->price,
                'image' => $image,
            ];
        })->toArray();
    }

    // 1. Lấy tất cả giỏ hàng của người dùng cùng với các sản phẩm trong giỏ hàng
    public function getUserCartsWithProducts($userId)
    {
        $carts = DB::table('carts')->where('customer_id', $userId)->get();
        if ($carts->isEmpty()) {
            return response()->json(['message' => 'Không tìm thấy giỏ hàng của người dùng'], 404);
        }
        $cartProducts = [];
        foreach ($carts as $cart) {
            $cartProducts[] = DB::table('cart_product')
                ->where('cart_id', $cart->id)
                ->get();
        }

        return response()->json([
            'carts' => $carts,
            'cart_products' => $cartProducts,
        ]);
    }

    public function getTotalCartProductCount($userId)
    {
        $carts = DB::table('carts')->where('customer_id', $userId)->get();
        if ($carts->isEmpty()) {
            return 0;
        }
        $totalQuantity = 0;
        foreach ($carts as $cart) {
            $cartProducts = DB::table('cart_product')->where('cart_id', $cart->id)->get();
            $totalQuantity += $cartProducts->sum('qty');
        }
        return $totalQuantity;
    }

    public function convertCartFormCart($userId)
    {

        $carts = DB::table('carts')->where('customer_id', $userId)->get();
        $cartData = [];
        foreach ($carts as $cart) {
            $cartProducts = DB::table('cart_product')
                ->where('cart_id', $cart->id)
                ->get();

            foreach ($cartProducts as $cartItem) {
                $uuid = $cartItem->uuid ?: null;
                $product = $this->productRepository->findById($cartItem->product_id);

                if ($product) {
                    $image = $product->image;
                    $priceOriginal = $product->price;
                    if ($uuid) {
                        $variant = $this->productVariantRepository->findProductVariant($cartItem->product_id, $uuid);
                        if ($variant) {
                            $image = $variant->album;
                            $priceOriginal = $variant->price;
                        }
                    }
                }


                $cartData[] = [
                    'cartId' => $cart->id,
                    'productId' => $cartItem->product_id,
                    'uuid' => $uuid,
                    'name' => $cartItem->name,
                    'variant_id' => $cartItem->variant_id,
                    'qty' => $cartItem->qty,
                    'price' => $cartItem->price,
                    'options' => json_decode($cartItem->option),
                    'image' => $image,
                    'priceOriginal' => $priceOriginal,
                ];
            }
        }
        return $cartData;
    }



    // private function request($request)
    // {

    //     $cartCaculate = $this->reCaculateCart();
    //     $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);

    //     $payload = $request->except(['_token', 'voucher', 'create']);
    //     $payload['code'] = time();
    //     $payload['cart'] = $cartCaculate;
    //     $payload['promotion']['discount'] = $cartPromotion['discount'] ?? '';
    //     $payload['promotion']['name'] = $cartPromotion['selectedPromotion']->name ?? '';
    //     $payload['promotion']['code'] = $cartPromotion['selectedPromotion']->code ?? '';
    //     $payload['promotion']['startDate'] = $cartPromotion['selectedPromotion']->startDate ?? '';
    //     $payload['promotion']['endDate'] = $cartPromotion['selectedPromotion']->endDate ?? '';
    //     $payload['confirm'] = 'pending';
    //     $payload['delivery'] = 'pending';
    //     $payload['payment'] = 'unpaid';
    //     return $payload;
    // }


    private function request($request)
    {
        // Kiểm tra xem người dùng có đăng nhập hay không và lấy customer_id nếu có
        $customerId = Auth::check() ? Auth::id() : null; // Hoặc lấy từ session()->get('customer_id') nếu bạn lưu trong session

        $cartCaculate = $this->reCaculateCart();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);

        $user = Auth::guard('customer')->user();
        if ($user) {

            $carts = $this->convertCartFormCart($user->id);
            $carts = collect($carts);
            $carts = $carts->map(function ($cart) {
                return (object) $cart;
            });
            // Chuyển giỏ hàng thành mảng sản phẩm
            $products = [];
            foreach ($carts as $cartItem) {
                $products[] = [
                    'product_id' => $cartItem->productId,
                    'variant_id' => $cartItem->options->attribute ?? null, // nếu có biến thể
                    'name' => $cartItem->name,
                    'quantity' => $cartItem->qty,
                    'price' => $cartItem->price,
                ];
            }
        } else {
            $carts = Cart::instance('shopping')->content();
            // Chuyển giỏ hàng thành mảng sản phẩm
            $products = [];
            foreach ($carts as $cartItem) {
                // Chuyển mỗi sản phẩm trong giỏ hàng thành mảng thông tin phù hợp với payload
                $products[] = [
                    'product_id' => $cartItem->id,
                    'variant_id' => $cartItem->options->attribute ?? null, // nếu có biến thể
                    'name' => $cartItem->name,
                    'quantity' => $cartItem->qty,
                    'price' => $cartItem->price,
                ];
            }
        }


        // Tạo payload với giỏ hàng và các thông tin cần thiết
        $payload = $request->except(['_token', 'voucher', 'create']);
        $payload['code'] = time();
        $payload['cart'] = $cartCaculate;
        $payload['products'] = $products; // Thay 'products' bằng thông tin từ giỏ hàng
        $payload['promotion']['discount'] = $cartPromotion['discount'] ?? '';
        $payload['promotion']['name'] = $cartPromotion['selectedPromotion']->name ?? '';
        $payload['promotion']['code'] = $cartPromotion['selectedPromotion']->code ?? '';
        $payload['promotion']['startDate'] = $cartPromotion['selectedPromotion']->startDate ?? '';
        $payload['promotion']['endDate'] = $cartPromotion['selectedPromotion']->endDate ?? '';
        $payload['confirm'] = 'pending';
        $payload['delivery'] = 'pending';
        $payload['payment'] = 'unpaid';

        // Thêm customer_id vào payload nếu có
        if ($user) {
            $payload['customer_id'] = $user->id;
        }

        return $payload;
    }




    private function cartAndPromotion()
    {
        $cartCaculate = $this->reCaculateCart();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);
        $cartCaculate['cartTotal'] = $cartCaculate['cartTotal'] - $cartPromotion['discount'];
        $cartCaculate['cartDiscount'] = $cartPromotion['discount'];

        return $cartCaculate;
    }

    public function reCaculateCart()
    {
        $user = Auth::guard('customer')->user();
        if ($user) {

            $carts = $this->convertCartFormCart($user->id);
            $carts = collect($carts);
            $carts = $carts->map(function ($cart) {
                return (object) $cart;
            });
        } else {
            $carts = Cart::instance('shopping')->content();
        }
        $total = 0;
        $totalItems = 0;
        foreach ($carts as $cart) {
            $total = $total + $cart->price * $cart->qty;
            $totalItems = $totalItems + $cart->qty;
        }
        return [
            'cartTotal' => $total,
            'cartTotalItems' => $totalItems
        ];
    }



    // public function remakeCart($carts)
    // {
    //     $cartId = $carts->pluck('id')->all();
    //     $temp = [];
    //     $objects = [];
    //     if (count($cartId)) {
    //         foreach ($cartId as $key => $val) {
    //             $extract = explode('_', $val);
    //             if (count($extract) > 1) {
    //                 $temp['variant'][] = $extract[1];
    //             } else {
    //                 $temp['product'][] = $extract[0];
    //             }
    //         }


    //         if (isset($temp['variant'])) {
    //             $objects['variants'] = $this->productVariantRepository->findByCondition(
    //                 [],
    //                 true,
    //                 [],
    //                 ['id', 'desc'],
    //                 ['whereIn' => $temp['variant'], 'whereInField' => 'uuid']
    //             )->keyBy('uuid');
    //         }

    //         if (isset($temp['product'])) {
    //             $objects['products'] = $this->productRepository->findByCondition(
    //                 [
    //                     config('apps.general.defaultPublish')
    //                 ],
    //                 true,
    //                 [],
    //                 ['id', 'desc'],
    //                 ['whereIn' => $temp['product'], 'whereInField' => 'id']
    //             )->keyBy('id');
    //         }


    //         foreach ($carts as $keyCart => $cart) {
    //             $explode = explode('_', $cart->id);
    //             $objectId = $explode[1] ?? $explode[0];
    //             if (isset($objects['variants'][$objectId])) {
    //                 $variantItem = $objects['variants'][$objectId];
    //                 $variantImage = explode(',', $variantItem->album)[0] ?? null;
    //                 $cart->setImage($variantImage)->setPriceOriginal($variantItem->price);
    //             } elseif (isset($objects['products'][$objectId])) {
    //                 $productItem = $objects['products'][$objectId];
    //                 $cart->setImage($productItem->image)->setPriceOriginal($productItem->price);
    //             }
    //         }
    //     }

    //     return $carts;
    // }

    public function remakeCart($carts)
    {
        $cartId = $carts->pluck('id')->all();
        $temp = [];
        $objects = [];

        if (count($cartId)) {
            foreach ($cartId as $key => $val) {
                $extract = explode('_', $val);
                if (count($extract) > 1) {
                    $temp['variant'][] = $extract[1];
                    $temp['product'][] = $extract[0];
                } else {
                    $temp['product'][] = $extract[0];
                }
            }

            if (isset($temp['variant'])) {
                $objects['variants'] = $this->productVariantRepository->findByCondition(
                    [],
                    true,
                    [],
                    ['id', 'desc'],
                    ['whereIn' => $temp['variant'], 'whereInField' => 'uuid']
                )->keyBy('uuid');
            }

            if (isset($temp['product'])) {
                $objects['products'] = $this->productRepository->findByCondition(
                    [config('apps.general.defaultPublish')],
                    true,
                    [],
                    ['id', 'desc'],
                    ['whereIn' => $temp['product'], 'whereInField' => 'id']
                )->keyBy('id');
            }

            foreach ($carts as $keyCart => $cart) {
                $explode = explode('_', $cart->id);
                $productId = $explode[0]; // ID sản phẩm
                $uuid = $explode[1] ?? null; // UUID biến thể nếu có

                $productItem = $objects['products'][$productId] ?? null;
                $variantItem = $uuid && isset($objects['variants'][$uuid]) ? $objects['variants'][$uuid] : null;

                // Lấy ảnh: Ưu tiên ảnh biến thể, nếu không có thì lấy ảnh sản phẩm
                $variantImage = $variantItem && !empty($variantItem->album) ? explode(',', $variantItem->album)[0] : null;
                $finalImage = $variantImage ?? ($productItem->image ?? 'frontend/resources/img/no_image.png');

                // Cập nhật ảnh và giá
                $cart->setImage($finalImage)->setPriceOriginal($variantItem->price ?? $productItem->price ?? 0);
            }
        }

        return $carts;
    }


    public function remakeCartCart()
    {
        $carts = Cart::instance('shopping')->content();
        $cartId = $carts->pluck('id')->all();
        $temp = [];
        $objects = [];

        if (count($cartId)) {
            foreach ($cartId as $key => $val) {
                $extract = explode('_', $val);
                if (count($extract) > 1) {
                    $temp['variant'][] = $extract[1];
                } else {
                    $temp['product'][] = $extract[0];
                }
            }

            // Lấy thông tin biến thể
            if (isset($temp['variant'])) {
                $objects['variants'] = $this->productVariantRepository->findByCondition(
                    [],
                    true,
                    [],
                    ['id', 'desc'],
                    ['whereIn' => $temp['variant'], 'whereInField' => 'uuid']
                )->keyBy('uuid');
            }

            // Lấy thông tin sản phẩm
            if (isset($temp['product'])) {
                $objects['products'] = $this->productRepository->findByCondition(
                    [
                        config('apps.general.defaultPublish')
                    ],
                    true,
                    [],
                    ['id', 'desc'],
                    ['whereIn' => $temp['product'], 'whereInField' => 'id']
                )->keyBy('id');
            }

            // Gán hình ảnh vào từng sản phẩm trong giỏ hàng
            foreach ($carts as $keyCart => $cart) {
                $explode = explode('_', $cart->id);
                $objectId = $explode[1] ?? $explode[0];

                if (isset($objects['variants'][$objectId])) {
                    $variantItem = $objects['variants'][$objectId];
                    $variantImage = explode(',', $variantItem->album)[0] ?? null;
                    $cart->image = $variantImage;
                    $cart->setPriceOriginal($variantItem->price);
                } elseif (isset($objects['products'][$objectId])) {
                    $productItem = $objects['products'][$objectId];
                    $cart->image = $productItem->image;
                    $cart->setPriceOriginal($productItem->price);
                } else {
                    // Nếu không tìm thấy image, đặt ảnh mặc định
                    $cart->image = asset('path/to/default-image.jpg');
                }
            }
        }

        return $carts;
    }

    public function cartPromotion($cartTotal = 0)
    {
        $maxDiscount = 0;
        $selectedPromotion = null;
        $promotions = $this->promotionRepository->getPromotionByCartTotal();
        if (!is_null($promotions)) {
            foreach ($promotions as $promotion) {
                $discount = $promotion->discountInformation['info'];
                $amountFrom = $discount['amountFrom'] ?? [];
                $amountTo = $discount['amountTo'] ?? [];
                $amountValue = $discount['amountValue'] ?? [];
                $amountType = $discount['amountType'] ?? [];
                if (!empty($amountFrom) && count($amountFrom) == count($amountTo) && count($amountTo) == count($amountValue)) {
                    for ($i = 0; $i < count($amountFrom); $i++) {
                        $currentAmountFrom = convert_price($amountFrom[$i]);
                        $currentAmountTo = convert_price($amountTo[$i]);
                        $currentAmountValue = convert_price($amountValue[$i]);
                        $currentAmountType = $amountType[$i];
                        if ($cartTotal > $currentAmountFrom && ($cartTotal <= $currentAmountTo) || $cartTotal > $currentAmountTo) {

                            if ($currentAmountType == 'cash') {
                                $maxDiscount = max($maxDiscount, $currentAmountValue);
                            } else if ($currentAmountType == 'percent') {
                                $discountValue = ($currentAmountValue / 100) * $cartTotal;
                                $maxDiscount = max($maxDiscount, $discountValue);
                            }
                            $selectedPromotion = $promotion;
                        }
                    }
                }
            }
        }
        return [
            'discount' => $maxDiscount,
            'selectedPromotion' => $selectedPromotion
        ];
    }
}
