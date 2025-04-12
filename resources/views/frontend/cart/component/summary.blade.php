@php
    $totalDiscountPromotion = 0;
@endphp

<div class="cart-list">
    @foreach ($carts as $keyCart => $cart)
        @php
            $discountAmount = ($cart->priceOriginal - $cart->price) * $cart->qty;
            $totalDiscountPromotion += $discountAmount;
        @endphp
    @endforeach

    <div class="panel-foot mt-4 pay">
        <div class="cart-summary mb-4 p-3 border rounded shadow-sm bg-light">

            <div class="cart-summary-item mb-3">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="summay-title">Giảm giá khuyến mãi</span>
                    </div>
                    <div class="col-auto text-end">
                        <div class="summary-value discount-promotion text-danger">
                            - {{ convert_price($totalDiscountPromotion, true) }}đ
                        </div>
                    </div>
                </div>
            </div>

            <div class="cart-summary-item mb-3">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="summay-title">Giảm giá</span>
                    </div>
                    <div class="col-auto text-end">
                        <div class="summary-value discount-value text-danger">
                            - {{ convert_price($cartPromotion['discount'], true) }}đ
                        </div>
                    </div>
                </div>
            </div>

            <div class="cart-summary-item mb-3">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="summay-title">Phí giao hàng</span>
                    </div>
                    <div class="col-auto text-end">
                        <div class="summary-value">Miễn phí</div>
                    </div>
                </div>
            </div>

            <div class="cart-summary-item mb-4">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="summay-title fw-bold">Tổng tiền</span>
                    </div>
                    <div class="col-auto text-end">
                        <div class="summary-value cart-total text-primary fw-bold">
                            {{ count($carts) && !is_null($carts) ? convert_price($cartCaculate['cartTotal'] - $cartPromotion['discount'], true) : 0 }}đ
                        </div>
                    </div>
                </div>
            </div>

            <div class="buy-more text-center">
                <a href="{{ write_url('san-pham') }}" class="btn btn-outline-primary btn-buymore">Chọn thêm sản
                    phẩm</a>
            </div>

        </div>
    </div>
</div>
