<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>Tên nhà cung cấp</th>
                <th>SĐT liên hệ</th>
                <th>Địa chỉ</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($suppliers) && is_object($suppliers))
                @foreach ($suppliers as $supplier)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $supplier->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            {{ $supplier->name }}
                        </td>
                        <td>
                            {{ $supplier->phone }}
                        </td>
                        <td>
                            {{ $supplier->address }},
                            {{ $supplier->ward_name }},
                            {{ $supplier->district_name }}, {{ $supplier->province_name }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-success"><i
                                    class="fa fa-edit"></i></a>
                            <a href="{{ route('supplier.delete', $supplier->id) }}" class="btn btn-danger"><i
                                    class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $suppliers->links('pagination::bootstrap-4') }}
</div>
