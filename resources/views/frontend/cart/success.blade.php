@extends('frontend.homepage.layout')
@section('content')
    <div class="cart-success">
        <div class="panel-head"></div>
        <div class="panel-body">
            <div class="checkout-box">
                <div class="checkout-box-head pb-3 mb-3">
                    <h2 class="cart-heading"><span>Đặt hàng thành công</span></h2>
                    <div class="row g-3 d-flex align-items-center">
                        <style>
                            .detail-order {
                                font-size: 16px;
                                padding: 10px;
                                border-radius: 8px;
                                font-weight: 300;
                                margin-left: 10px;
                            }

                            .detail-order div {
                                margin-bottom: 5px;
                                padding: 5px;
                            }
                        </style>
                        <div class="detail-order">
                            <div><strong>Mã đơn hàng:</strong> #{{ $order->code }}</div>
                            <div><strong>Ngày đặt hàng:</strong> {{ convertDateTime($order->created_at) }}</div>
                            <div><strong>Tên người nhận:</strong> {{ $order->fullname }}</div>
                            @php
                                $province = $order->provinces->first()->name;
                                $district = $order->provinces
                                    ->first()
                                    ->districts->where('code', $order->district_id)
                                    ->first()->name;
                                $ward = $order->provinces
                                    ->first()
                                    ->districts->where('code', $order->district_id)
                                    ->first()
                                    ->wards->where('code', $order->ward_id)
                                    ->first()->name;
                            @endphp
                            <div><strong>Địa chỉ: </strong> {{ $order->address }}, {{ $ward }}, {{ $district }},
                                {{ $province }}</div>
                            <div><strong>Số điện thoại: </strong> {{ $order->phone }}</div>
                            <div><strong>Hình thức thanh toán: </strong>
                                {{ array_column(__('payment.method'), 'title', 'name')[$order->method] ?? '-' }}
                            </div>

                            @if (isset($template))
                                @include($template)
                            @endif
                        </div>
                    </div>
                </div>

                <div class="checkout-box-body">
                    <table class="table table-bordered align-middle">
                        <thead class="table-bordered">
                            <tr>
                                <th class="text-start">Tên sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Giá niêm yết</th>
                                <th class="text-end">Giá bán</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $carts = $order->products;
                            @endphp
                            @foreach ($carts as $key => $val)
                                @php
                                    $name = $val->pivot->name;
                                    $qty = $val->pivot->qty;
                                    $price = convert_price($val->pivot->price, true);
                                    $priceOriginal = convert_price($val->pivot->priceOriginal, true);
                                    $subtotal = convert_price($val->pivot->price * $qty, true);
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
                                <td colspan="4" class="text-end">Mã giảm giá</td>
                                <td><strong>{{ $order->promotion['code'] }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Tổng giá trị sản phẩm</td>
                                <td><strong>{{ convert_price($order->cart['cartTotal'], true) }}đ</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Tổng giá trị khuyến mãi</td>
                                <td><strong>{{ convert_price($order->promotion['discount'], true) }}đ</strong></td>
                            </tr>
                            <tr class="total_payment">
                                <td colspan="4" class="text-end"><span>Tổng thanh toán</span></td>
                                <td>{{ convert_price($order->cart['cartTotal'] - $order->promotion['discount'], true) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <style>
                    .table-bordered {
                        border: 1px solid #dee2e6;
                    }

                    .table-bordered th,
                    .table-bordered td {
                        border: 1px solid #dee2e6;
                    }

                    .table-bordered thead th {
                        background-color: #f8f9fa;
                    }

                    .table-bordered tbody td {
                        background-color: #ffffff;
                    }

                    .table-bordered tfoot td {
                        background-color: #f1f3f5;
                    }

                    .table-bordered tfoot td {
                        border: none;
                        background-color: #f1f3f5;
                    }
                </style>
            </div>
        </div>
    </div>
@endsection

<input type="hidden" id="province_id" name="province_id" value="{{ $order->province_id }}">
<input type="hidden" id="district_id" name="district_id" value="{{ $order->district_id }}">
<input type="hidden" id="ward_id" name="ward_id" value="{{ $order->ward_id }}">
