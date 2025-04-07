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

    var lineData = {
        labels: [
            "Tháng 1", // Tháng 1
            "Tháng 2", // Tháng 2
            "Tháng 3", // Tháng 3
            "Tháng 4", // Tháng 4
            "Tháng 5", // Tháng 5
            "Tháng 6", // Tháng 6
            "Tháng 7", // Tháng 7
            "Tháng 8", // Tháng 8
            "Tháng 9", // Tháng 9
            "Tháng 10", // Tháng 10
            "Tháng 11", // Tháng 11
            "Tháng 12", // Tháng 12
        ],
        datasets: [
            {
                label: "Lợi nhuận", // Dữ liệu Lợi nhuận
                backgroundColor: "rgba(101, 167, 217, 0.50)", // Màu xanh lá
                borderColor: "rgba(35, 168, 240, 0.7)",
                pointBackgroundColor: "rgb(87, 177, 225)",
                pointBorderColor: "#fff",
                data: [
                    1000000, 1200000, 1500000, 1100000, 1700000, 1600000,
                    1400000, 1550000, 1600000, 1450000, 1800000, 1700000,
                ], // Lợi nhuận trong 12 tháng
            },
            {
                label: "Chi tiêu", // Dữ liệu Chi tiêu
                backgroundColor: "rgba(220, 220, 220, 0.5)", // Màu xám
                pointBorderColor: "#fff",
                data: [
                    800000, 1000000, 1300000, 950000, 1400000, 1300000, 1200000,
                    1250000, 1100000, 1150000, 1500000, 1400000,
                ], // Chi tiêu trong 12 tháng
            },
        ],
    };

    var lineOptions = {
        responsive: true,
    };

    var ctx = document.getElementById("lineChart").getContext("2d");
    new Chart(ctx, { type: "line", data: lineData, options: lineOptions });

    // Khởi tạo khi trang được load
    $(document).ready(function () {
        HT.loadDoughnutChart();
        HT.loadPolarChart();
    });
})(jQuery);
