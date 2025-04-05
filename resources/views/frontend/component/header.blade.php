<div id="header" class="pc-header uk-visible-large" data-uk-sticky style="background: #fff">
    <div class="upper">
        <div class="uk-container uk-container-center ">
            <div class="uk-flex uk-flex-between uk-flex-middle">
                <!-- Tên công ty sát bên trái -->
                <div class="company-name uk-text-left uk-width-1-2">
                    {{ $system['homepage_company'] }}
                </div>

                <!-- Phần đăng nhập/đăng xuất sát bên phải -->
                <div class="uk-text-right uk-width-1-2">
                    @if (auth()->guard('customer')->check())
                        <div class="header-cart uk-text-right uk-flex uk-flex-right">
                            <div class="uk-flex uk-flex-middle">
                                <a href="{{ route('customer.profile') }}" class="register_login  vector">
                                    {{ auth()->guard('customer')->user()->name }}
                                </a>
                                <a style="color: red; margin-left: 15px;" href="{{ route('customer.logout') }}"
                                    class="cart-text">
                                    Đăng xuất
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="header-cart uk-text-right uk-flex uk-flex-right">
                            <div class="uk-flex uk-flex-middle">
                                <a href="{{ route('customer.register') }}" class="register_login vector">
                                    Đăng Ký
                                </a>
                                <a href="{{ route('fe.auth.login') }}" class="register_login" style="margin-left: 15px">
                                    Đăng nhập
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="middle">
        <div class="uk-container uk-container-center">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <div class="logo">
                    <a href="/"><img src="{{ $system['homepage_logo'] }}" alt="Logo"></a>
                </div>
                <style>
                    .input-text {
                        flex-grow: 1;
                        padding: 10px 40px 10px 10px;
                        border: 1px solid #ccc;
                        border-radius: 25px;
                        outline: none;
                    }

                    #voice-search {
                        position: absolute;
                        right: 130px;
                        font-size: 18px;
                        cursor: pointer;
                        color: #333;
                        text-align: center;
                        padding: 13px;
                    }
                </style>
                <div class="header-search">
                    <form action="{{ write_url('tim-kiem') }}" class="uk-form form" style="margin-bottom: 0;">
                        <input type="text" name="keyword" placeholder="Nhập từ khóa" value=""
                            class="input-text">
                        {{-- <i id="voice-search" aria-hidden="true" class="fa fa-microphone voice-icon"></i> --}}
                        <button type="submit" value="" name="">
                            Tìm kiếm <i class="fa fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="header-toolbox">
                    <div class="uk-flex uk-flex-middle">
                        <div class="header-cart">
                            <div class="uk-flex uk-flex-middle">
                                <div class="cart-mini" onmouseover="showCartPopup()" onmouseleave="hideCartPopup()">
                                    <img src="frontend/resources/img/shopping-bag.png" alt="cart image"
                                        style="margin-bottom: 10px;">
                                    <span id="cartTotalItem"
                                        class="cart-total-quantity">{{ $countMiniCart ?? 0 }}</span>

                                    <div class="cart-popup" id="cartPopup" style="display: none;">
                                        <div class="panel-body cart-minicart-hearder">
                                            @if (count($carts) && !is_null($carts))
                                                <div class="cart-list">
                                                    @foreach ($carts as $cart)
                                                        <div class="cart-item">
                                                            <div class="cart-item-image">
                                                                <span class="image">
                                                                    <img src="{{ !empty($cart->image) ? $cart->image : asset('frontend/resources/img/no_image.png') }}"
                                                                        alt="">
                                                                </span>
                                                                <span
                                                                    class="cart-item-number">{{ $cart->qty }}</span>
                                                            </div>
                                                            <div class="cart-item-info">
                                                                <h3 class="title">{{ $cart->name }}</h3>
                                                                <div class="cart-item-price">
                                                                    <span
                                                                        class="cart-price-sale">{{ convert_price($cart->price * $cart->qty, true) }}đ</span>
                                                                </div>
                                                                {{-- <div class="cart-item-remove"
                                                                    data-row-id="{{ $cart->rowId ?? $cart->cartId . '_' . $cart->productId . ($cart->uuid ? '_' . $cart->uuid : '') }}">
                                                                    ✕</div> --}}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button class="checkout-btn"
                                                    onclick="window.location.href='{{ route('cart.checkout') }}'">
                                                    Thanh toán
                                                </button>
                                            @else
                                                <div class="empty-cart-message">
                                                    <img src="frontend/resources/img/shopping-bag.png"
                                                        alt="Empty Cart" />
                                                    <p style="font-weight:bold;">Giỏ hàng trống</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <style>
                                    .cart-popup {
                                        display: none;
                                        position: absolute;
                                        top: 40px;
                                        right: 0;
                                        background: #fff;
                                        border: 1px solid #ddd;
                                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                                        width: 400px;
                                        padding: 10px;
                                        z-index: 1000;
                                    }

                                    .cart-popup .cart-list {
                                        margin-top: 12px;
                                    }

                                    .cart-mini:hover .cart-popup {
                                        display: block;
                                    }

                                    .cart-popup .cart-item .cart-item-image {
                                        position: relative;
                                        margin-right: 10px;
                                    }


                                    .cart-popup .cart-item {
                                        display: flex;
                                        align-items: center;
                                        padding: 10px;
                                        border-bottom: 1px solid #eee;
                                    }

                                    .header-cart .cart-popup .cart-item-image img {
                                        width: 50px;
                                        height: 50px;
                                        object-fit: cover;
                                        border-radius: 5px;
                                        border: 1px solid gray;
                                    }

                                    .cart-popup .cart-item-number {
                                        background: red;
                                        color: #fff;
                                        border-radius: 50%;
                                        width: 20px;
                                        height: 20px;
                                        font-size: 12px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        position: absolute;
                                        top: -5px;
                                        right: -5px;
                                    }

                                    .cart-popup .cart-item-info .title {
                                        font-size: 13px;
                                        white-space: nowrap;
                                        overflow: hidden;
                                        text-overflow: ellipsis;
                                        max-width: 280px;
                                        display: block;
                                    }


                                    .cart-popup .cart-item-info {
                                        margin-right: 15px;
                                    }

                                    .cart-popup .cart-item-remove {
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        width: 25px;
                                        height: 25px;
                                        background-color: #f5f5f5;
                                        border-radius: 50%;
                                        font-size: 16px;
                                        cursor: pointer;
                                    }

                                    .cart-popup .checkout-btn {
                                        width: 40%;
                                        padding: 10px;
                                        background: linear-gradient(to right, #003366, #3399ff);
                                        color: white;
                                        text-align: center;
                                        border: none;
                                        cursor: pointer;
                                        font-size: 16px;
                                        border-radius: 10px;
                                        margin: 15px;
                                        display: block;
                                        margin-left: auto;
                                        font-weight: 600;
                                    }

                                    .cart-popup .checkout-btn:hover {
                                        background: #003366
                                    }

                                    /* Mũi tên trên popup */
                                    .cart-popup::before {
                                        content: "";
                                        position: absolute;
                                        top: -10px;
                                        right: 20px;
                                        border-width: 10px;
                                        border-style: solid;
                                        border-color: transparent transparent white transparent;
                                    }

                                    /* Căn giữa nếu giỏ hàng trống */
                                    .cart-popup .cart-empty {
                                        display: flex;
                                        flex-direction: column;
                                        align-items: center;
                                        justify-content: center;
                                    }

                                    .cart-popup .cart-empty img {
                                        width: 80px;
                                        margin-bottom: 10px;
                                    }

                                    .empty-cart-message {
                                        display: flex;
                                        flex-direction: column;
                                        justify-content: center;
                                        align-items: center;
                                        text-align: center;
                                        height: 100%;
                                    }

                                    .empty-cart-message img {
                                        max-width: 100px;
                                        margin-bottom: 10px;
                                        height: 70px;
                                    }
                                </style>

                                <script>
                                    function showCartPopup() {
                                        document.getElementById("cartPopup").style.display = "block";
                                    }

                                    function hideCartPopup() {
                                        document.getElementById("cartPopup").style.display = "none";
                                    }
                                </script>



                            </div>
                        </div>
                        <style>
                            .cart-mini {
                                position: relative;
                            }

                            .cart-total-quantity {
                                position: absolute;
                                top: 5px;
                                right: -7px;
                                background-color: #ff4c4c;
                                color: white;
                                font-size: 12px;
                                font-weight: bold;
                                border-radius: 50%;
                                width: 18px;
                                height: 18px;
                                text-align: center;
                                line-height: 18px;
                            }
                        </style>
                        <div style="padding-left: 100px;">
                            <div class="uk-flex uk-flex-middle">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="lower">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium uk-flex uk-flex-center uk-flex-middle">
                <div class=" uk-text-center">
                    @include('frontend.component.navigation')
                </div>
            </div>
        </div>
    </div>


</div>



</div>
<div class="mobile-header uk-hidden-large">
    <div class="mobile-upper">
        <div class="uk-container uk-container-center">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <div class="mobile-logo">
                    <a href="." title="{{ $system['seo_meta_title'] }}">
                        <img src="{{ $system['homepage_logo'] }}" alt="Mobile Logo">
                    </a>
                </div>
                <div class="mobile-widget">
                    <div class="uk-flex uk-flex-middle">
                        @if (auth()->guard('customer')->check())
                            <a href="{{ route('customer.profile') }}" class="btn btn-addCart">
                                <div style="font-size: 28px; color: #111;" class="fa fa-user"></div>
                            </a>
                        @else
                            <a href="{{ route('fe.auth.login') }}" class="btn btn-addCart">
                                <div style="font-size: 28px; color: #111;" class="fa fa-user"></div>
                            </a>
                        @endif
                        <a href="{{ write_url('thanh-toan') }}" class="btn btn-addCart">
                            <div class="cart-mini" onmouseover="showCartPopup()" onmouseleave="hideCartPopup()">
                                <img src="frontend/resources/img/shopping-bag.png" alt="cart image"
                                    style="width:28px; height: 28px; top: 5px">
                                <span id="cartTotalItem" class="cart-total-quantity">{{ $countMiniCart ?? 0 }}</span>
                            </div>
                        </a>

                        <a href="#mobileCanvas" class="mobile-menu-button" data-uk-offcanvas>
                            <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%"
                                preserveAspectRatio="none" viewBox="0 0 1536 1896.0833" class=""
                                fill="rgb(218,34,41)">
                                <path
                                    d="M1536 1344v128q0 26-19 45t-45 19H64q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26 0 45 19t19 45zm0-512v128q0 26-19 45t-45 19H64q-26 0-45-19T0 960V832q0-26 19-45t45-19h1408q26 0 45 19t19 45zm0-512v128q0 26-19 45t-45 19H64q-26 0-45-19T0 448V320q0-26 19-45t45-19h1408q26 0 45 19t19 45z">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="search-mobile">
        <form action="{{ write_url('tim-kiem') }}" class="uk-form form mobile-form">
            <div class="form-row">
                <input type="text" name="keyword" value="" class="input-text" placeholder="Nhập từ khóa">
                <button type="submit" value="" name="btn-search">
                    Tìm kiếm <i class="fa fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>
<div id="mobileCanvas" class="uk-offcanvas offcanvas">
    <div class="uk-offcanvas-bar">
        @if (isset($menu['mobile']))
            <ul class="l1 uk-nav uk-nav-offcanvas uk-nav uk-nav-parent-icon" data-uk-nav>
                @foreach ($menu['mobile'] as $key => $val)
                    @php
                        $name = $val['item']->name;
                        $canonical = write_url($val['item']->canonical, true, true);
                    @endphp
                    <li class="l1 {{ count($val['children']) ? 'uk-parent uk-position-relative' : '' }}">
                        <?php echo isset($val['children']) && is_array($val['children']) && count($val['children']) ? '<a href="#" title="" class="dropicon"></a>' : ''; ?>
                        <a href="{{ $canonical }}" title="{{ $name }}"
                            class="l1">{{ $name }}</a>
                        @if (count($val['children']))
                            <ul class="l2 uk-nav-sub">
                                @foreach ($val['children'] as $keyItem => $valItem)
                                    @php
                                        $name_2 = $valItem['item']->name;
                                        $canonical_2 = write_url($valItem['item']->canonical, true, true);
                                    @endphp
                                    <li class="l2">
                                        <a href="{{ $canonical_2 }}" title="{{ $name_2 }}"
                                            class="l2">{{ $name_2 }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
