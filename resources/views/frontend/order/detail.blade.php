@extends('frontend.homepage.layout')
@section('content')
    <div id="customer-container" class="customer-container  p-5">
        <div class="row">
            <div class="col-12 col-lg-3 mx-auto side-profile">
                @include('frontend.auth.customer.components.sidebar')
            </div>
            <div class="col-12 col-lg-9 mx-auto">
                <h4 class="text-center mb-3 mt-3 profile-title">Chi Tiết Đơn Hàng</h4>
                <div class="order-section px-5">
                    <!-- Thông tin người mua -->
                    <div class="order-header row mb-20"
                        style="background-color: rgb(246, 246, 246);
                            padding: 10px;">
                        <div class="order-info row">
                            <div class="col-lg-6">
                                <div class="order-item">
                                    <div class="label"><strong>Tên người mua:</strong></div>
                                    <div class="value">{{ $order->fullname }}</div>
                                </div>
                                <div class="order-item">
                                    <div class="label"><strong>Email:</strong></div>
                                    <div class="value">{{ $order->email }}</div>
                                </div>
                                <div class="order-item">
                                    <div class="label"><strong>Số điện thoại:</strong></div>
                                    <div class="value">{{ $order->phone }}</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="order-item">
                                    <div class="label"><strong>Địa chỉ:</strong></div>
                                    <div class="value">{{ $order->address }}</div>
                                </div>
                                <div class="order-item">
                                    <div class="label"><strong>Phường/Xã:</strong></div>
                                    <div class="value">{{ $order->ward_name }}</div>
                                </div>
                                <div class="order-item">
                                    <div class="label"><strong>Quận/Huyện:</strong></div>
                                    <div class="value">{{ $order->district_name }}</div>
                                </div>
                                <div class="order-item">
                                    <div class="label"><strong>Tỉnh/Thành phố:</strong></div>
                                    <div class="value">{{ $order->province_name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trạng thái đơn hàng -->
                    <div class="row order-status mb-20" style="background-color: rgb(246, 246, 246); padding: 10px;">
                        <div class="col-lg-6">
                            <div class="order-item align-items-center">
                                <div class="label"><strong>Trạng thái:</strong></div>
                                <div class="value">
                                    @if ($order->confirm == 'confirm')
                                        @if ($order->delivery == 'pending')
                                            <span class="text-warning"><strong>ĐANG CHỜ VẬN CHUYỂN</strong></span>
                                        @elseif ($order->delivery == 'processing')
                                            <span class="text-info"><strong>ĐANG VẬN CHUYỂN</strong></span>
                                        @elseif ($order->delivery == 'success')
                                            <span class="text-success"><strong>GIAO HÀNG THÀNH CÔNG</strong></span>
                                        @elseif ($order->delivery == 'returned' && $order->payment == 'refunded')
                                            <span class="text-info"><strong>ĐƠN HÀNG ĐANG ĐƯỢC HOÀN TRẢ</strong></span>
                                        @endif
                                    @elseif ($order->confirm == 'cancle')
                                        <span class="text-danger"><strong>ĐƠN HÀNG ĐÃ ĐƯỢC HỦY</strong></span>
                                    @elseif ($order->confirm == 'pending')
                                        <span class="text-primary"><strong>CHỜ XÁC NHẬN ĐƠN HÀNG</strong></span>
                                    @elseif ($order->confirm == 'returned')
                                        <span class="text-success"><strong>TRẢ HÀNG THÀNH CÔNG</strong></span>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 text-right">
                            @if ($order->confirm != 'cancle' && $order->payment == 'unpaid' && $order->delivery == 'pending')
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button cancelOrderButton">
                                    HỦY ĐƠN
                                </button>
                            @endif
                            @if (
                                $order->confirm == 'confirm' &&
                                    $order->payment == 'paid' &&
                                    $order->delivery == 'success' &&
                                    \Carbon\Carbon::parse($order->updated_at)->diffInDays(now()) < 15)
                                <button type="button" class="button returnOrderButton">
                                    TRẢ HÀNG
                                </button>
                            @endif


                        </div>
                    </div>
                    <script>
                        const orderDetailUrl = "{{ route('my-order.detail', ['id' => 'ORDER_ID']) }}";
                    </script>

                    <!-- Danh sách sản phẩm -->
                    <div class="order-items row "
                        style="background-color: rgb(246, 246, 246);
                            padding: 10px; margin-bottom: 10px;">
                        @foreach ($order->products as $key => $val)
                            @php

                                $name = $val->pivot->name;
                                $qty = $val->pivot->qty;
                                $price = convert_price($val->pivot->price, true);
                                $priceOriginal = convert_price($val->pivot->priceOriginal, true);
                                $subtotal = convert_price($val->pivot->price * $qty, true);
                                $image = image($val->image);
                            @endphp
                            <div class="order-item">
                                <img alt="Mặc định" height="100" src="{{ $image }}" width="100" />
                                <div class="item-details">
                                    <div class="item-name">
                                        {{ $name }}
                                    </div>
                                    <div class="item-category">
                                        Phân loại hàng: {{ $val->pivot->category }}
                                    </div>
                                    <div class="item-quantity">
                                        x{{ $qty }}
                                    </div>
                                </div>
                                <div class="item-price">
                                    <div class="text-danger">
                                        {{ $price }} đ
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" class="orderId" value="{{ $order->id }}">
                    <div class="orderData" data-order='@json($order)'></div>

                    <!-- Tổng tiền -->
                    <div class="order-footer row "
                        style="background-color: rgb(246, 246, 246);
                            padding: 10px;">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4 total-price">
                            <strong>Thành tiền:</strong>
                            {{ convert_price($order->cart['cartTotal'], true) }} đ
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('css')
    <style>
        .order-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .button.cancelOrderButton {
            background: linear-gradient(to right, #cc0000, #ff3300);
            color: white;
            text-transform: uppercase;
            border-radius: 5px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .mt-20 {
            margin-bottom: 20px
        }

        .button.cancelOrderButton:hover {
            background: darkred;
        }


        .order-item {
            display: flex;
        }

        .order-status .text-right {
            text-align: right;
        }

        .order-info {
            display: flex;
            justify-content: space-between;
        }

        .order-item {
            display: flex;
        }

        .label {
            flex: 0 0 40%;
            font-weight: bold;
        }

        .value {
            flex: 1;
        }

        .order-item strong {
            margin-right: 10px;
        }

        .profile-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .mb-20 {
            margin-bottom: 20px
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

        #backround-padding {
            background-color: rgb(246, 246, 246);
            padding: 20px;
        }

        .order-header .btn {
            font-size: 14px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            margin-top: 10px;
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

        @media (max-width: 992px) {

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
                width: 100%;
                text-align: center;
                padding: 10px 0;
                background: #fff;
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


        @media (max-width: 992px) {
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
        document.addEventListener("DOMContentLoaded", function() {
            function updateOrderSectionClass() {
                const orderSection = document.querySelector('.order-section');
                if (window.innerWidth < 992) {
                    orderSection.classList.remove('px-5');
                } else {
                    orderSection.classList.add('px-5');
                }
            }
            updateOrderSectionClass();
            window.addEventListener('resize', updateOrderSectionClass);
        });
    </script>
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
@endsection
