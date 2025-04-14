@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo']['stockTaking']['create']])
@include('admin.dashboard.component.formError')

@php
    $url =
        $config['method'] == 'create'
            ? route('stock.stock-taking.store')
            : route('stock.stock-taking.update', $stockTaking->id);
@endphp

<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row mb15">
            <div class="col-lg-6">
                <div class="ibox-title">
                    <h5>Chọn lô hàng</h5>
                </div>
                <div class="ibox-content">
                    <select name="code" id="code" class="form-control input-sm input-s-sm inline setupSelect2">
                        <option value="">-- Chọn Lô Hàng --</option>
                        @foreach ($codes as $batchId => $batch)
                            <option value="{{ $batchId }}">{{ $batch['code'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="ibox-title">
                    <h5>Lý do kiểm kê kho</h5>
                </div>
                <div class="ibox-content">
                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả..."></textarea>
                </div>
            </div>
        </div>

        <div class="row mb15">
            <div class="col-lg-6">
                <div class="ibox-title">
                    <h5>Chọn sản phẩm</h5>
                </div>
                <div class="ibox-content">
                    <div id="product-selection">
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="ibox-title">
                    <h5>Trạng thái</h5>
                </div>
                <div class="ibox-content">
                    <input type="hidden" name="publish" value="0">

                    <select class="form-control" disabled>
                        <option value="0" selected>Bản nháp</option>
                        <option value="1">Cập nhật vào kho</option>
                    </select>
                </div>
            </div>

        </div>


        <!-- Bảng Hiển Thị -->
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="ibox-title">
                    <h5>Thông tin sản phẩm và biến thể</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-bordered" id="product-table">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Biến thể</th>
                                <th style="width:10%;">SL (CSDL)</th>
                                <th style="width:10%;">SL thực tế</th>
                                <th style="width:10%;">Chênh lệch</th>
                                <th style="width:10%;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Các dòng sẽ được thêm vào sau khi chọn sản phẩm và biến thể -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Nút Submit -->
        <div class="text-right mb15 " style= "margin-bottom: 40px">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        const batchs = @json($codes);

        // Khi chọn Lô hàng
        $('#code').on('change', function() {
            const code = $(this).val();
            const productSelection = $('#product-selection');
            const tableBody = $('#product-table tbody');

            productSelection.html('');
            tableBody.html('');

            if (code && batchs[code]) {
                const products = batchs[code].products;

                $.each(products, function(index, product) {
                    let productHtml = `
                <div class="form-group border p-2 mb-2">
                    <label>
                        <input type="checkbox" class="product-checkbox" data-product-id="${product.product_id}">
                        <strong>${product.product_name}</strong>
                    </label>
            `;

                    // Nếu có biến thể
                    if (product.variant && Array.isArray(product.variant) && product.variant
                        .length > 0) {
                        productHtml += `<div class="variants mt-2" style="display: none;">`;

                        $.each(product.variant, function(i, variant) {
                            productHtml += `
                        <label style="margin-right:10px;">
                            <input type="checkbox" class="variant-checkbox" name="products[${product.product_id}][variant][]" 
                                value="${variant.variant_id}" 
                                data-product-id="${product.product_id}">
                            ${variant.variant_name}
                        </label>
                    `;
                        });

                        productHtml += `</div>`;
                    } else {
                        // Không có biến thể
                        productHtml += `
                    <input type="hidden" name="products[${product.product_id}][variant]" value="none">
                `;
                    }

                    productHtml += `</div>`;
                    productSelection.append(productHtml);
                });
            }
        });

        // Khi tích chọn sản phẩm
        $(document).on('change', '.product-checkbox', function() {
            const productId = $(this).data('product-id');
            const productContainer = $(this).closest('.form-group');
            const variantsDiv = productContainer.find('.variants');
            const tableBody = $('#product-table tbody');
            const code = $('#code').val();
            const product = batchs[code].products.find(p => p.product_id == productId);

            // Reset checkbox khác nếu đã chọn biến thể
            tableBody.find(`tr[data-product-id="${productId}"]`).remove();

            if (this.checked) {
                if (variantsDiv.length > 0) {
                    variantsDiv.show();
                } else {
                    // Nếu không có biến thể
                    const row = `
                <tr data-product-id="${productId}" data-variant-id="none">
                    <td>${product.product_name}</td>
                    <td>Không có</td>
                    <td>${product.quantity || 0}</td>
                    <td><input type="number" class="form-control actual-quantity" data-variant-id="none" value="0" min="0"></td>
                    <td class="difference">0</td>
                    <td class="text-center"><button type="button" class="btn btn-danger remove-row">Xóa</button></td>
                </tr>
                `;
                    tableBody.append(row);
                }
            } else {
                // Khi bỏ chọn sản phẩm, reset lại biến thể và sản phẩm
                variantsDiv.hide(); // Ẩn phần biến thể
                tableBody.find(`tr[data-product-id="${productId}"]`).remove(); // Xóa sản phẩm đã chọn
            }
        });

        // Khi tích chọn biến thể
        $(document).on('change', '.variant-checkbox', function() {
            const productId = $(this).data('product-id');
            const variantId = $(this).val();
            const tableBody = $('#product-table tbody');
            const code = $('#code').val();
            const product = batchs[code].products.find(p => p.product_id == productId);
            const variant = product.variant.find(v => v.variant_id == variantId);

            const checkbox = $(`.product-checkbox[data-product-id="${productId}"]`);
            if (!checkbox.prop('checked')) return;

            // Kiểm tra nếu sản phẩm và biến thể đã tồn tại trong bảng
            if (tableBody.find(`tr[data-product-id="${productId}"][data-variant-id="${variantId}"]`)
                .length > 0) {
                alert('Sản phẩm và biến thể này đã được chọn rồi!');
                return;
            }

            const row = `
    <tr data-product-id="${productId}" data-variant-id="${variantId}">
        <td>${product.product_name}</td>
        <td>${variant.variant_name}</td>
        <td>${variant.quantity}</td>
        <td><input type="number" class="form-control actual-quantity" data-variant-id="${variant.variant_id}" value="0" min="0"></td>
        <td class="difference">0</td>
        <td class="text-center"><button type="button" class="btn btn-danger remove-row">Xóa</button></td>
    </tr>
    `;
            tableBody.append(row);
        });

        // Tính toán chênh lệch
        $(document).on('input', '.actual-quantity', function() {
            const actualQuantity = parseFloat($(this).val()) || 0;
            const row = $(this).closest('tr');
            const stockQuantity = parseFloat(row.find('td:eq(2)').text()) || 0;
            const difference = actualQuantity - stockQuantity;

            row.find('.difference').text(difference);
        });

        // Xóa dòng
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });

        $('form').on('submit', function(e) {
            e.preventDefault(); // Ngăn form submit mặc định

            const code = $('#code').val();
            const batch = batchs[code];
            const batchId = batch.products.length > 0 ? batch.products[0].batch_id : null;

            const products = [];

            $('#product-table tbody tr').each(function() {
                const productId = $(this).data('product-id');
                const variantId = $(this).data('variant-id');
                const actualQuantity = $(this).find('.actual-quantity').val();
                const difference = $(this).find('.difference').text();

                const product = batch.products.find(p => p.product_id == productId);
                const variant = variantId !== "none" ? product.variant.find(v => v.variant_id ==
                    variantId) : null;

                const initialQuantity = variant ? variant.initial_quantity : product
                    .initial_quantity;
                const quantity = variant ? variant.quantity : product.quantity;

                // Đảm bảo batch_id được gán đúng cho từng sản phẩm hoặc biến thể
                products.push({
                    batch_id: variant ? variant.batch_id : product
                        .batch_id, // Lấy batch_id từ variant nếu có
                    product_id: productId,
                    variant_id: variantId,
                    actual_quantity: actualQuantity,
                    difference: difference,
                    initial_quantity: initialQuantity,
                    quantity: quantity
                });
            });

            // Xoá input cũ nếu có
            $('input[name="products"]').remove();

            // Thêm input ẩn chứa JSON
            const input = $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'products')
                .val(JSON.stringify(products));

            $(this).append(input);

            // Submit lại form
            this.submit();
        });


    });
</script>
<style>
    .form-group label {
        display: flex;
        align-items: center;
        gap: 10px;

    }

    .product-checkbox {
        margin-right: 20px;
    }

    .variants {
        margin-left: 40px;
    }

    .variant-checkbox {
        margin-right: 10px;
    }
</style>
