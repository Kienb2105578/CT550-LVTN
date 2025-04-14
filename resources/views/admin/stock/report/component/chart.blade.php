<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Tổng Quan Doanh Thu & Chi Phí Nhập Hàng</h5>
                </div>
                <div class="ibox-content">
                    <div>
                        <div id="lineChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Số Đơn Hàng Thành Công & Lợi Nhuận</h5>
                </div>
                <div class="ibox-content">
                    <div>
                        <div id="slineChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $.ajax({
            type: "GET",
            url: "ajax/order/chartRevenueAndCost",
            dataType: "json",
            success: function(response) {
                console.log(response);

                let months = response.months;
                let revenue = ['Doanh thu'].concat(response.revenue);
                let cost = ['Chi phí nhập'].concat(response.cost);
                let profit = ['Lợi nhuận'].concat(response.profit);
                let orderCount = ['Số đơn hàng'].concat(response.orderCount);

                // Biểu đồ Doanh thu & Chi phí nhập
                c3.generate({
                    bindto: '#lineChart',
                    data: {
                        columns: [revenue, cost],
                        colors: {
                            'Doanh thu': '#1ab394',
                            'Chi phí nhập': '#ff6384'
                        }
                    },
                    axis: {
                        x: {
                            type: 'category',
                            categories: months
                        }
                    }
                });

                // Biểu đồ Số đơn hàng & Lợi nhuận
                c3.generate({
                    bindto: '#slineChart',
                    data: {
                        columns: [orderCount, profit],
                        colors: {
                            'Số đơn hàng': '#36a2eb',
                            'Lợi nhuận': '#f1c40f'
                        },
                        type: 'spline'
                    },
                    axis: {
                        x: {
                            type: 'category',
                            categories: months
                        }
                    }
                });
            },
            error: function() {
                console.error("Lỗi khi tải dữ liệu biểu đồ.");
            }
        });
    });
</script>
