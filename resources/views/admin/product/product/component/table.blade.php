@php
    $query = base64_encode(http_build_query(request()->query()));
    $queryUrl = rtrim($query, '=');
@endphp
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th style="width:50px;" class="text-center">
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th style="width:780px;">{{ __('messages.tableName') }}</th>
                <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }}</th>
                <th class="text-center" style="width:100px;">{{ __('messages.tableAction') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($products) && is_object($products))
                @foreach ($products as $product)
                    <tr id="{{ $product->id }}">
                        <td class="text-center">
                            <input type="checkbox" value="{{ $product->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            <div class="uk-flex uk-flex-middle">
                                <div class="image mr5">
                                    <div class="img-scaledown image-product"><img src="{{ image($product->image) }}"
                                            alt=""></div>
                                </div>
                                <div class="main-info">
                                    <div class="name"><span class="maintitle">{{ $product->name }}</span></div>
                                    <div class="catalogue">
                                        <span class="text-danger">{{ __('messages.tableGroup') }} </span>
                                        @foreach ($product->array_product_catalogue_name as $val)
                                            <a href="{{ route('product.index', ['product_catalogue_id' => $val['id']]) }}"
                                                title="">{{ $val['name'] }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center js-switch-{{ $product->id }}">
                            <input type="checkbox" value="{{ $product->publish }}" class="js-switch status "
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $product->publish == 2 ? 'checked' : '' }} data-modelId="{{ $product->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('product.edit', [$product->id, $queryUrl ?? 'p']) }}"
                                class="btn btn-info btn-outline"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteProductModal-{{ $product->id }}"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Xóa Sản Phẩm -->
                    <div class="modal fade" id="deleteProductModal-{{ $product->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteProductModalLabel-{{ $product->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('product.destroy', $product->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deleteProductModalLabel-{{ $product->id }}">Xác
                                            nhận xóa sản phẩm</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa sản phẩm <strong>{{ $product->name }}</strong>
                                            không?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label>Tên sản phẩm</label>
                                            <input type="text" class="form-control" value="{{ $product->name }}"
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
    {{ $products->links('pagination::bootstrap-4') }}
</div>
