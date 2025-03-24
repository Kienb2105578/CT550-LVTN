<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\FrontendController;
use App\Services\CartService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends FrontendController
{
    protected $cartService;
    protected $productRepository;
    protected $language;

    public function __construct(
        CartService $cartService,
        ProductRepository $productRepository,
    ) {
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
        parent::__construct();
    }

    public function create(Request $request)
    {
        $flag = $this->cartService->createCart($request, $this->language);

        $user = Auth::guard('customer')->user();
        if ($user) {
            $carts = $this->cartService->convertCartFormCart($user->id);
            $totalQuantity = $this->cartService->getTotalCartProductCount($user->id);
            $countMiniCart = count($carts);
        } else {
            $cart = Cart::instance('shopping')->content();
            $totalQuantity = Cart::instance('shopping')->count();
            $cartArray = $cart->toArray();
            $carts = $this->cartService->convertCartToArray($cart);
            $countMiniCart = count($cartArray);
        }

        return response()->json([
            'cart' => $cart ?? null,
            'carts' => $carts,
            'totalQuantity' => $totalQuantity,
            'countMiniCart' => $countMiniCart,
            'messages' => 'Thêm sản phẩm vào giỏ hàng thành công',
            'code' => ($flag) ? 10 : 11,
        ]);
    }


    public function update(Request $request)
    {
        $response = $this->cartService->updateCart($request);
        $user = Auth::guard('customer')->user();
        if ($user) {
            $carts = $this->cartService->convertCartFormCart($user->id);
            $totalQuantity = $this->cartService->getTotalCartProductCount($user->id);
            $countMiniCart = count($carts);
        } else {
            $totalQuantity = Cart::instance('shopping')->count();
            $cart = Cart::instance('shopping')->content();
            $cartArray = $cart->toArray();
            $countMiniCart = count($cartArray);
        }

        return response()->json([
            'response' => $response,
            'totalQuantity' => $totalQuantity,
            'countMiniCart' => $countMiniCart,
            'messages' => 'Cập nhật số lượng thành công',
            'code' => (!$response) ? 11 : 10,
        ]);
    }

    public function delete(Request $request)
    {
        $response = $this->cartService->deleteCart($request);
        $user = Auth::guard('customer')->user();
        if ($user) {
            $carts = $this->cartService->convertCartFormCart($user->id);
            $totalQuantity = $this->cartService->getTotalCartProductCount($user->id);
            $countMiniCart = count($carts);
        } else {
            $totalQuantity = Cart::instance('shopping')->count();
            $cart = Cart::instance('shopping')->content();
            $cartArray = $cart->toArray();
            $countMiniCart = count($cartArray);
        }

        return response()->json([
            'response' => $response,
            'totalQuantity' => $totalQuantity,
            'countMiniCart' => $countMiniCart,
            'messages' => 'Xóa sản phẩm khỏi giỏ hàng thành công',
            'code' => (!$response) ? 11 : 10,
        ]);
    }
}
