@extends('frontend.homepage.layout')
@section('content')
    <div id="customer-container" class=" p-5">
        <div class="row">
            <div id="sidebar-menu" class="col-12 col-lg-3 mx-auto">
                <div class="list-group">
                    <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action">
                        Tài khoản của tôi
                    </a>
                    <a href="{{ route('my-order.index') }}" class="list-group-item list-group-item-action active">
                        Đơn hàng đã mua
                    </a>
                    <a href="{{ route('customer.password.change') }}" class="list-group-item list-group-item-action">
                        Đổi mật khẩu
                    </a>
                    <a href="{{ route('customer.logout') }}" class="list-group-item list-group-item-action">
                        Đăng xuất
                    </a>
                </div>
            </div>
            <div id="order-content" class="col-12 col-lg-9 mx-auto">
                <form action="{{ route('my-order.index') }}" method="post" class="form-order">
                    @csrf
                    <h4 class="text-center mb-3">Đơn hàng đã mua</h4>
                    <div id="order-tabs" class="order-section">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-confirm="" data-payment="" data-delivery=""
                                    data-status="all">
                                    Tất cả
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-confirm="pending" data-payment="" data-delivery="">
                                    Chờ Xác Nhận
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-confirm="confirm" data-payment="" data-delivery="">
                                    Đã Xác Nhận
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-confirm="confirm" data-payment="paid" data-delivery="processing">
                                    Đang Vận Chuyển
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-confirm="confirm" data-payment="paid" data-delivery="success">
                                    Đã Giao
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-confirm="cancle" data-payment="" data-delivery="">
                                    Đã Hủy
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-payment="refunded" data-delivery="returned">
                                    Trả Hàng
                                </a>
                            </li>
                        </ul>

                    </div>
                    <div id="order-list" class="order-section">
                        @foreach ($orders as $order)
                            @foreach ($order['products'] as $product)
                                <div class="order-item">
                                    <img class="order-image" alt="Product image" src="{{ $product['product_image'] }}" />
                                    <div class="item-details">
                                        <div class="item-name">{{ $product['product_name'] }}</div>
                                        @if ($product['variant_name'] != null)
                                            <div class="item-category">
                                                Phân loại hàng: {{ $product['variant_name'] }}
                                            </div>
                                        @endif
                                        <div class="item-quantity">x{{ $product['qty'] }}</div>
                                    </div>
                                    <div class="item-price">
                                        <div class="text-danger">
                                            @if ($product['price'] != $product['priceOriginal'])
                                                <span class="cart-price-old mr10">
                                                    {{ convert_price($product['priceOriginal'] * $product['qty'], true) }}đ
                                                </span>
                                            @endif
                                            <span class="cart-price-sale">
                                                {{ convert_price($product['price'] * $product['qty'], true) }}đ
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="order-footer">
                                <div class="total-price">
                                    Thành tiền:
                                    ₫{{ number_format($order['products']->sum(fn($product) => $product['price'] * $product['qty']), 0, ',', '.') }}
                                </div>
                                <div>
                                    @if (isset($order['order_id']) && is_numeric($order['order_id']))
                                        <a href="{{ route('my-order.detail', ['id' => $order['order_id']]) }}"
                                            class="btn btn-outline-secondary">Chi tiết</a>
                                    @else
                                        <p>Không có mã đơn hàng hợp lệ.</p>
                                    @endif
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <style>
        .profile-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }


        .profile-section img {
            border-radius: 50%;
        }

        .profile-section .username {
            font-size: 18px;
            font-weight: bold;
        }

        .profile-section .edit-profile {
            font-size: 14px;
            color: #888;
        }

        .nav-link {
            color: #333;
            font-size: 16px;
        }

        .nav-link.active {
            color: #ee4d2d;
        }

        .order-section {
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .order-header .shop-name {
            font-size: 18px;
            font-weight: bold;
        }

        .order-header .btn {
            font-size: 14px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .order-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .order-item .item-details {
            flex-grow: 1;
            margin-left: 20px;
        }

        .order-item .item-details .item-name {
            font-size: 16px;
            font-weight: bold;
        }

        .order-item .item-details .item-category {
            font-size: 14px;
            color: #888;
        }

        .order-item .item-details .item-quantity {
            font-size: 14px;
            color: #888;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-footer .total-price {
            font-size: 18px;
            font-weight: bold;
            color: #ee4d2d;
        }

        .order-footer .btn {
            font-size: 14px;
        }

        /* Tạo thanh trượt cho order-section */
        .order-section {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 25px;
        }

        .order-item {
            display: flex;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .order-section::-webkit-scrollbar {
            width: 8px;
        }

        .order-section::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
        }

        .order-section::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }

        #sidebar-menu {
            background: #fff;
            border-radius: 5px;
            padding: 15px;
        }

        #order-content {
            background: #fff;
            border-radius: 5px;
            padding: 20px;
        }

        #order-tabs {
            margin-bottom: 20px;
        }

        /* Order List */
        #order-list {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 25px;
        }

        @media (max-width: 960px) {

            body,
            html {
                margin: 0;
                padding: 0;
                width: 100%;
            }

            .container {
                max-width: 100%;
                padding: 0;
            }

            .row {
                display: flex;
                flex-direction: column;
                width: 100%;
            }

            #sidebar-section {
                order: -1;
                /* Đưa sidebar lên trên */
                width: 100%;
                text-align: center;
                padding: 10px 0;
                background: #fff;
                /* Giữ nền trắng để dễ nhìn */
            }

            #sidebar-menu {
                position: sticky;
                top: 20px;
                height: fit-content;
            }

            .list-group {
                display: flex;
                justify-content: center;
                padding: 10px;
            }

            .list-group-item {
                flex: 1;
                text-align: center;
            }

            #content-section {
                width: 100%;
                padding: 10px;
            }
        }


        @media (max-width: 767px) {
            #sidebar-menu {
                margin-bottom: 15px;
            }

            .list-group {
                flex-direction: row;
                overflow-x: auto;
                white-space: nowrap;
            }

            .list-group-item {
                font-size: 14px;
                padding: 10px;
            }

            #order-content {
                padding: 10px;
            }

            .order-item img {
                width: 50px;
                height: 50px;
            }
        }

        @media (max-width: 575px) {
            .nav-tabs {
                flex-direction: column;
                align-items: center;
            }

            .nav-item {
                width: 100%;
            }

            .nav-link {
                text-align: center;
                font-size: 14px;
            }

            .order-item {
                padding: 8px;
            }

            .order-footer {
                flex-direction: column;
                text-align: center;
            }

            .order-footer .btn {
                margin-top: 10px;
            }
        }

        @media (max-width: 415px) {
            #customer-container {
                padding: 5px;
            }

            .list-group {
                flex-direction: column;
            }

            .list-group-item {
                font-size: 13px;
                padding: 8px;
            }

            .order-item {
                flex-direction: column;
                padding: 5px;
            }

            .order-item img {
                width: 20px;
                height: 20px;
            }

            .order-footer {
                font-size: 12px;
            }

            .btn-main {
                font-size: 12px;
                padding: 5px 10px;
            }

            .form-order {
                padding: 0px 0px;
            }
        }
    </style>
    <style>
        .btn-main {
            height: 33px;
            background: #da2229;
            text-transform: uppercase;
            color: #fff;
            font-weight: 600;
            right: 5px;
            top: 6px;
            border: 12px;
            padding: 0 20px;
            border-radius: 5px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function updateContainerClass() {
                const container = document.getElementById("customer-container");
                if (window.innerWidth > 960) {
                    container.classList.add("container");
                } else {
                    container.classList.remove("container");
                }
            }
            updateContainerClass();
            window.addEventListener("resize", updateContainerClass);
        });

        var orderDetailUrl = "{{ route('my-order.detail', ['id' => '__ID__']) }}";
    </script>
@endsection
