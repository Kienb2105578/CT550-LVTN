<form action="{{ route('report.time') }}" method="get">
    <div class="wrapper wrapper-content report-time">
        @include('admin.dashboard.component.statistic')
        <div class="row mb15 mt30">
            <div class="col-lg-6">
                <div class="panel-title">Báo cáo doanh thu</div>
                <h4 class="heading-1"><span>Chọn khoảng thời gian:</span></h4>
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
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Cơ Cấu bán ra theo ngày</h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <div id="pie"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Lợi nhuận theo ngày</h5>
                    </div>
                    <div class="ibox-content">
                        <div id="morris-line-chart" style="height: 250px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style= "background: white; padding: 10px;">
            <div class="col-12 text-end" style="float: right; margin-right: 20px;">
                <a href="{{ route('report.exportFileTime', [
                    'startDate' => request('startDate'),
                    'endDate' => request('endDate'),
                ]) }}"
                    target="_blank" class="btn btn-primary btn-sm" id="exportFileTime">
                    <i class="fa fa-file-export"></i> Xuất file
                </a>
            </div>
        </div>
        <script src="backend/js/plugins/d3/d3.min.js"></script>
        <script src="backend/js/plugins/c3/c3.min.js"></script>
        <script src="backend/js/plugins/morris/raphael-2.1.0.min.js"></script>
        <script src="backend/js/plugins/morris/morris.js"></script>
        <script src="{{ asset('backend/js/plugins/icheck/icheck.min.js') }}"></script>
        <script src="backend/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
        <script src="backend/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <script>
            function updateExportUrl() {
                let startDate = $('input[name="startDate"]').val();
                let endDate = $('input[name="endDate"]').val();

                if (startDate && endDate) {
                    let exportUrl = `{{ route('report.exportFileTime') }}?startDate=${startDate}&endDate=${endDate}`;
                    console.log("Cập nhật URL xuất file: ", exportUrl);
                    $('#exportFileTime').attr('href', exportUrl);
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
                    columns.push([report.order_date, parseInt(report.sum_revenue)]);
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
            var chartData = @json($charts);

            Morris.Line({
                element: 'morris-line-chart',
                data: chartData,
                xkey: 'order_date',
                ykeys: ['sum_profit'],
                labels: ['Profit'],
                lineColors: ['#1ab394'],
                resize: true,
                lineWidth: 4,
                pointSize: 5,
            });
        </script>
        <div class="row">
            <div class="ibox-content time">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Ngày</th>
                            <th class="text-center">SL khách hàng</th>
                            <th class="text-center">SL đơn</th>
                            <th class="text-center">Tiền hàng(vnđ)</th>
                            <th class="text-center">Tổng chiết khấu(vnđ)</th>
                            <th class="text-center">Doanh thu(vnđ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($reports))
                            @php
                                $td = [
                                    'order_date',
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
                                        <td class="text-center">
                                            {{ $value }}
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        {{ convert_price($val['sum_revenue'] - $val['sum_discount'], true) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<style>
    .morris-hover {
        z-index: 10000;
        position: absolute;
        background-color: #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .morris-default-style {
        margin: 0 auto;
        padding: 10px;
    }

    .morris-tooltip {
        position: relative;
        z-index: 10001;
        background-color: #fff;
        padding: 5px;
        border-radius: 3px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .nosbij {
        margin-left: 20px;
    }
</style>
