<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-6">
            <div class="widget navy-bg no-padding">
                <div class="p-m">
                    <h1 class="m-xs">{{ convert_price($orderStatistic['revenue'], true) }} đ</h1>

                    <h3 class="font-bold no-margins">
                        Tổng doanh thu
                    </h3>
                    <small>Nguồn thu từ INCOM</small>
                </div>
                <div class="flot-chart">
                    <div class="flot-chart-content" id="flot-chart1"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="widget lazur-bg no-padding">
                <div class="p-m">
                    <h1 class="m-xs">{{ convert_price($orderStatistic['orderIncomeMonth'], true) }} đ</h1>

                    <h3 class="font-bold no-margins">
                        Doanh thu tháng hiện tại
                    </h3>
                    <small>Nguồn thu từ INCOM</small>
                </div>
                <div class="flot-chart">
                    <div class="flot-chart-content" id="flot-chart2"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="widget gray-bg no-padding">
                <div class="p-m">
                    <h1 class="no-margins">{{ $orderStatistic['orderCurrentMonth'] }}</h1>
                    <h3 class="font-bold no-margins">
                        Đơn hàng trong tháng
                    </h3>
                    {!! growHtml($orderStatistic['grow']) !!}
                    <small>Tăng trưởng với tháng trước</small>
                </div>
                <div class="flot-chart">
                    <div class="flot-chart-content" id="flot-chart3"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <div class="widget style1">
                <div class="row">
                    <div class="col-xs-4 text-center">
                        <i class="fa fa-trophy fa-5x"></i>
                    </div>
                    <div class="col-xs-8 text-right">
                        <span>Doanh thu hôm nay</span>
                        <h2 class="font-bold" style="font-size: 18px">
                            {{ convert_price($orderStatistic['orderIncomeToday'], true) }} đ</h2>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .report-da {
                display: flex;
                justify-content: center;
                align-items: center;
                padding-top: 15px;
            }

            .report-da h4 {
                font-size: 16px;
                text-transform: uppercase;
            }

            .dashboard {
                display: block;
            }

            .widget.style1.navy-bg {
                background-color: #2c547d;
                transition: background-color 0.3s ease;
            }

            .dashboard:hover .widget.style1.navy-bg {
                background-color: rgb(4, 45, 80);
            }

            .widget.style1.lazur-bg {
                background-color: #0099cc;
                transition: background-color 0.3s ease;
            }

            .dashboard:hover .widget.style1.lazur-bg {
                background-color: green;

            }

            /* Yellow background */
            .widget.style1.yellow-bg {
                background-color: #f8c600;
                transition: background-color 0.3s ease;
            }

            .dashboard:hover .widget.style1.yellow-bg {
                background-color: orange;
                /* Đổi thành màu khi hover */
            }

            .dashboard:hover {
                text-decoration: none;
            }
        </style>

        <div class="col-lg-3">
            <a href="{{ route('dashboard.index') }}" class="dashboard">
                <div class="widget style1 navy-bg">
                    <div class="row">
                        <div class="col-xs-4">
                            <i class="fa fa-pie-chart fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right report-da">
                            <h4 class="font-bold">Tổng quan</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3">
            <a href="{{ route('report.product') }}" class="dashboard">
                <div class="widget style1 lazur-bg">
                    <div class="row">
                        <div class="col-xs-4">
                            <i class="fa fa-envelope-o fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right report-da">
                            <h4 class="font-bold">Xem báo cáo theo Sản phẩm</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3">
            <a href="{{ route('report.time') }}" class="dashboard">
                <div class="widget style1 yellow-bg">
                    <div class="row">
                        <div class="col-xs-4">
                            <i class="fa fa-bar-chart-o fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right report-da">
                            <h4 class="font-bold">Xem báo cáo theo thời gian</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <script src="backend/js/plugins/flot/jquery.flot.js"></script>
    <script src="backend/js/plugins/flot/jquery.flot.resize.js"></script>
    <script>
        $(document).ready(function() {
            var d1 = [
                [1262304000000, 6],
                [1264982400000, 3057],
                [1267401600000, 20434],
                [1270080000000, 31982],
                [1272672000000, 26602],
                [1275350400000, 27826],
                [1277942400000, 24302],
                [1280620800000, 24237],
                [1283299200000, 21004],
                [1285891200000, 12144],
                [1288569600000, 10577],
                [1291161600000, 10295]
            ];
            var d2 = [
                [1262304000000, 5],
                [1264982400000, 200],
                [1267401600000, 1605],
                [1270080000000, 6129],
                [1272672000000, 11643],
                [1275350400000, 19055],
                [1277942400000, 30062],
                [1280620800000, 39197],
                [1283299200000, 37000],
                [1285891200000, 27000],
                [1288569600000, 21000],
                [1291161600000, 17000]
            ];

            var data1 = [{
                    label: "Data 1",
                    data: d1,
                    color: '#17a084'
                },
                {
                    label: "Data 2",
                    data: d2,
                    color: '#127e68'
                }
            ];
            $.plot($("#flot-chart1"), data1, {
                xaxis: {
                    tickDecimals: 0
                },
                series: {
                    lines: {
                        show: true,
                        fill: true,
                        fillColor: {
                            colors: [{
                                opacity: 1
                            }, {
                                opacity: 1
                            }]
                        }
                    },
                    points: {
                        width: 0.1,
                        show: false
                    }
                },
                grid: {
                    show: false,
                    borderWidth: 0
                },
                legend: {
                    show: false
                }
            });

            var data2 = [{
                label: "Data 1",
                data: d1,
                color: '#19a0a1'
            }];
            $.plot($("#flot-chart2"), data2, {
                xaxis: {
                    tickDecimals: 0
                },
                series: {
                    lines: {
                        show: true,
                        fill: true,
                        fillColor: {
                            colors: [{
                                opacity: 1
                            }, {
                                opacity: 1
                            }]
                        }
                    },
                    points: {
                        width: 0.1,
                        show: false
                    }
                },
                grid: {
                    show: false,
                    borderWidth: 0
                },
                legend: {
                    show: false
                }
            });

            var data3 = [{
                    label: "Data 1",
                    data: d1,
                    color: '#fbbe7b'
                },
                {
                    label: "Data 2",
                    data: d2,
                    color: '#f8ac59'
                }
            ];
            $.plot($("#flot-chart3"), data3, {
                xaxis: {
                    tickDecimals: 0
                },
                series: {
                    lines: {
                        show: true,
                        fill: true,
                        fillColor: {
                            colors: [{
                                opacity: 1
                            }, {
                                opacity: 1
                            }]
                        }
                    },
                    points: {
                        width: 0.1,
                        show: false
                    }
                },
                grid: {
                    show: false,
                    borderWidth: 0
                },
                legend: {
                    show: false
                }
            });

            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green'
            });

            $(".todo-list").sortable({
                placeholder: "sort-highlight",
                handle: ".handle",
                forcePlaceholderSize: true,
                zIndex: 999999
            }).disableSelection();

            var mapData = {
                "US": 498,
                "SA": 200,
                "CA": 1300,
                "DE": 220,
                "FR": 540,
                "CN": 120,
                "AU": 760,
                "BR": 550,
                "IN": 200,
                "GB": 120,
                "RU": 2000
            };

            $('#world-map').vectorMap({
                map: 'world_mill_en',
                backgroundColor: "transparent",
                regionStyle: {
                    initial: {
                        fill: '#e4e4e4',
                        "fill-opacity": 1,
                        stroke: 'none',
                        "stroke-width": 0,
                        "stroke-opacity": 0
                    }
                },
                series: {
                    regions: [{
                        values: mapData,
                        scale: ["#1ab394", "#22d6b1"],
                        normalizeFunction: 'polynomial'
                    }]
                }
            });
        });
    </script>
