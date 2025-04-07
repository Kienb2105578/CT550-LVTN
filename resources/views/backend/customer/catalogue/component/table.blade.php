<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>Tên Nhóm khách hàng</th>
                <th class="text-center">Số khách hàng</th>
                <th>Mô tả</th>
                <th class="text-center">Tình Trạng</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($customerCatalogues) && is_object($customerCatalogues))
                @foreach ($customerCatalogues as $customerCatalogue)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $customerCatalogue->id }}"
                                class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            {{ $customerCatalogue->name }}
                        </td>
                        <td class="text-center">
                            {{ $customerCatalogue->customers_count }} người
                        </td>
                        <td>
                            {{ $customerCatalogue->description }}
                        </td>
                        <td class="text-center js-switch-{{ $customerCatalogue->id }}">
                            <input type="checkbox" value="{{ $customerCatalogue->publish }}" class="js-switch status "
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $customerCatalogue->publish == 2 ? 'checked' : '' }}
                                data-modelId="{{ $customerCatalogue->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('customer.catalogue.edit', $customerCatalogue->id) }}"
                                class="btn btn-info btn-outline"><i class="fa fa-edit"></i></a>
                            {{-- <a href="{{ route('customer.catalogue.delete', $customerCatalogue->id) }}"
                                class="btn btn-danger btn-outline"><i class="fa fa-trash"></i></a> --}}
                            <a href="javascript:void(0)" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteModal-{{ $customerCatalogue->id }}">
                                <i class="fa fa-trash"></i>
                            </a>

                        </td>
                    </tr>

                    <!-- Modal Xóa Nhóm Thành Viên -->
                    <div class="modal fade" id="deleteModal-{{ $customerCatalogue->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteModalLabel-{{ $customerCatalogue->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('customer.catalogue.destroy', $customerCatalogue->id) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <!-- Thay modal-header bằng ibox-title -->
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deleteModalLabel-{{ $customerCatalogue->id }}">Xác
                                            nhận xóa nhóm thành viên</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa nhóm
                                            <strong>{{ $customerCatalogue->name }}</strong> không?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Không thể hoàn tác thao tác này.</p>
                                        <div class="form-group">
                                            <label>Tên nhóm</label>
                                            <input type="text" class="form-control"
                                                value="{{ $customerCatalogue->name }}" readonly>
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
    {{ $customerCatalogues->links('pagination::bootstrap-4') }}
</div>
