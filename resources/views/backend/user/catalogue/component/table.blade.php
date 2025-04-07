<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>Tên Nhóm nhân Viên</th>
                <th class="text-center">Số nhân viên</th>
                <th>Mô tả</th>
                <th class="text-center">Tình Trạng</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($userCatalogues) && is_object($userCatalogues))
                @foreach ($userCatalogues as $userCatalogue)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $userCatalogue->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>{{ $userCatalogue->name }}</td>
                        <td class="text-center">{{ $userCatalogue->users_count }} người</td>
                        <td>{{ $userCatalogue->description }}</td>
                        <td class="text-center">
                            <input type="checkbox" value="{{ $userCatalogue->publish }}"
                                class="js-switch status js-switch-{{ $userCatalogue->id }}" data-field="publish"
                                data-model="{{ $config['model'] }}" {{ $userCatalogue->publish == 2 ? 'checked' : '' }}
                                data-modelId="{{ $userCatalogue->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('user.catalogue.edit', $userCatalogue->id) }}"
                                class="btn btn-info btn-outline"><i class="fa fa-edit"></i></a>
                            <!-- Nút Xóa mở Modal -->
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteUserCatalogueModal-{{ $userCatalogue->id }}"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Xóa UserCatalogue -->
                    <div class="modal fade" id="deleteUserCatalogueModal-{{ $userCatalogue->id }}" tabindex="-1"
                        role="dialog" aria-labelledby="deleteUserCatalogueModalLabel-{{ $userCatalogue->id }}"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('user.catalogue.destroy', $userCatalogue->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title"
                                            id="deleteUserCatalogueModalLabel-{{ $userCatalogue->id }}">Xác nhận xóa
                                            nhóm thành viên</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa nhóm thành viên có tên là
                                            <strong>{{ $userCatalogue->name }}</strong> không?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label for="" class="control-label text-left">Tên nhóm</label>
                                            <input type="text" class="form-control"
                                                value="{{ $userCatalogue->name }}" readonly>
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
    {{ $userCatalogues->links('pagination::bootstrap-4') }}
</div>
