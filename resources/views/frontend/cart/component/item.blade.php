<div class="panel-body">
    @if (count($carts) && !is_null($carts))
        @php
            $totalDiscount = 0;
        @endphp
        <div class="cart-list">
            @foreach ($carts as $keyCart => $cart)
                @php
                    $discountAmount = ($cart->priceOriginal - $cart->price) * $cart->qty;
                    $totalDiscount += $discountAmount;
                @endphp
                <div class="cart-item">
                    <div class="uk-grid uk-grid-medium">
                        <div class="uk-width-small-1-1 uk-width-medium-1-5">
                            <div class="cart-item-image">
                                <span class="image img-scaledown"><img
                                        src="{{ !empty($cart->image) ? $cart->image : asset('frontend/resources/img/no_image.png') }}"
                                        alt=""></span>
                                <span class="cart-item-number">{{ $cart->qty }}</span>
                            </div>
                        </div>
                        <div class="uk-width-small-1-1 uk-width-medium-4-5">
                            <div class="cart-item-info">
                                <h3 class="title"><span>{{ $cart->name }}</span></h3>
                                <div class="cart-item-action uk-flex uk-flex-middle uk-flex-space-between">
                                    <div class="cart-item-qty">
                                        <button type="button" class="btn-qty minus">-</button>
                                        <input type="text" class="input-qty" value="{{ $cart->qty }}">
                                        <input type="hidden" class="rowId"
                                            value="{{ $cart->rowId ?? $cart->cartId . '_' . $cart->productId . ($cart->uuid ? '_' . $cart->uuid : '') }}">
                                        <input type="hidden" class="productId"
                                            value="{{ isset($cart->id) ? explode('_', $cart->id)[0] : $cart->productId }}">

                                        @if (!empty($cart->options) && (is_array($cart->options) || is_object($cart->options)))
                                            @if (is_array($cart->options) && isset($cart->options['attribute']))
                                                @foreach ($cart->options['attribute'] as $attribute)
                                                    <input type="hidden" class="attributeId"
                                                        value="{{ $attribute }}">
                                                @endforeach
                                            @elseif (is_object($cart->options) && isset($cart->options->attribute))
                                                @foreach ($cart->options->attribute as $attribute)
                                                    <input type="hidden" class="attributeId"
                                                        value="{{ $attribute }}">
                                                @endforeach
                                            @endif
                                        @endif


                                        <button type="button" class="btn-qty plus">+</button>
                                    </div>
                                    <div class="cart-item-price">
                                        <div class="uk-flex uk-flex-bottom">
                                            @if ($cart->price != $cart->priceOriginal)
                                                <span
                                                    class="cart-price-old mr10">{{ convert_price($cart->priceOriginal * $cart->qty, true) }}đ</span>
                                            @endif
                                            <span
                                                class="cart-price-sale">{{ convert_price($cart->price * $cart->qty, true) }}đ</span>
                                        </div>
                                    </div>
                                    <div class="cart-item-remove"
                                        data-row-id="{{ $cart->rowId ?? $cart->cartId . '_' . $cart->productId . ($cart->uuid ? '_' . $cart->uuid : '') }}">
                                        <span>✕</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
