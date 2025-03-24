<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Cart;

class CartComposer
{

    protected $cartService;

    public function __construct(
        CartService $cartService,
    ) {
        $this->cartService = $cartService;
    }

    public function compose(View $view)
    {
        $user = Auth::guard('customer')->user();
        if ($user) {
            $carts = $this->cartService->convertCartFormCart($user->id);
            $countMiniCart = count($carts);
            $cartCaculate = $this->cartService->reCaculateCart();
            $carts = collect($carts);
            $carts = $carts->map(function ($cart) {
                return (object) $cart;
            });
        } else {
            $carts = Cart::instance('shopping')->content();
            $cartArray = $carts->toArray();
            $countMiniCart = count($cartArray);
            $carts = $this->cartService->remakeCart($carts);
            $cartCaculate = $this->cartService->reCaculateCart();
        }
        $view->with('cartShare', $cartCaculate)
            ->with('countMiniCart', $countMiniCart)
            ->with('carts', $carts)
        ;
    }
}
