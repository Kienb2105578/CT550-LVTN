<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="text-center">
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th class="text-center">Tên Sản Phẩm</th>
                <th class="text-center">Tổng Số Lượng Nhập</th>
                <th class="text-center">Tổng Số Lượng Còn Lại</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($products) && $products->count())
                @foreach ($products as $product)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" value="{{ $product->product_id }}"
                                class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            {{ $product->full_product_name }}
                        </td>
                        <td class="text-center">
                            {{ $product->total_initial_quantity }}
                        </td>
                        <td class="text-center">
                            {{ $product->total_remaining_quantity }}
                        </td>
                        <td class="text-center">
                            <button class="btn btn-success btn-edit edit-product"
                                data-variant="{{ $product->variant_id }}" data-id="{{ $product->product_id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center">Không có dữ liệu</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>


<!-- Modal -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailModalLabel">Chi Tiết Sản Phẩm</h5>
            </div>
            <div class="modal-body">
                <p id="modalContent">Đang tải dữ liệu...</p>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
