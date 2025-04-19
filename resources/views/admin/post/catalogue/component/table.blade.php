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
            @if (isset($postCatalogues) && is_object($postCatalogues))
                @foreach ($postCatalogues as $postCatalogue)
                    <tr id="{{ $postCatalogue->id }}">
                        <td>
                            <input type="checkbox" value="{{ $postCatalogue->id }}" class="input-checkbox checkBoxItem">
                        </td>

                        <td>
                            {{ str_repeat('|----', $postCatalogue->level > 0 ? $postCatalogue->level - 1 : 0) . $postCatalogue->name }}
                        </td>
                        <td class="text-center js-switch-{{ $postCatalogue->id }}">
                            <input type="checkbox" value="{{ $postCatalogue->publish }}" class="js-switch status"
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $postCatalogue->publish == 2 ? 'checked' : '' }}
                                data-modelId="{{ $postCatalogue->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('post.catalogue.edit', $postCatalogue->id) }}"
                                class="btn btn-info btn-outline"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deletePostCatalogueModal-{{ $postCatalogue->id }}"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Xóa Danh Mục Bài Viết -->
                    <div class="modal fade" id="deletePostCatalogueModal-{{ $postCatalogue->id }}" tabindex="-1"
                        role="dialog" aria-labelledby="deletePostCatalogueModalLabel-{{ $postCatalogue->id }}"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('post.catalogue.destroy', $postCatalogue->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title"
                                            id="deletePostCatalogueModalLabel-{{ $postCatalogue->id }}">
                                            Xác nhận xóa danh mục bài viết
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa danh mục bài viết
                                            <strong>{{ $postCatalogue->name }}</strong>
                                            không?
                                        </p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label>Tên danh mục</label>
                                            <input type="text" class="form-control"
                                                value="{{ $postCatalogue->name }}" readonly>
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
    {{ $postCatalogues->links('pagination::bootstrap-4') }}
</div>
