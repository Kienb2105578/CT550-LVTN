<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>Họ Tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th class="text-center">Tình Trạng</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($customers) && is_object($customers))
                @foreach ($customers as $customer)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $customer->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            {{ $customer->name }}
                        </td>
                        <td>
                            {{ $customer->email }}
                        </td>
                        <td>
                            {{ $customer->phone }}
                        </td>
                        <td>
                            {{ $customer->address }},{{ $customer->ward_id }}
                        </td>
                        <td class="text-center js-switch-{{ $customer->id }}">
                            <input type="checkbox" value="{{ $customer->publish }}" class="js-switch status "
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $customer->publish == 2 ? 'checked' : '' }} data-modelId="{{ $customer->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-info btn-outline"><i
                                    class="fa fa-edit"></i></a>
                            {{-- <a href="{{ route('customer.delete', $customer->id) }}"
                                class="btn btn-danger btn-outline"><i class="fa fa-trash"></i></a> --}}
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteModal-{{ $customer->id }}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Xóa Khách Hàng -->
                    <div class="modal fade" id="deleteModal-{{ $customer->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteModalLabel-{{ $customer->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('customer.destroy', $customer->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deleteModalLabel-{{ $customer->id }}">Xác nhận xóa
                                            khách hàng</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa khách hàng <strong>{{ $customer->name }}</strong>
                                            không?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="text" class="form-control" value="{{ $customer->email }}"
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
    {{ $customers->links('pagination::bootstrap-4') }}
</div>
