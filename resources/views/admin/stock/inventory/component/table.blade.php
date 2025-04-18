<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th class="text-center">STT</th>
                <th class="text-center">Mã Lô Hàng</th>
                <th class="text-center">Tên nhà cung cấp</th>
                <th class="text-center">SĐT liên hệ</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        @php
            $index = 0;
        @endphp
        <tbody>
            @if (isset($inventorys) && $inventorys->count())
                @foreach ($inventorys as $inventory)
                    @php
                        $index += 1;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index }}</td>
                        <td class="text-center">
                            {{ $inventory->purchaseOrder->code }}
                        </td>
                        <td>
                            {{ $inventory->purchaseOrder->supplier->name }}
                        </td>
                        <td>
                            {{ $inventory->purchaseOrder->supplier->phone }}
                        </td>
                        <td class="text-center">
                            <button class="btn btn-info btn-outline btn-edit edit-stock"
                                data-id="{{ $inventory->purchase_order_id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">Không có dữ liệu</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $inventorys->links('pagination::bootstrap-4') }}
</div>
<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">CHI TIẾT LÔ HÀNG</h5>
            </div>
            <div class="modal-body">
                <p id="modalContent">Đang tải dữ liệu...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
