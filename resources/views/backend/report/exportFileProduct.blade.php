<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Doanh Thu</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            /* Giảm kích thước chữ */
        }

        .container {
            width: 100%;
            margin: auto;
            padding: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            /* Chỉnh từ fixed thành auto để cột tự co giãn */
            font-size: 10px;
            /* Thu nhỏ chữ trong bảng */
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 5px;
            /* Giảm padding để bảng nhỏ gọn hơn */
            text-align: center;
            vertical-align: middle;
            /* Căn giữa nội dung theo chiều dọc */
            word-wrap: break-word;
            /* Xuống dòng khi văn bản quá dài */
            white-space: normal;
            /* Cho phép xuống dòng */
        }

        .table th {
            background-color: #f2f2f2;
        }

        /* Cố định độ rộng tối thiểu để tránh tràn */
        .table th:nth-child(1),
        .table td:nth-child(1) {
            width: 5%;
        }

        .table th:nth-child(2),
        .table td:nth-child(2) {
            width: 20%;
            /* Tên sản phẩm */
        }

        .table th:nth-child(3),
        .table td:nth-child(3),
        .table th:nth-child(4),
        .table td:nth-child(4),
        .table th:nth-child(5),
        .table td:nth-child(5),
        .table th:nth-child(6),
        .table td:nth-child(6),
        .table th:nth-child(7),
        .table td:nth-child(7) {
            width: 12%;
            /* Các cột số */
        }

        /* Định dạng tổng doanh thu */
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 12px;
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
                        <h3 style="margin: 0;">BÁO CÁO DOANH THU THEO SẢN PHẨM</h3>
                    </td>
                </tr>
            </table>
        </div>
        @php
            $total_revenue = 0;
        @endphp
        <table class="table" style="width: 100%; table-layout: fixed; word-wrap: break-word;">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Số khách hàng</th>
                    <th>Số đơn hàng</th>
                    <th>Giá gốc</th>
                    <th>Giảm giá</th>
                    <th>Doanh thu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $index => $report)
                    @php
                        $realRevenue = $report['sum_revenue'] - $report['sum_discount'];
                        $total_revenue += $realRevenue;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $report['sku'] }}</td>
                        <td>{{ $report['product_name'] }}</td>
                        <td>{{ $report['count_customer'] }}</td>
                        <td>{{ $report['count_order'] }}</td>
                        <td>{{ number_format($report['sum_revenue']) }} ₫</td>
                        <td>{{ number_format($report['sum_discount']) }} ₫</td>
                        <td>{{ number_format($report['sum_revenue'] - $report['sum_discount']) }} ₫</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px; width: 50%; float: right; text-align: right; padding-top: 10px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="font-weight: bold; text-transform: uppercase; padding-top:20px ;">Tổng doanh thu</td>
                        <td style="color: blue; font-weight: bold; font-size: 18px; padding-top:20px; ">
                            <span>{{ number_format($total_revenue) }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
