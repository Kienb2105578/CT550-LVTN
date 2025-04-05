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
     <div class="panel-foot mt30 pay">
         <div class="cart-summary mb20">
             <div class="cart-summary-item">
                 <div class="uk-flex uk-flex-middle uk-flex-space-between">
                     <span class="summay-title">Giảm giá khuyến mãi</span>
                     <div class="summary-value discount-promotion">- {{ convert_price($totalDiscountPromotion, true) }}đ
                     </div>
                 </div>
             </div>
             <div class="cart-summary-item">
                 <div class="uk-flex uk-flex-middle uk-flex-space-between">
                     <span class="summay-title">Giảm giá</span>
                     <div class="summary-value discount-value">- {{ convert_price($cartPromotion['discount'], true) }}đ
                     </div>
                 </div>
             </div>
             <div class="cart-summary-item">
                 <div class="uk-flex uk-flex-middle uk-flex-space-between">
                     <span class="summay-title">Phí giao hàng</span>
                     <div class="summary-value">Miễn phí</div>
                 </div>
             </div>
             <div class="cart-summary-item">
                 <div class="uk-flex uk-flex-middle uk-flex-space-between">
                     <span class="summay-title bold">Tổng tiền</span>
                     <div class="summary-value cart-total">
                         {{ count($carts) && !is_null($carts) ? convert_price($cartCaculate['cartTotal'] - $cartPromotion['discount'], true) : 0 }}đ
                     </div>
                 </div>
             </div>
             <div class="buy-more">
                 <a href="{{ write_url('san-pham') }}" class="btn-buymore">Chọn thêm sản phẩm</a>
             </div>
         </div>
     </div>
 </div>
