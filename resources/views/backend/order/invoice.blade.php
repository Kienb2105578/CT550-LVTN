<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn #{{ $order->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }

        .header {
            text-align: center;
        }

        .header img {
            max-width: 100px;
        }

        .info {
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .total {
            text-align: right;
            margin-top: 10px;
        }

        .mt10 {
            margin-top: 10px;
        }

        .mb10 {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table style="width: 100%; text-align: center; vertical-align: middle;">
                <tr>
                    <td style="width: 50%; text-align: left; vertical-align: middle;">
                        <h3 style="margin: 0;">INCOM</h3>
                        <h3 style="margin: 0;">CỬA HÀNG NỘI THẤT</h3>
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: middle;">
                        <h3 style="margin: 0;">HÓA ĐƠN #{{ $order->id }}</h3>
                        <p style="margin: 0;">Ngày: {{ date('d/m/Y', strtotime($order->created_at)) }}</p>
                    </td>
                </tr>
            </table>

        </div>

        <div class="info" style="margin-top: 30px;">
            <strong>Khách hàng:</strong> {{ $order->fullname }} <br>
            <strong>Điện thoại:</strong> {{ $order->phone }} <br>
            <strong>Địa chỉ:</strong> {{ $order->address }}, {{ $order->ward_name }},
            {{ $order->district_name }}, {{ $order->province_name }}
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>MỤC</th>
                    <th>SỐ LƯỢNG</th>
                    <th>ĐƠN GIÁ</th>
                    <th>THÀNH TIỀN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $product)
                    @php
                        $name = $product->pivot->name;
                        $qty = $product->pivot->qty;
                        $price = convert_price($product->pivot->price, true);
                        $priceOriginal = convert_price($product->pivot->priceOriginal, true);
                        $subtotal = convert_price($product->pivot->price * $qty, true);
                    @endphp
                    <tr>
                        <td>{{ $name }}</td>
                        <td>{{ $qty }}</td>
                        <td>{{ number_format($product->pivot->price, 0, ',', '.') }}đ</td>
                        <td>{{ $subtotal }}đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px; width: 50%; float: right; text-align: right; padding-top: 10px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Tổng tạm</td>
                        <td>{{ convert_price($order->cart['cartTotal'], true) }} ₫</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Giảm giá</td>
                        <td style="color: red;">- {{ convert_price($order->promotion['discount'], true) }} ₫</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Vận chuyển</td>
                        <td>0 ₫</td>
                    </tr>

                    <tr>
                        <td style="font-weight: bold; text-transform: uppercase; padding-top:20px ;">Tổng cuối</td>
                        <td style="color: blue; font-weight: bold; font-size: 18px; padding-top:20px; ">
                            {{ convert_price($order->cart['cartTotal'] - $order->promotion['discount'], true) }} ₫
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
</body>

</html>
