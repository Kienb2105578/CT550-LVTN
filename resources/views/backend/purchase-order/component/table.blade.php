<div class="table-responsive">
    @php
        $query = base64_encode(http_build_query(request()->query()));
        $queryUrl = rtrim($query, '=');
    @endphp
    <table class="table">
        <thead>
            <tr>
                <th class="text-center" style="width:4%;"><input type="checkbox" id="checkAll" class="input-checkbox"></th>
                <th class="text-center" style="width:10%;">Mã đơn</th>
                <th>Nhà cung cấp</th>
                <th class="text-center" style="width:11%">Tổng tiền</th>
                <th class="text-center" style="width:10%">Trạng thái</th>
                <th>Ghi chú</th>
                <th class="text-center" style="width:14%;">{{ __('messages.tableAction') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($purchaseOrders) && $purchaseOrders->count())
                @foreach ($purchaseOrders as $purchaseOrder)
                    <tr id="{{ $purchaseOrder->id }}">
                        <td class="text-center"><input type="checkbox" value="{{ $purchaseOrder->id }}"
                                class="input-checkbox checkBoxItem"></td>
                        <td class="text-center">{{ $purchaseOrder->code }}</td> <!-- Mã đơn -->
                        <td>{{ $purchaseOrder->supplier_name }}</td> <!-- Nhà cung cấp -->
                        <td class="text-center" style="color: red;">
                            {{ number_format($purchaseOrder->total, 0, ',', '.') }} đ</td>
                        <td class="text-center">
                            @if ($purchaseOrder->status == 'pending')
                                <span class="badge badge-warning">Chờ kiểm định</span>
                            @elseif($purchaseOrder->status == 'approved')
                                <span class="badge badge-success">Đã nhập kho</span>
                            @else
                                <span class="badge badge-danger">Đã hoàn trả</span>
                            @endif
                        </td>
                        <td>{{ $purchaseOrder->note ?? 'Không có ghi chú' }}</td>
                        <td class="text-center">
                            <a href="{{ route('purchase-order.edit', $purchaseOrder->id) }}"
                                class="btn btn-info btn-outline">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deletePurchaseOrderModal-{{ $purchaseOrder->id }}"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Xóa Đơn Mua Hàng -->
                    <div class="modal fade" id="deletePurchaseOrderModal-{{ $purchaseOrder->id }}" tabindex="-1"
                        role="dialog" aria-labelledby="deletePurchaseOrderModalLabel-{{ $purchaseOrder->id }}"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('purchase-order.destroy', $purchaseOrder->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title"
                                            id="deletePurchaseOrderModalLabel-{{ $purchaseOrder->id }}">Xác nhận xóa
                                            đơn mua hàng</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa đơn mua hàng
                                            <strong>{{ $purchaseOrder->code }}</strong> không?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label>Mã phiếu nhập</label>
                                            <input type="text" class="form-control"
                                                value="{{ $purchaseOrder->code }}" readonly>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Hủy</button>
                                        <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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
