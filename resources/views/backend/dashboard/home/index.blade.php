@if ($usercatalogue_id_login == 1)
    <div class="wrapper wrapper-content">
        @include('backend.dashboard.component.statistic')

        <div class="wrapper wrapper-content animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <style>
                        .nav-tabs li a {
                            color: #000;
                            font-weight: normal;
                            text-transform: uppercase;
                        }

                        .nav-tabs li a:hover {
                            color: #007bff;
                        }

                        .nav-tabs .active a {
                            color: #007bff !important;
                            font-weight: bold;
                        }
                    </style>
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-1">Tổng quan</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-2">Doanh Thu</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-3">Đơn hàng hôm nay</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane active">
                                <div class="panel-body">
                                    @include('backend.dashboard.component.chart_2')
                                </div>
                            </div>
                            <div id="tab-2" class="tab-pane">
                                <div class="panel-body">
                                    @include('backend.dashboard.component.chart')
                                </div>
                            </div>
                            <div id="tab-3" class="tab-pane">
                                <div class="panel-body">
                                    @include('backend.dashboard.component.order')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row mb15 mt30">
        <div class="col-lg-4">
        </div>

        <canvas id="polarChart" height="140" style="display: none;"></canvas>
        <canvas id="doughnutChart" height="140" style="display: none;"></canvas>
        <div class="col-lg-4">
            <div class="widget red-bg p-lg text-center">
                <div class="m-b-md">
                    <i class="fa fa-bell fa-4x"></i>
                    <h1 class="m-xs">X</h1>
                    <h3 class="font-bold no-margins">
                        BẠN KHÔNG ĐỦ QUYỀN ĐỂ XEM CHỨC NĂNG NÀY
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
        </div>
    </div>
@endif
