<div class="wrapper wrapper-content animated fadeInRight mt20">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title d-flex justify-content-between align-items-center">
                    <h5>Báo cáo doanh thu</h5>
                    <a href="{{ route('stock.report.exportFile', [
                        'catalogue_id' => request('catalogue_id'),
                        'startDate' => request('startDate'),
                        'endDate' => request('endDate'),
                    ]) }}"
                        target="_blank" class="btn btn-primary btn-sm" id="exportFileBtn">
                        <i class="fa fa-file-export"></i> Xuất file
                    </a>

                </div>

                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-5 m-b-xs form-row">
                            <label for="" class="control-label text-left">Chọn danh mục sản phẩm <span
                                    class="text-danger"> (*)</span></label>
                            <select name="catalogue_id" id="catalogue_id"
                                class="form-control input-sm input-s-sm inline setupSelect2">
                                <option value="">-- Chọn danh mục sản phẩm --</option>
                                @foreach ($catalogues as $catalogue)
                                    <option value="{{ $catalogue->catalogue_id }}">{{ $catalogue->catalogue_name }}
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
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Vốn tồn kho</th>
                                    <th>SL tồn kho</th>
                                    <th>Giá nhập</th>
                                    <th>Giá bán</th>
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
                    <div id="batchModal" class="modal fade" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Chi tiết lô hàng</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mã lô hàng</th>
                                                <th>SL còn lại</th>
                                                <th>SL ban đầu</th>
                                                <th>Giá nhập</th>
                                                <th>Tình trạng</th>
                                            </tr>
                                        </thead>
                                        <tbody id="batchDetails"></tbody>
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
        // Trigger when the product category or dates change
        $('#catalogue_id, input[name="startDate"], input[name="endDate"]').change(function() {
            loadInventoryData();
        });

        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        function loadInventoryData() {
            let catalogueId = $('#catalogue_id').val();
            let startDate = $('input[name="startDate"]').val();
            let endDate = $('input[name="endDate"]').val();

            if (!catalogueId || !startDate || !endDate) {
                return;
            }

            $.ajax({
                url: 'ajax/stock/getReport',
                method: 'GET',
                data: {
                    catalogue_id: catalogueId,
                    startDate: startDate,
                    endDate: endDate
                },
                success: function(response) {
                    let tableContent = '';

                    // Loop through each product in the response
                    response.products.forEach(function(product, index) {
                        tableContent += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${product.product_name}</td>
                        <td>${formatVND(product.total_capital)}</td>
                        <td>${product.stock_quantity}</td>
                        <td>${formatVND(product.purchase_price)}</td>
                        <td>${formatVND(product.sale_price)}</td>
                        <td>
                            <button class="btn btn-info btn-sm show-batch-details" 
                                data-batch='${JSON.stringify(product.details)}'>
                                Xem
                            </button>
                        </td>
                    </tr>`;
                    });

                    $('#inventoryTable').html(tableContent);
                }
            });
        }

        // Show the batch details in the modal
        $(document).on('click', '.show-batch-details', function() {
            let batch = JSON.parse($(this).attr('data-batch'));
            let detailContent = '';

            batch.forEach(function(batchDetail) {
                detailContent += `
                <tr>
                    <td>${batchDetail.purchase_order_code}</td>
                    <td>${batchDetail.quantity}</td>
                    <td>${batchDetail.initial_quantity}</td>
                    <td>${formatVND(batchDetail.price)}</td>
                    <td>${batchDetail.publish ? 'Đã xuất bản' : 'Chưa xuất bản'}</td>
                </tr>`;
            });

            $('#batchDetails').html(detailContent);
            $('#batchModal').modal('show');
        });

        function updateExportUrl() {
            let catalogueId = $('#catalogue_id').val();
            let startDate = $('input[name="startDate"]').val();
            let endDate = $('input[name="endDate"]').val();

            if (catalogueId && startDate && endDate) {
                let exportUrl =
                    `{{ route('stock.report.exportFile') }}?catalogue_id=${catalogueId}&startDate=${startDate}&endDate=${endDate}`;
                console.log("Cập nhật URL xuất file: ", exportUrl); // Debug
                $('#exportFileBtn').attr('href', exportUrl);
            }
        }

        // Bắt sự kiện thay đổi
        $('#catalogue_id, input[name="startDate"], input[name="endDate"]').change(updateExportUrl);

    });
</script>

<style>
    .d-flex {
        display: flex;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .align-items-center {
        align-items: center;
    }
</style>
