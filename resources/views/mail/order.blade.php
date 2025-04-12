<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thông tin đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .cart-heading span {
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

        .checkout-box {
            padding: 15px 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .checkout-box-head {
            margin-bottom: 30px;
            border-bottom: 1px solid #ccc;
        }

        .order-title {
            border-radius: 16px;
            padding: 10px 15px;
            font-weight: 700;
            font-size: 16px;
        }

        .order-date {
            display: flex;
            align-items: center;
            font-size: 16px;
            font-weight: bold;
        }

        .table thead th {
            font-weight: 500;
            font-size: 14px;
            vertical-align: middle;
            text-align: center;
            padding: 12px 15px;
        }

        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            font-size: 14px;
            color: #000;
            border-bottom: 1px solid #ccc;
        }

        .table tfoot td {
            padding: 8px;
        }

        .table td:last-child {
            text-align: right;
        }

        .table tbody tr:nth-child(2n) td {
            background-color: #d9d9d9;
        }

        .total_payment {
            font-weight: bold;
            font-size: 24px;
        }

        .detail-order div {
            margin-bottom: 5px;
            padding: 5px;
        }

        .cart-success {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cart-heading {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="cart-success">
        <div class="panel-body">
            <h2 class="cart-heading  mb-4"><span>Thông tin đơn hàng</span></h2>
            <div class="checkout-box">
                <div class="checkout-box-head">
                    <div class="row align-items-center">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-8 detail-order">
                            <div><strong>Mã đơn hàng:</strong> #{{ $data['order']->code }}</div>
                            <div><strong>Ngày đặt hàng:</strong> {{ convertDateTime($data['order']->created_at) }}</div>
                            <div><strong>Tên người nhận:</strong> {{ $data['order']->fullname }}</div>
                            <div><strong>Email:</strong> {{ $data['order']->email }}</div>
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
                            <div><strong>Địa chỉ:</strong> {{ $data['order']->address }}, {{ $ward }},
                                {{ $district }}, {{ $province }}</div>
                            <div><strong>Số điện thoại:</strong> {{ $data['order']->phone }}</div>
                            <div><strong>Hình thức thanh toán:</strong>
                                {{ array_column(__('payment.method'), 'title', 'name')[$data['order']->method] ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="checkout-box-body mt-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Tên sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Giá niêm yết</th>
                                <th class="text-end">Giá bán</th>
                                <th class="text-end">Thành tiền</th>
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
                                    <td class="text-start">{{ $name }}</td>
                                    <td class="text-center">{{ $qty }}</td>
                                    <td class="text-end">{{ $priceOriginal }}đ</td>
                                    <td class="text-end">{{ $price }}đ</td>
                                    <td class="text-end"><strong>{{ $subtotal }}đ</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">Mã giảm giá</td>
                                <td class="text-end">
                                    <strong>{{ $data['cartPromotion']['selectedPromotion']->code ?? '-' }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị sản phẩm</td>
                                <td class="text-end">
                                    <strong>{{ convert_price($data['cartCaculate']['cartDiscount'] + $data['cartCaculate']['cartTotal'], true) }}đ</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị khuyến mãi</td>
                                <td class="text-end">
                                    <strong>{{ convert_price($data['cartCaculate']['cartDiscount'], true) }}đ</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">Phí giao hàng</td>
                                <td class="text-end"><strong>0đ</strong></td>
                            </tr>
                            <tr class="total_payment">
                                <td colspan="4"><span>Tổng thanh toán</span></td>
                                <td class="text-end">{{ convert_price($data['cartCaculate']['cartTotal'], true) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
