<!-- Modal Update -->
<div class="modal fade" id="updateModal-{{ $stock->id }}" tabindex="-1" role="dialog"
    aria-labelledby="updateModalLabel-{{ $stock->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('stock.stock-taking.update', $stock->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="ibox-title">
                    <h5 class="modal-title">Chỉnh sửa phiếu kiểm kê</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body mb15" style="max-height: 70vh; overflow-y: auto;">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="code">Mã Lô Hàng</label>
                                <input type="text" name="code" value="{{ $stock->code }}" class="form-control"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="publish">Trạng thái</label>
                                <select name="publish" class="form-control">
                                    <option value="0" {{ $stock->publish == 0 ? 'selected' : '' }}>Bản nháp
                                    </option>
                                    <option value="1" {{ $stock->publish == 1 ? 'selected' : '' }}>Cập nhật vào kho
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea name="description" class="form-control">{{ $stock->description }}</textarea>
                    </div>

                    <div class="form-group mb15">
                        @php
                            $products = json_decode($stock->products, true); // Giả sử $stock->products là JSON chứa thông tin sản phẩm
                        @endphp
                        @foreach ($products as $key => $product)
                            <div class="mb-3 p-3 border rounded">
                                <!-- Row 1: Tên sản phẩm và ID -->
                                <div class="form-row">
                                    <div class="col-md-12 mb15">
                                        <strong style=" font-size: 16px; color: rgb(0, 102, 255);">Sản
                                            phẩm: {{ $product['product_id'] }} -
                                            {{ $product['variant_id'] ?? 'Không có biến thể' }}</strong>
                                    </div>
                                </div>

                                <!-- Row 2: Số lượng CSDL và Chênh lệch (Không thể chỉnh sửa) -->
                                <div class="form-row mt-2">
                                    <div class="col-md-2">
                                        <label>Số lượng CSDL:</label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control"
                                            value="{{ $product['initial_quantity'] }}" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Chênh lệch:</label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control"
                                            value="{{ $product['actual_quantity'] - $product['initial_quantity'] }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Số lượng thực tế:</label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control"
                                            name="products[{{ $key }}][actual_quantity]"
                                            value="{{ $product['actual_quantity'] }}" required
                                            data-product="{{ json_encode($product) }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="products" id="products-json-{{ $stock->id }}">
                </div> <!-- Kết thúc modal-body -->

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Cập nhật</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        const modalId = '#updateModal-{{ $stock->id }}';
        const form = $(modalId + ' form');

        // Xử lý cập nhật chênh lệch khi thay đổi số lượng thực tế
        $(modalId).on('input', 'input[name^="products"][name$="[actual_quantity]"]', function() {
            const $actualInput = $(this);
            const actualQty = parseFloat($actualInput.val()) || 0;
            const productData = $actualInput.data('product');
            const initialQty = parseFloat(productData.initial_quantity);

            // Tìm ô input chênh lệch trong cùng khối .form-row
            const $differenceInput = $actualInput.closest('.form-row').find('input[readonly]').eq(1);
            const difference = actualQty - initialQty;

            $differenceInput.val(difference);
        });

        // Xử lý trước khi submit
        form.on('submit', function(e) {
            let products = [];

            $(modalId + ' input[name^="products"][name$="[actual_quantity]"]').each(function() {
                const actualQty = parseFloat($(this).val()) || 0;
                const productData = $(this).data('product');

                products.push({
                    batch_id: productData.batch_id,
                    product_id: productData.product_id,
                    variant_id: productData.variant_id ?? null,
                    actual_quantity: actualQty,
                    initial_quantity: productData.initial_quantity,
                    difference: actualQty - productData.initial_quantity,
                    quantity: actualQty
                });
            });

            $(modalId + ' #products-json-{{ $stock->id }}').val(JSON.stringify(products));
        });
    });
</script>
