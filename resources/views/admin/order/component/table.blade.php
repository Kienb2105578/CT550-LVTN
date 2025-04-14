<div class="table-responsive">
    <table class="table  order-table">
        <thead>
            <tr>
                <th>Mã đơn hàng</th>
                <th>Ngày đặt hàng</th>
                <th>Khách hàng</th>
                {{-- <th class="text-right">Giảm khuyến mại</th> --}}
                <th class="text-right">Doanh thu</th>
                <th class="text-center">Trạng thái</th>
                <th style="width: 60px !important">Thanh toán</th>
                <th>Giao hàng</th>
                <th>Hình thức</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($orders) && is_object($orders))
                @foreach ($orders as $order)
                    <tr>
                        <input type="hidden" value="{{ $order->id }}" class=" checkBoxItem">
                        <td>
                            <a href="{{ route('order.detail', $order->id) }}">{{ $order->code }}</a>
                        </td>
                        <td>
                            {{ convertDateTime($order->created_at, 'd-m-Y') }}
                        </td>
                        <td>
                            <div><b>N:</b> {{ $order->fullname }}</div>
                            <div><b>P:</b> {{ $order->phone }}</div>
                            <div><b>A:</b> {{ $order->address }},
                                {{ $order->ward_name }}, {{ $order->district_name }}, {{ $order->province_name }}</div>
                        </td>

                        {{-- <td class="text-right order-discount">
                            {{ convert_price($order->promotion['discount'], true) }}đ
                        </td> --}}
                        <td class="text-right order-total">
                            {{ convert_price($order->cart['cartTotal'], true) }}đ
                        </td>
                        <td class="text-center">
                            @if ($order->confirm == 'cancle')
                                <span class="cancle-badge">{{ __('cart.confirm')[$order->confirm] }}</span>
                            @elseif ($order->confirm == 'returned')
                                <span class="returned-badge">{{ __('cart.confirm')[$order->confirm] }}</span>
                            @else
                                {!! __('cart.confirm')[$order->confirm] !!}
                            @endif
                        </td>

                        @foreach (__('cart') as $keyItem => $item)
                            @if ($keyItem === 'confirm')
                                @continue
                            @endif
                            <td class="text-center">
                                @if ($order->confirm != 'cancle' && $order->confirm != 'returned')
                                    <select name="{{ $keyItem }}" class="setupSelect2 updateBadge"
                                        data-field="{{ $keyItem }}">
                                        @foreach ($item as $keyOption => $option)
                                            @if ($keyOption === 'none')
                                                @continue
                                            @endif
                                            <option {{ $keyOption == $order->{$keyItem} ? 'selected' : '' }}
                                                value="{{ $keyOption }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    -
                                @endif
                                <input type="hidden" class="changeOrderStatus" value="{{ $order->{$keyItem} }}">
                            </td>
                        @endforeach
                        <td class="text-center">
                            @if ($order->confirm != 'cancle')
                                <img title="{{ array_column(__('payment.method'), 'title', 'name')[$order->method] ?? '-' }}"
                                    style="max-width:54px;"
                                    src="{{ array_column(__('payment.method'), 'image', 'name')[$order->method] ?? '-' }}"
                                    alt="">
                            @else
                                -
                            @endif
                            <input type="hidden" class="confirm" value="{{ $order->confirm }}">
                        </td>
                        <td class="tesxt-center">
                            <a href="{{ route('order.detail', $order->id) }}" class="btn btn-info btn-outline"><i
                                    class="fa fa-edit"></i></a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>

    </table>
</div>
<div class="pagination-wrapper">
    {{ $orders->links('pagination::bootstrap-4') }}
</div>
