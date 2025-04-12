<div id="header" class="pc-header d-none d-lg-block sticky-top" style="background: #fff">
    <div class="upper">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="company-name text-start w-50">
                    {{ $system['homepage_company'] }}
                </div>

                <div class="text-end w-50">
                    @if (auth()->guard('customer')->check())
                        <div class="header-cart d-flex justify-content-end">
                            <div class="d-flex align-items-center">
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
                        <div class="header-cart d-flex justify-content-end">
                            <div class="d-flex align-items-center">
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
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="logo d-flex align-items-center">
                    <a href="/">
                        <img src="{{ $system['homepage_favicon'] }}" alt="Logo" class="img-fluid">
                    </a>
                </div>
                <div class="header-search">
                    <form action="{{ write_url('tim-kiem') }}" class="form" style="margin-bottom: 0;">
                        <input type="text" name="keyword" placeholder="Nhập từ khóa" value=""
                            class="input-text">
                        <button type="submit" value="" name="">
                            Tìm kiếm
                        </button>
                    </form>
                </div>

                <form action="{{ route('product.catalogue.searchProductByImage') }}" method="POST"
                    enctype="multipart/form-data" class="d-inline-block">
                    @csrf
                    <input type="file" name="image" id="image_product" style="display: none;"
                        onchange="this.form.submit()">
                    <button type="button" class="btn-login btn btn-sm"
                        onclick="document.getElementById('image_product').click()">Tìm ảnh</button>
                </form>

                <div class="header-toolbox">
                    <div class="d-flex align-items-center">
                        <div class="header-cart">
                            <div class="d-flex align-items-center">
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
                            </div>
                        </div>

                        <div class="ms-5">
                            <div class="d-flex align-items-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lower">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="text-center">
                    @include('frontend.component.navigation')
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Mobile Header --}}
<div class="mobile-header d-block d-lg-none bg-white border-bottom">
    <div class="mobile-upper py-2">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                {{-- Logo --}}
                <div class="mobile-logo">
                    <a href="." title="{{ $system['seo_meta_title'] }}">
                        <img src="{{ $system['homepage_logo'] }}" alt="Mobile Logo" style="max-height: 40px;">
                    </a>
                </div>

                {{-- Widget --}}
                <div class="mobile-widget">
                    <div class="d-flex align-items-center gap-3">
                        {{-- Tài khoản --}}
                        @if (auth()->guard('customer')->check())
                            <a href="{{ route('customer.profile') }}" class="btn btn-link p-0">
                                <i class="fa fa-user" style="font-size: 28px; color: #111;"></i>
                            </a>
                        @else
                            <a href="{{ route('fe.auth.login') }}" class="btn btn-link p-0">
                                <i class="fa fa-user" style="font-size: 28px; color: #111;"></i>
                            </a>
                        @endif

                        {{-- Giỏ hàng --}}
                        <a href="{{ write_url('thanh-toan') }}" class="btn btn-link p-0 position-relative">
                            <img src="{{ asset('frontend/resources/img/shopping-bag.png') }}" alt="Cart"
                                style="width:28px; height:28px;">
                            <span id="cartTotalItem"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $countMiniCart ?? 0 }}
                            </span>
                        </a>

                        {{-- Button mở menu --}}
                        <button class="btn p-0 border-0" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#mobileCanvas" aria-controls="mobileCanvas">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 1536 1896.0833" fill="rgb(218,34,41)">
                                <path d="M1536 1344v128q0 26-19 45t-45 19H64q-26
                                    0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26
                                    0 45 19t19 45zm0-512v128q0 26-19 45t-45
                                    19H64q-26 0-45-19T0 960V832q0-26 19-45t45-19h1408q26
                                    0 45 19t19 45zm0-512v128q0 26-19 45t-45
                                    19H64q-26 0-45-19T0 448V320q0-26 19-45t45-19h1408q26
                                    0 45 19t19 45z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search form --}}
    <div class="search-mobile py-2 border-top">
        <div class="container">
            <form action="{{ write_url('tim-kiem') }}" class="d-flex flex-column w-100">
                <div class="d-flex justify-content-end">
                    <button type="submit" name="btn-search" class="btn btn-outline-primary">
                        Tìm kiếm
                    </button>
                </div>
                <div>
                    <input type="text" name="keyword" value="" class="form-control"
                        placeholder="Nhập từ khóa" style="border-radius: 0 !important">
                </div>
            </form>
        </div>
    </div>


</div>

<!-- Offcanvas menu dùng Bootstrap 5 -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileCanvas" aria-labelledby="mobileCanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileCanvasLabel">Danh mục</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        @if (isset($menu['mobile']))
            <ul class="list-group list-group-flush">
                @foreach ($menu['mobile'] as $key => $val)
                    @php
                        $name = $val['item']->name;
                        $canonical = write_url($val['item']->canonical, true, true);
                        $hasChildren = isset($val['children']) && is_array($val['children']) && count($val['children']);
                    @endphp

                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ $canonical }}" class="text-decoration-none">{{ $name }}</a>
                            @if ($hasChildren)
                                <button class="btn btn-sm btn-link p-0" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-{{ $key }}" aria-expanded="false"
                                    aria-controls="collapse-{{ $key }}">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                            @endif
                        </div>

                        @if ($hasChildren)
                            <div class="collapse mt-2" id="collapse-{{ $key }}">
                                <ul class="list-group list-group-flush ps-3">
                                    @foreach ($val['children'] as $keyItem => $valItem)
                                        @php
                                            $name_2 = $valItem['item']->name;
                                            $canonical_2 = write_url($valItem['item']->canonical, true, true);
                                        @endphp
                                        <li class="list-group-item">
                                            <a href="{{ $canonical_2 }}"
                                                class="text-decoration-none">{{ $name_2 }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>



<style>
    @media (min-width: 992px) {
        .col-lg-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }
    }
</style>
<style>
    .input-text {
        flex-grow: 1;
        padding: 10px 40px 10px 10px;
        border: 1px solid #ccc;
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

    .cart-popup::before {
        content: "";
        position: absolute;
        top: -10px;
        right: 20px;
        border-width: 10px;
        border-style: solid;
        border-color: transparent transparent white transparent;
    }

    .cart-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .cart-item-image {
        position: relative;
        margin-right: 10px;
    }

    .cart-item-image img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
        border: 1px solid gray;
    }

    .cart-item-number {
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

    .cart-item-info .title {
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 280px;
        display: block;
    }

    .checkout-btn {
        width: 40%;
        padding: 10px;
        background: linear-gradient(to right, #003366, #3399ff);
        color: white;
        text-align: center;
        border: none;
        cursor: pointer;
        font-size: 16px;
        border-radius: 10px;
        margin: 15px auto 0;
        font-weight: 600;
    }

    .checkout-btn:hover {
        background: #003366;
    }

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
