<div class="wrapper wrapper-content animated fadeInRight mt30">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Biểu đồ doanh thu năm {{ date('Y') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="chartContainer">
                                <canvas id="barChart" height="120"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="row mb15">
                                <div class="pull-right col-lg-12">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-xs btn-white chartButton active"
                                            data-chart="1">Theo Năm</button>
                                        <button type="button" class="btn btn-xs btn-white chartButton" data-chart="30">
                                            Theo Tháng</button>
                                        <button type="button" class="btn btn-xs btn-white chartButton" data-chart="7">7
                                            ngày</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <ul class="stat-list">
                                        <li>
                                            <h2 class="no-margins">{{ $orderStatistic['totalOrders'] }}</h2>
                                            <small>Tổng số đơn hàng hoàn thành</small>
                                            <div class="progress progress-mini">
                                                <div style="width: 48%;" class="progress-bar"></div>
                                            </div>
                                        </li>
                                        <li>
                                            <h2 class="no-margins ">{{ $orderStatistic['productTotal'] }}</h2>
                                            <small>Tổng số sản phẩm</small>
                                            <div class="progress progress-mini">
                                                <div style="width: 22%;" class="progress-bar"></div>
                                            </div>
                                        </li>
                                        <li>
                                            <h2 class="no-margins ">{{ $customerStatistic['totalCustomers'] }}</h2>
                                            <small>Tổng số khách hàng</small>
                                            <div class="progress progress-mini">
                                                <div style="width: 60%;" class="progress-bar"></div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@php
    $data = json_encode($orderStatistic['revenueChart']['data']);
    $label = json_encode($orderStatistic['revenueChart']['label']);
@endphp

<script>
    var data = JSON.parse('{!! $data !!}')
    var label = JSON.parse('{!! $label !!}')
</script>
