@php
    $query = base64_encode(http_build_query(request()->query()));
    $queryUrl = rtrim($query, '=');
@endphp
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th style="width:50px;">STT</th>
                <th>{{ __('messages.tableName') }}</th>
                <th class="text-center" style="width:200px;">{{ __('messages.tableAction') }} </th>
            </tr>
        </thead>
        @php
            $index = 0;
        @endphp
        <tbody>
            @if (isset($attributeCatalogues) && is_object($attributeCatalogues))
                @foreach ($attributeCatalogues as $attributeCatalogue)
                    <tr>
                        @php
                            $index += 1;
                        @endphp
                        <td>{{ $index }}</td>
                        <td>
                            {{ str_repeat('|----', $attributeCatalogue->level > 0 ? $attributeCatalogue->level - 1 : 0) . $attributeCatalogue->name }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('attribute.catalogue.edit', [$attributeCatalogue->id, $queryUrl ?? '']) }}"
                                class="btn btn-info btn-outline"><i class="fa fa-edit"></i></a>
                            <!-- Trigger Modal for Deletion -->
                            {{-- <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteAttributeCatalogueModal-{{ $attributeCatalogue->id }}">
                                <i class="fa fa-trash"></i>
                            </button> --}}
                        </td>
                    </tr>

                    <!-- Modal for Attribute Catalogue Deletion -->
                    <div class="modal fade" id="deleteAttributeCatalogueModal-{{ $attributeCatalogue->id }}"
                        tabindex="-1" role="dialog"
                        aria-labelledby="deleteAttributeCatalogueModalLabel-{{ $attributeCatalogue->id }}"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('attribute.catalogue.destroy', $attributeCatalogue->id) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title"
                                            id="deleteAttributeCatalogueModalLabel-{{ $attributeCatalogue->id }}">
                                            Xác nhận xóa loại thuộc tính
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa loại thuộc tính
                                            <strong>{{ $attributeCatalogue->name }}</strong>?
                                        </p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label for="" class="control-label text-left">Tên loại thuộc
                                                tính</label>
                                            <input type="text" class="form-control"
                                                value="{{ $attributeCatalogue->name }}" readonly>
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
{{-- <div class="pagination-wrapper">
    {{ $attributeCatalogues->links('pagination::bootstrap-4') }}
</div> --}}
