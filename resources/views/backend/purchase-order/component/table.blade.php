<div class="table-responsive">
    @php
        $query = base64_encode(http_build_query(request()->query()));
        $queryUrl = rtrim($query, '=');
    @endphp
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th style="width:50px;"><input type="checkbox" id="checkAll" class="input-checkbox"></th>
                <th style="width:100px;">Mã đơn</th>
                <th style="width:300px;">Nhà cung cấp</th>
                <th style="width:150px;">Tổng tiền</th>
                <th style="width:100px;">Trạng thái</th>
                <th style="width:200px;">Ghi chú</th>
                <th class="text-center" style="width:100px;">{{ __('messages.tableAction') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($purchaseOrders) && $purchaseOrders->count())
                @foreach ($purchaseOrders as $purchaseOrder)
                    <tr id="{{ $purchaseOrder->id }}">
                        <td><input type="checkbox" value="{{ $purchaseOrder->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>{{ $purchaseOrder->code }}</td> <!-- Mã đơn -->
                        <td>{{ $purchaseOrder->supplier_name }}</td> <!-- Nhà cung cấp -->
                        <td style="color: red;">{{ number_format($purchaseOrder->total, 0, ',', '.') }} đ</td>
                        <td>
                            @if ($purchaseOrder->status == 'pending')
                                <span class="badge badge-warning">Chờ kiểm định</span>
                            @elseif($purchaseOrder->status == 'approved')
                                <span class="badge badge-success">Đã nhập kho</span>
                            @else
                                <span class="badge badge-danger">Đã hoàn trả</span>
                            @endif
                        </td>
                        <td>{{ $purchaseOrder->note ?? 'Không có ghi chú' }}</td> <!-- Ghi chú -->
                        <td class="text-center">
                            <a href="{{ route('purchase-order.edit', $purchaseOrder->id) }}" class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a href="{{ route('purchase-order.delete', $purchaseOrder->id) }}" class="btn btn-danger">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td> <!-- Hành động -->
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">Không có đơn hàng nào.</td>
                </tr>
            @endif
        </tbody>

    </table>
</div>
<div class="pagination-wrapper">
    {{ $purchaseOrders->links('pagination::bootstrap-4') }}
</div>
