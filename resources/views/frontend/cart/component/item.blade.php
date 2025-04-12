<div class="panel-body">
    @if (!empty($carts) && count($carts))
        @php
            $totalDiscount = 0;
        @endphp

        <div class="cart-list">
            @foreach ($carts as $cart)
                @php
                    $discount = ($cart->priceOriginal - $cart->price) * $cart->qty;
                    $totalDiscount += $discount;
                    $rowId =
                        $cart->rowId ?? $cart->cartId . '_' . $cart->productId . ($cart->uuid ? '_' . $cart->uuid : '');
                    $productId = isset($cart->id) ? explode('_', $cart->id)[0] : $cart->productId;
                    $attributes = [];

                    if (!empty($cart->options)) {
                        if (is_array($cart->options) && isset($cart->options['attribute'])) {
                            $attributes = $cart->options['attribute'];
                        } elseif (is_object($cart->options) && isset($cart->options->attribute)) {
                            $attributes = $cart->options->attribute;
                        }
                    }
                @endphp

                <div class="cart-item border mb-3 p-3 shadow-sm d-flex" style="min-height: 140px; position: relative;">
                    <div class="d-flex" style="width: 100%;">
                        <!-- ảnh -->
                        <div style="width: 120px; flex-shrink: 0;"
                            class="me-3 d-flex align-items-center justify-content-center">
                            <div class="position-relative">
                                <img src="{{ $cart->image ?? asset('frontend/resources/img/no_image.png') }}"
                                    style="width: 120px; height: 120px; object-fit: cover;" class="rounded"
                                    alt="">
                                <span
                                    class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-primary">
                                    {{ $cart->qty }}
                                </span>
                            </div>
                        </div>

                        <!-- nội dung -->
                        <div class="flex-grow-1 d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="mb-2 cart-product-name">{{ $cart->name }}</h5>
                            </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap mt-auto">
                                <!-- số lượng -->
                                <div class="cart-item-qty">
                                    <button type="button" class="btn-qty minus">-</button>
                                    <input type="text" class="input-qty" value="{{ $cart->qty }}">
                                    <input type="hidden" class="rowId" value="{{ $rowId }}">
                                    <input type="hidden" class="productId" value="{{ $productId }}">
                                    @foreach ((array) $attributes as $attribute)
                                        <input type="hidden" class="attributeId" value="{{ $attribute }}">
                                    @endforeach
                                    <button type="button" class="btn-qty plus">+</button>
                                </div>

                                <!-- giá -->
                                <div class="cart-item-price text-end">
                                    @if ($cart->price != $cart->priceOriginal)
                                        <span class="text-muted text-decoration-line-through me-2">
                                            {{ convert_price($cart->priceOriginal * $cart->qty, true) }}đ
                                        </span>
                                    @endif
                                    <span
                                        class="fw-bold text-danger">{{ convert_price($cart->price * $cart->qty, true) }}đ</span>
                                </div>

                                <!-- xoá -->
                                <div class="cart-item-remove ms-3 text-danger me-3"
                                    style="cursor: pointer; position: absolute; top: 10px; right: 10px;"
                                    data-row-id="{{ $rowId }}">
                                    ✕
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
