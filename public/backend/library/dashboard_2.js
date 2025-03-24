(function ($) {
    "use strict";
    var HT = {};

    // Cấu hình biểu đồ Polar
    HT.createPolarChart = (labels, data) => {
        let ctx = document.getElementById("polarChart").getContext("2d");

        if (window.myPolarChart) {
            window.myPolarChart.destroy();
        }

        window.myPolarChart = new Chart(ctx, {
            type: "polarArea",
            data: {
                labels: labels,
                datasets: [
                    {
                        data: data,
                        backgroundColor: [
                            "#a3e1d4",
                            "#dedede",
                            "#b5b8cf",
                            "#ff6384",
                            "#36a2eb",
                        ],
                    },
                ],
            },
            options: { responsive: true },
        });
    };

    // Cấu hình biểu đồ Doughnut
    HT.createDoughnutChart = (labels, data) => {
        let ctx = document.getElementById("doughnutChart").getContext("2d");

        if (window.myDoughnutChart) {
            window.myDoughnutChart.destroy();
        }

        window.myDoughnutChart = new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: labels,
                datasets: [
                    {
                        data: data,
                        backgroundColor: [
                            "#a3e1d4",
                            "#dedede",
                            "#b5b8cf",
                            "#ff6384",
                            "#36a2eb",
                        ],
                    },
                ],
            },
            options: { responsive: true },
        });
    };

    // Gọi API để tải dữ liệu Doughnut Chart
    HT.loadDoughnutChart = () => {
        $.ajax({
            type: "GET",
            url: "ajax/order/chartDoughnutChart",
            dataType: "json",
            success: function (response) {
                console.log(response);
                HT.createDoughnutChart(response.labels, response.values);
            },
            error: function () {
                console.error("Lỗi khi tải dữ liệu biểu đồ Doughnut");
            },
        });
    };

    // Gọi API để tải dữ liệu Polar Chart
    HT.loadPolarChart = () => {
        $.ajax({
            type: "GET",
            url: "ajax/order/chartPolarChart",
            dataType: "json",
            success: function (response) {
                console.log(response);
                HT.createPolarChart(response.labels, response.values);
            },
            error: function () {
                console.error("Lỗi khi tải dữ liệu biểu đồ Polar");
            },
        });
    };

    // Khởi tạo khi trang được load
    $(document).ready(function () {
        HT.loadDoughnutChart();
        HT.loadPolarChart();
    });
})(jQuery);
