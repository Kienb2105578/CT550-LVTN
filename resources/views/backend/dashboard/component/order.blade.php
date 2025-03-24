<div class="ibox">
    <div class="ibox-title">
        <h5>Danh sách đơn hàng mới</h5>
    </div>
    <div class="ibox-content">
        <div class="mb10">
            <div class="text-danger" style="font-size:12px;"><i>*Tổng cuối là tổng chưa bao gồm giảm giá</i></div>
        </div>
        <table class="table table-striped table-bordered order-table">
            <thead>
                <tr>
                    <th class="text-center" style="width:7%">Mã</th>
                    <th class="text-center d-none d-md-table-cell" style="width:10%">Ngày tạo</th>
                    <th class="text-center d-none d-md-table-cell">Khách hàng</th>
                    <th class="text-center d-none d-md-table-cell" style="width:10%">Giảm giá (vnđ)</th>
                    <th class="text-center" style="width:10%">Tổng cuối (vnđ)</th>
                    <th class="text-center" style="width:13%"></th>
                </tr>
            </thead>
            <tbody>
                @if (isset($newOrders) && is_object($newOrders))
                    @foreach ($newOrders as $order)
                        <tr>
                            <td class="text-center">
                                <a href="{{ route('order.detail', $order->id) }}">{{ $order->code }}</a>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                {{ convertDateTime($order->created_at, 'd-m-Y') }}
                            </td>
                            <td class="d-none d-md-table-cell">
                                <strong>Họ và tên:</strong> {{ $order->fullname }} - {{ $order->phone }} <br>
                                <strong>Địa chỉ:</strong> {{ $order->address }} - {{ $order->ward_name }} -
                                {{ $order->district_name }} - {{ $order->province_name }}
                            </td>
                            <td class="text-center d-none d-md-table-cell order-discount">
                                {{ convert_price($order->promotion['discount'], true) }} đ
                            </td>
                            <td class="text-center order-total">
                                {{ convert_price($order->cart['cartTotal'], true) }} đ
                            </td>
                            <td class="text-center">
                                <a href="{{ route('order.detail', $order->id) }}">Xem chi tiết</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>

        </table>
    </div>
</div>
