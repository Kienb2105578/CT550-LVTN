@php
    $query = base64_encode(http_build_query(request()->query()));
    $queryUrl = rtrim($query, '=');
@endphp
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th style="width:50px;">
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>{{ __('messages.tableName') }}</th>
                <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }} </th>
                <th class="text-center" style="width:100px;">{{ __('messages.tableAction') }} </th>
            </tr>
        </thead>
        <tbody>
            @if (isset($productCatalogues) && is_object($productCatalogues))
                @foreach ($productCatalogues as $productCatalogue)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $productCatalogue->id }}"
                                class="input-checkbox checkBoxItem">
                        </td>

                        <td>
                            {{ str_repeat('|----', $productCatalogue->level > 0 ? $productCatalogue->level - 1 : 0) . $productCatalogue->name }}
                        </td>
                        <td class="text-center">
                            <input type="checkbox" value="{{ $productCatalogue->publish }}"
                                class="js-switch status js-switch-{{ $productCatalogue->id }}" data-field="publish"
                                data-model="{{ $config['model'] }}"
                                {{ $productCatalogue->publish == 2 ? 'checked' : '' }}
                                data-modelId="{{ $productCatalogue->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('product.catalogue.edit', [$productCatalogue->id, $queryUrl]) }}"
                                class="btn btn-info btn-outline"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteProductCatalogueModal-{{ $productCatalogue->id }}"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Xóa Danh Mục Sản Phẩm -->
                    <div class="modal fade" id="deleteProductCatalogueModal-{{ $productCatalogue->id }}" tabindex="-1"
                        role="dialog" aria-labelledby="deleteProductCatalogueModalLabel-{{ $productCatalogue->id }}"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('product.catalogue.destroy', $productCatalogue->id) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title"
                                            id="deleteProductCatalogueModalLabel-{{ $productCatalogue->id }}">Xác nhận
                                            xóa danh mục sản phẩm</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa danh mục sản phẩm
                                            <strong>{{ $productCatalogue->name }}</strong> không?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label>Tên danh mục sản phẩm</label>
                                            <input type="text" class="form-control"
                                                value="{{ $productCatalogue->name }}" readonly>
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
    {{ $productCatalogues->links('pagination::bootstrap-4') }}
</div>
