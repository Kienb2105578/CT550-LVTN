<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th class="text-center" style="width:3%;">
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th class="text-center" style="width:10%;">Mã Lô Hàng</th>
                <th class="text-center">Tên sản phẩm</th>
                <th class="text-center" style="width:10%;">Nhân viên</th>
                <th class="text-center" style="width:10%;">Vai trò</th>
                <th class="text-center" style="width:10%;">Loại giao dịch</th>
                <th class="text-center" style="width:7%;">Số lượng</th>
                {{-- <th class="text-center" style="width:10%;">Thao tác</th> --}}
            </tr>
        </thead>
        <tbody>
            @if ($stocks->count())
                @foreach ($stocks as $stock)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" value="{{ $stock->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td class="text-center">{{ $stock->purchaseOrder->code ?? '' }}</td>
                        <td class="text-truncate-2">
                            {{ $stock->product_name }}
                            @if ($stock->variant)
                                - {{ $stock->variant->name }}
                            @endif
                        </td>

                        <td class="text-center text-truncate-1">{{ $stock->user->name ?? '-' }}</td>
                        <td class="text-center">{{ $stock->user->user_catalogues->name ?? '-' }}</td>
                        <td class="text-center">
                            @if ($stock->type == 'import')
                                <span class="badge bg-success">Nhập hàng</span>
                            @elseif ($stock->type == 'export')
                                <span class="badge bg-danger">Xuất hàng</span>
                            @elseif ($stock->type == 'return')
                                <span class="badge bg-warning">Trả hàng</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $stock->quantity }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $stocks->links('pagination::bootstrap-4') }}
</div>


<style>
    .text-truncate-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
