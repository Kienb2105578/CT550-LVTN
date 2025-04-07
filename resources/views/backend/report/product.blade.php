<form action="{{ route('report.product') }}" method="get">
    <div class="wrapper wrapper-content report-product">
        @include('backend.dashboard.component.statistic')
        <div class="row mb15">
            <div class="col-lg-6">
                <div class="panel-head">
                    <div class="panel-title">Báo cáo doanh thu theo sản phẩm</div>
                    <h4 class=""><span>Chọn khoảng thời gian:</span></h4>
                    <div class="ibox">
                        <div class="ibox-content">
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
                                <div class="col-lg-6">
                                    <button class="btn btn-info btn-outline" type="submit" value="name">Gửi báo
                                        cáo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Cơ Cấu sản phẩm bán ra</h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <div id="pie"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style= "background: white; padding: 10px;">
            <div class="col-12 text-end" style="float: right; margin-right: 20px;">
                <a href="{{ route('report.exportFileProduct', [
                    'startDate' => request('startDate'),
                    'endDate' => request('endDate'),
                ]) }}"
                    target="_blank" class="btn btn-primary btn-sm" id="exportFileProduct">
                    <i class="fa fa-file-export"></i> Xuất file
                </a>
            </div>
        </div>
        <script src="backend/js/plugins/d3/d3.min.js"></script>
        <script src="backend/js/plugins/c3/c3.min.js"></script>
        <script>
            function updateExportUrl() {
                let startDate = $('input[name="startDate"]').val();
                let endDate = $('input[name="endDate"]').val();

                if (startDate && endDate) {
                    let exportUrl = `{{ route('report.exportFileProduct') }}?startDate=${startDate}&endDate=${endDate}`;
                    console.log("Cập nhật URL xuất file: ", exportUrl);
                    $('#exportFileProduct').attr('href', exportUrl);
                }
            }

            $(document).ready(function() {
                updateExportUrl();
            });
            $('input[name="startDate"], input[name="endDate"]').change(updateExportUrl);


            $(document).ready(function() {
                var reports = {!! json_encode($reports) !!};
                var columns = [];
                reports.forEach(function(report) {

                    columns.push([report.product_name, parseInt(report.sum_revenue)]);
                });
                console.log("Columns data: ", columns);

                if (typeof c3 !== "undefined") {
                    c3.generate({
                        bindto: '#pie',
                        data: {
                            columns: columns,
                            type: 'pie'
                        },
                        tooltip: {
                            format: {
                                value: function(value, ratio, id) {
                                    return value.toLocaleString() + ' ₫';
                                }
                            }
                        }
                    });
                } else {
                    console.error("C3.js chưa được tải!");
                }
            });
        </script>
        <div class="row">
            <div class="ibox-content time">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-right">SKU</th>
                                <th>Tên SP</th>
                                <th class="text-right">SL khách</th>
                                <th class="text-right">SL hàng bán</th>
                                <th class="text-right">Giá gốc(vnđ)</th>
                                <th class="text-right">Giảm giá(vnđ)</th>
                                <th class="text-right">Doanh thu(vnđ)</th>
                        </thead>
                        <tbody>
                            @if (count($reports))
                                @php
                                    $td = [
                                        'sku',
                                        'product_name',
                                        'count_customer',
                                        'count_order',
                                        'sum_revenue|format',
                                        'sum_discount|format',
                                    ];
                                @endphp
                                @foreach ($reports as $key => $val)
                                    <tr class="table">
                                        @foreach ($td as $item)
                                            @php
                                                $explode = explode('|', $item);
                                                $value =
                                                    isset($explode[1]) && $explode[1] == 'format'
                                                        ? convert_price($val[$explode[0]], true)
                                                        : $val[$explode[0]];
                                            @endphp
                                            <td class="text-right">
                                                {{ $value }}
                                            </td>
                                        @endforeach
                                        <td class="text-right text-danger">
                                            {{ convert_price($val['sum_revenue'] - $val['sum_discount'], true) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
