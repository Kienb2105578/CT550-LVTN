<!DOCTYPE html>
<html>

<head>
    <style>
        .cart-success {
            padding: 30px 10px;
        }

        @media (min-width: 1220px) {
            .cart-success {
                width: 800px;
                margin: 0 auto;
            }
        }

        .cart-success .cart-heading {
            text-align: center;
            margin-bottom: 30px;
        }

        .cart-success .cart-heading>span {
            text-transform: uppercase;
            font-weight: 700;
        }

        .discover-text>* {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 16px;
            cursor: pointer;
            color: #fff;
        }

        .discover-text {
            text-align: center;
        }

        .checkout-box {
            padding: 15px 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-success .panel-body {
            margin-bottom: 40px;
        }

        .checkout-box-head {
            margin-bottom: 30px;
        }

        .checkout-box-head .order-title {
            border-radius: 16px;
            padding: 10px 15px;
            font-weight: 700;
            font-size: 16px;
        }

        .checkout-box-head .order-date {
            display: flex;
            align-items: center;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .cart-success .table {
            width: 100%;
            border-spacing: 0;
            border-collapse: inherit;
        }

        .cart-success .table thead>tr th {
            font-weight: 500;
            font-size: 14px;
            vertical-align: middle;
            border-bottom: 2px solid #dee2e6;
            text-align: center;
            border: none;
            padding: 12px 15px;
        }

        .cart-success tbody tr td {
            padding: 12px 15px;
            vertical-align: middle;
            font-size: 14px;
            color: #000;
            border-bottom: 1px solid #ccc;
        }

        .cart-success tfoot {
            background: #fff;
        }

        .cart-success tfoot tr td {
            padding: 8px;
        }

        .cart-success .table td:last-child {
            text-align: right;
        }

        .cart-success .table tbody tr:nth-child(2n) td {
            background-color: #d9d9d9;
        }

        .total_payment {
            font-weight: bold;
            font-size: 24px;
        }

        .panel-foot .checkout-box div {
            margin-bottom: 15px;
            font-weight: 500;
        }

        .uk-text-left {
            text-align: left;
        }

        .uk-text-right {
            text-align: right;
        }

        .uk-text-center {
            text-align: center;
        }

        .detail-order div {
            margin-bottom: 5px;
            padding: 5px;
        }
    </style>
</head>

<body>
    <div class="cart-success">
        <div class="panel-body">
            <h2 class="cart-heading"><span>Thông tin đơn hàng</span></h2>
            <div class="checkout-box">
                <div class="checkout-box-head" style="border-bottom: 1px solid #ccc;">
                    <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                        <div class="uk-width-large-1-3"></div>
                        <div class="detail-order">
                            <div><strong>Mã đơn hàng:</strong> #{{ $data['order']->code }}</div>
                            <div><strong>Ngày đặt hàng:</strong> {{ convertDateTime($data['order']->created_at) }}</div>
                            <div><strong>Tên người nhận: </strong>{{ $data['order']->fullname }}<span></span></div>
                            <div><strong>Email:</strong> {{ $data['order']->email }}<span></span></div>
                            @php
                                $province = $data['order']->provinces->first()->name;
                                $district = $data['order']->provinces
                                    ->first()
                                    ->districts->where('code', $data['order']->district_id)
                                    ->first()->name;
                                $ward = $data['order']->provinces
                                    ->first()
                                    ->districts->where('code', $data['order']->district_id)
                                    ->first()
                                    ->wards->where('code', $data['order']->ward_id)
                                    ->first()->name;
                            @endphp
                            <div><strong>Địa chỉ: </strong>{{ $data['order']->address }}, {{ $ward }},
                                {{ $district }},
                                <span>{{ $province }}<span></span>
                            </div>
                            <div><strong>Số điện thoại: </strong>{{ $data['order']->phone }}<span></span></div>
                            <div><strong>Hình thức thanh toán:</strong>
                                {{ array_column(__('payment.method'), 'title', 'name')[$data['order']->method] ?? '-' }}<span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="checkout-box-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="uk-text-left">Tên sản phẩm</th>
                                <th class="uk-text-center">Số lượng</th>
                                <th class="uk-text-right">Giá niêm yết</th>
                                <th class="uk-text-right">Giá bán</th>
                                <th class="uk-text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['carts'] as $key => $val)
                                @php
                                    $name = $val->name;
                                    $qty = $val->qty;
                                    $price = convert_price($val->price, true);
                                    $priceOriginal = convert_price($val->priceOriginal, true);
                                    $subtotal = convert_price($val->price * $qty, true);
                                @endphp
                                <tr>
                                    <td class="uk-text-left">{{ $name }}</td>
                                    <td class="uk-text-center">{{ $qty }}</td>
                                    <td class="uk-text-right">{{ $priceOriginal }}đ</td>
                                    <td class="uk-text-right">{{ $price }}đ</td>
                                    <td class="uk-text-right"><strong>{{ $subtotal }}đ</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">Mã giảm giá</td>
                                <td><strong>{{ $data['cartPromotion']['selectedPromotion']->code ?? '-' }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị sản phẩm</td>
                                <td><strong>{{ convert_price($data['cartCaculate']['cartDiscount'] + $data['cartCaculate']['cartTotal'], true) }}đ</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị khuyến mãi</td>
                                <td><strong>{{ convert_price($data['cartCaculate']['cartDiscount'], true) }}đ</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">Phí giao hàng</td>
                                <td><strong>0đ</strong></td>
                            </tr>
                            <tr class="total_payment">
                                <td colspan="4"><span>Tổng thanh toán</span></td>
                                <td>{{ convert_price($data['cartCaculate']['cartTotal'], true) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
