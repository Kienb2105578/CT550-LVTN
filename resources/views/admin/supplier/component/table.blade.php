<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên nhà cung cấp</th>
                <th>SĐT liên hệ</th>
                <th>Địa chỉ</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        @php
            $index = 0;
        @endphp
        <tbody>
            @if (isset($suppliers) && is_object($suppliers))
                @foreach ($suppliers as $supplier)
                    @php
                        $index += 1;
                    @endphp
                    <tr>
                        <td>{{ $index }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->phone }}</td>
                        <td>
                            {{ $supplier->address }},
                            {{ $supplier->ward_name }},
                            {{ $supplier->district_name }},
                            {{ $supplier->province_name }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-info btn-outline">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteModal-{{ $supplier->id }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Xóa Nhà Cung Cấp -->
                    <div class="modal fade" id="deleteModal-{{ $supplier->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteModalLabel-{{ $supplier->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deleteModalLabel-{{ $supplier->id }}">Xác nhận xóa
                                            nhà cung cấp</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa nhà cung cấp
                                            <strong>{{ $supplier->name }}</strong> không?
                                        </p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label>Họ Tên</label>
                                            <input type="text" class="form-control" value="{{ $supplier->name }}"
                                                readonly>
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

            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $suppliers->links('pagination::bootstrap-4') }}
</div>
