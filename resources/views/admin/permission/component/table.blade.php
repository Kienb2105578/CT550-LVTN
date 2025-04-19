<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tiêu đề</th>
                <th>Canonical</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        @php
            $index = 0;
        @endphp
        <tbody>
            @if (isset($permissions) && is_object($permissions))
                @foreach ($permissions as $permission)
                    @php
                        $index += 1;
                    @endphp
                    <tr>
                        <td>{{ $index }} </td>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->canonical }}</td>
                        <td class="text-center">
                            <a href="{{ route('permission.edit', $permission->id) }}" class="btn btn-info btn-outline">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deletePermissionModal-{{ $permission->id }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Xóa Quyền -->
                    <div class="modal fade" id="deletePermissionModal-{{ $permission->id }}" tabindex="-1"
                        role="dialog" aria-labelledby="deletePermissionModalLabel-{{ $permission->id }}"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('permission.destroy', $permission->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deletePermissionModalLabel-{{ $permission->id }}">
                                            Xác nhận xóa quyền
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa quyền <strong>{{ $permission->name }}</strong>
                                            không?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label>Tên quyền</label>
                                            <input type="text" class="form-control" value="{{ $permission->name }}"
                                                readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Canonical</label>
                                            <input type="text" class="form-control"
                                                value="{{ $permission->canonical }}" readonly>
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
    {{ $permissions->links('pagination::bootstrap-4') }}
</div>
