@php
    $query = base64_encode(http_build_query(request()->query()));
    $queryUrl = rtrim($query, '=');
@endphp

<div class="table-responsive">
    <table class="table  card-table">
        <thead class="thead-light">
            <tr>
                <th style="width:50px;">
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>Tên thuộc tính</th>
                <th class="text-center" style="width:12%">{{ __('messages.tableAction') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($attributes) && is_object($attributes))
                @foreach ($attributes as $attribute)
                    <tr id="{{ $attribute->id }}">
                        <td>
                            <input type="checkbox" value="{{ $attribute->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="main-info">
                                    <div class="name">
                                        <span class="maintitle">{{ $attribute->name }}</span>
                                    </div>
                                    <div class="catalogue">
                                        <span class="text-danger">{{ __('messages.tableGroup') }} </span>
                                        @foreach ($attribute->array_attribute_catalogue_name as $val)
                                            <a href="{{ route('attribute.index', ['attribute_catalogue_id' => $val['id']]) }}"
                                                class="catalogue-link">{{ $val['name'] }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('attribute.edit', [$attribute->id, $queryUrl ?? '']) }}"
                                class="btn btn-info btn-outline">
                                <i class="fa fa-edit"></i>
                            </a>
                            <!-- Trigger Modal for Deletion -->
                            {{-- <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteAttributeModal-{{ $attribute->id }}">
                                <i class="fa fa-trash"></i>
                            </button> --}}

                        </td>
                    </tr>

                    <!-- Modal for Attribute Deletion -->
                    <div class="modal fade" id="deleteAttributeModal-{{ $attribute->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteAttributeModalLabel-{{ $attribute->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('attribute.destroy', $attribute->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deleteAttributeModalLabel-{{ $attribute->id }}">
                                            Xác nhận xóa thuộc tính
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa thuộc tính
                                            <strong>{{ $attribute->name }}</strong>?
                                        </p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label for="" class="control-label text-left">Tên thuộc tính</label>
                                            <input type="text" class="form-control" value="{{ $attribute->name }}"
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
    {{ $attributes->links('pagination::bootstrap-4') }}
</div>
