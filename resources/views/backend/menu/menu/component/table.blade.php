<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>Tên Menu</th>
                <th>Từ khóa</th>
                <th class="text-center">Tình Trạng</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($menuCatalogues) && is_object($menuCatalogues))
                @foreach ($menuCatalogues as $menuCatalogue)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $menuCatalogue->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>{{ $menuCatalogue->name }}</td>
                        <td>{{ $menuCatalogue->keyword }}</td>
                        <td class="text-center js-switch-{{ $menuCatalogue->id }}">
                            <input type="checkbox" value="{{ $menuCatalogue->publish }}" class="js-switch status"
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $menuCatalogue->publish == 2 ? 'checked' : '' }}
                                data-modelId="{{ $menuCatalogue->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('menu.edit', $menuCatalogue->id) }}" class="btn btn-info btn-outline"><i
                                    class="fa fa-edit"></i></a>

                            @if ($menuCatalogue->keyword != 'main-menu' && $menuCatalogue->keyword != 'footer-menu')
                                <button type="button" class="btn btn-outline btn-danger" data-toggle="modal"
                                    data-target="#deleteModal-{{ $menuCatalogue->id }}"><i
                                        class="fa fa-trash"></i></button>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal Xóa Menu -->
                    <div class="modal fade" id="deleteModal-{{ $menuCatalogue->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteModalLabel-{{ $menuCatalogue->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('menu.destroy', $menuCatalogue->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deleteModalLabel-{{ $menuCatalogue->id }}">Xác nhận
                                            xóa vị trí menu</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa vị trí menu
                                            <strong>{{ $menuCatalogue->name }}</strong> không?
                                        </p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label>Tên vị trí menu</label>
                                            <input type="text" class="form-control"
                                                value="{{ $menuCatalogue->name }}" readonly>
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
