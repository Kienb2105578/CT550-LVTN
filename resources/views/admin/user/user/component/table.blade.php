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
                <th class="text-center">Nhóm nhân viên</th>
                <th class="text-center">Tình Trạng</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($users) && is_object($users))
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $user->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->address }}</td>
                        <td class="text-center">{{ $user->user_catalogues->name }}</td>
                        <td class="text-center js-switch-{{ $user->id }}">
                            <input type="checkbox" value="{{ $user->publish }}" class="js-switch status"
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $user->publish == 2 ? 'checked' : '' }} data-modelId="{{ $user->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('user.edit', $user->id) }}" class="btn btn-info btn-outline"><i
                                    class="fa fa-edit"></i></a>
                            <!-- Nút Xóa mở Modal -->
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteUserModal-{{ $user->id }}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Xóa User -->
                    <div class="modal fade" id="deleteUserModal-{{ $user->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteUserModalLabel-{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('user.destroy', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deleteUserModalLabel-{{ $user->id }}">Xác nhận
                                            xóa người dùng</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa người dùng có email là
                                            <strong>{{ $user->email }}</strong> không?
                                        </p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label for="" class="control-label text-left">Email</label>
                                            <input type="text" class="form-control" value="{{ $user->email }}"
                                                readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="control-label text-left">Họ Tên</label>
                                            <input type="text" class="form-control" value="{{ $user->name }}"
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
    {{ $users->links('pagination::bootstrap-4') }}
</div>
