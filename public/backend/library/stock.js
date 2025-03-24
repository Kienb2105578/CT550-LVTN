(function ($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf-token"]').attr("content");

    HT.getInventoryWithPurchase = () => {
        $(document).on("click", ".edit-stock", function () {
            let purchaseOrderId = $(this).data("id");

            $.ajax({
                url: "ajax/stock/getInventoryWithPurchase",
                type: "GET",
                data: { _id: purchaseOrderId },
                success: function (response) {
                    let content = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>SL nhập</th>
                                <th>Còn lại</th>
                                <th>Giá nhập</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>`;

                    response.forEach((item) => {
                        let checked = item.publish == 2 ? "checked" : "";
                        content += `
                        <tr>
                            <td><strong>${item.full_product_name}</strong></td>
                            <td>${item.initial_quantity}</td>
                            <td>${item.remaining_quantity}</td>
                            <td>${HT.formatCurrency(item.price)}</td>
                            <td>
                                <label class="switch-table">
                                    <input type="checkbox" class="toggle-status" data-id="${
                                        item.id
                                    }" ${checked}>
                                    <span class="slider-table round"></span>
                                </label>
                            </td>
                        </tr>`;
                    });

                    content += `</tbody></table>`;

                    $("#modalContent").html(content);
                    $("#editModal").modal("show");
                },
                error: function () {
                    $("#modalContent").html(
                        '<p class="text-danger">Lỗi khi tải dữ liệu!</p>'
                    );
                },
            });
        });
    };

    HT.togglePublishStatus = () => {
        $(document).on("change", ".toggle-status", function () {
            let _this = $(this);
            let id = _this.data("id");
            let status = _this.is(":checked") ? 2 : 1; // Check = 2, Uncheck = 1

            $.ajax({
                url: "ajax/stock/changeStatus",
                type: "POST",
                data: { id: id, status: status, _token: _token },
                dataType: "json",
                success: function (res) {
                    if (res.flag) {
                        toastr.success("Cập nhật trạng thái thành công!");
                    } else {
                        toastr.error("Không thể cập nhật trạng thái.");
                        _this.prop("checked", status === 2 ? false : true); // Hoàn tác nếu lỗi
                    }
                },
                error: function () {
                    toastr.error("Lỗi khi cập nhật trạng thái.");
                    _this.prop("checked", status === 2 ? false : true); // Hoàn tác nếu lỗi
                },
            });
        });
    };

    HT.closeModal = () => {
        $(document).on("click", ".btn-secondary", function () {
            $("#editModal").modal("hide");
        });

        $("#editModal").on("hidden.bs.modal", function () {
            $("#modalContent").html("Đang tải dữ liệu...");
        });
    };

    HT.formatCurrency = (number) => {
        return new Intl.NumberFormat("vi-VN", {
            style: "currency",
            currency: "VND",
        }).format(number);
    };

    HT.getInventoryWithProduct = () => {
        $(document).on("click", ".edit-product", function () {
            let productId = $(this).data("id"); // Lấy product_id từ data-id
            let variantId = $(this).data("variant"); // Lấy variant_id từ data-variant
            $.ajax({
                url: "ajax/stock/getInventoryWithProduct", // Địa chỉ URL của API backend
                type: "GET",
                data: {
                    _id: productId, // Gửi product_id
                    variant_id: variantId, // Gửi variant_id
                },
                success: function (response) {
                    let content = `
                <div class="product-info">
                    <div><strong>Tên sản phẩm: </strong>${
                        response.product_name
                    }</div> <!-- Hiển thị tên sản phẩm bên ngoài bảng -->
                    <div><strong>Giá bán: </strong>${HT.formatCurrency(
                        response.product_price
                    )}</div> <!-- Hiển thị giá bán bên ngoài bảng -->
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mã lô hàng</th> 
                            <th>SL nhập</th>
                            <th>Còn lại</th>
                            <th>Giá nhập</th> 
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>`;

                    response.details.forEach((item) => {
                        let checked = item.publish == 2 ? "checked" : "";
                        content += `
                    <tr>
                        <td><strong>${
                            item.batch_id
                        }</strong></td> <!-- Hiển thị Mã lô hàng -->
                        <td>${item.initial_quantity}</td>
                        <td>${item.remaining_quantity}</td>
                        <td>${HT.formatCurrency(
                            item.price
                        )}</td> <!-- Hiển thị Giá bán -->
                        <td>
                            <label class="switch-table">
                                <input type="checkbox" class="toggle-status" data-id="${
                                    item.id
                                }" ${checked}>
                                <span class="slider-table round"></span>
                            </label>
                        </td>
                    </tr>`;
                    });

                    content += `</tbody></table>`;

                    $("#modalContent").html(content);
                    $("#productDetailModal").modal("show");
                },
                error: function () {
                    $("#modalContent").html(
                        '<p class="text-danger">Lỗi khi tải dữ liệu!</p>'
                    );
                },
            });
        });
    };

    $(document).ready(function () {
        HT.getInventoryWithPurchase();
        HT.togglePublishStatus();
        HT.closeModal();
        HT.getInventoryWithProduct();
    });
})(jQuery);
