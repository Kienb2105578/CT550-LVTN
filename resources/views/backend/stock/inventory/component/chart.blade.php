<div class="wrapper wrapper-content animated fadeInRight mt20">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>XEM CHI TIẾT SẢN PHẨM THEO THỜI GIAN</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-5 m-b-xs form-row">
                            <label for="" class="control-label text-left">Chọn sản phẩm <span
                                    class="text-danger"> (*)</span></label>
                            <select name="product_id" id="product_id"
                                class="form-control input-sm input-s-sm inline setupSelect2">
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->product_id }}">{{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-7 m-b-xs">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-row mb15">
                                        <label for="" class="control-label text-left">Ngày bắt đầu <span
                                                class="text-danger"> (*)</span></label>
                                        <div class="form-date">
                                            <input type="text" name="startDate"
                                                value="{{ request('startDate') ?: old('startDate') }}"
                                                class="form-control datepickerReport" placeholder="" autocomplete="off">
                                            <span><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-row mb15">
                                        <label for="" class="control-label text-left">Ngày kết thúc <span
                                                class="text-danger"> (*)</span></label>
                                        <div class="form-date">
                                            <input type="text" name="endDate"
                                                value="{{ request('endDate') ?: old('endDate') }}"
                                                class="form-control datepickerReport" placeholder="" autocomplete="off">
                                            <span><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h4><strong>Sản phẩm:</strong> <span id="productName"></span></h4>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Biến thể</th>
                                    <th>Tổng SL nhập</th>
                                    <th>Tổng SL xuất</th>
                                    <th>Tổng SL trả hàng</th>
                                    <th>SL tồn kho</th>
                                    <th>Chênh lệch</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTable">
                                <tr>
                                    <td colspan="5" class="text-center">Chưa có dữ liệu</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal xem lịch sử nhập/xuất -->
                    <div id="movementModal" class="modal fade" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Chi tiết nhập/xuất kho</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Thời gian</th>
                                                <th>Loại</th>
                                                <th>Số lượng</th>
                                            </tr>
                                        </thead>
                                        <tbody id="movementDetails"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#product_id, input[name="startDate"], input[name="endDate"]').change(function() {
            loadInventoryData();
        });

        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        function loadInventoryData() {
            let productId = $('#product_id').val();
            let startDate = $('input[name="startDate"]').val();
            let endDate = $('input[name="endDate"]').val();

            if (!productId || !startDate || !endDate) {
                return;
            }

            $.ajax({
                url: 'ajax/stock/getInventoryWithTime',
                method: 'GET',
                data: {
                    product_id: productId,
                    startDate: startDate,
                    endDate: endDate
                },
                success: function(response) {
                    if (!response) {
                        $('#productName').text('Không tìm thấy sản phẩm');
                        $('#inventoryTable').html(
                            '<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>'
                        );
                        return;
                    }

                    $('#productName').text(response.product_name);
                    let tableContent = '';

                    response.variants.forEach(function(variant) {
                        tableContent += `
                    <tr>
                        <td>${variant.variant_name}</td>
                        <td>${variant.total_import}</td>
                        <td>${variant.total_export}</td>
                        <td>${variant.total_return}</td>
                        <td>${variant.total_current_quantity}</td>
                        <td>${variant.missing_stock}</td>
                        <td>
                            <button class="btn btn-info btn-sm show-movements" 
                                data-movements='${JSON.stringify(variant.movements)}'>
                                Xem
                            </button>
                        </td>
                    </tr>`;
                    });

                    $('#inventoryTable').html(tableContent);
                }
            });
        }

        // Xử lý hiển thị lịch sử nhập/xuất trong modal
        $(document).on('click', '.show-movements', function() {
            let movements = JSON.parse($(this).attr('data-movements'));
            let detailContent = '';

            if (movements.length === 0) {
                detailContent = '<tr><td colspan="3" class="text-center">Không có dữ liệu</td></tr>';
            } else {
                movements.forEach(function(move) {
                    detailContent += `
                <tr>
                    <td>${move.created_at}</td>
                    <td>${move.type === 'import' ? 'Nhập' : move.type === 'export' ? 'Xuất' : 'Trả hàng'}</td>
                    <td>${move.quantity}</td>
                </tr>`;
                });
            }

            $('#movementDetails').html(detailContent);
            $('#movementModal').modal('show');
        });
    });
</script>
