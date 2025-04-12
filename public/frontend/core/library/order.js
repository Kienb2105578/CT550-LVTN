(function ($) {
    ("use strict");
    var HT = {}; // Khai báo là 1 đối tượng
    var timer = null;
    var _token = $('meta[name="csrf-token"]').attr("content");

    HT.getMyOrder = () => {
        $(".order-tab").on("click", function (e) {
            e.preventDefault();

            // Thêm class active cho tab được chọn
            $(".order-tab").removeClass("active");
            $(this).addClass("active");

            // Lấy thông tin trạng thái từ thuộc tính data
            let confirm = $(this).data("confirm") || "";
            let payment = $(this).data("payment") || "";
            let delivery = $(this).data("delivery") || "";

            console.log("Sending data:", { confirm, payment, delivery });

            // Gửi AJAX request để lấy danh sách đơn hàng
            $.ajax({
                url: "ajax/order/getMyOrder",
                type: "POST",
                data: {
                    _token: _token,
                    confirm: confirm,
                    payment: payment,
                    delivery: delivery,
                },
                beforeSend: function () {
                    $("#order-list").html(
                        '<p class="text-center">Đang tải dữ liệu...</p>'
                    );
                },
                success: function (response) {
                    console.log("Response data:", response); // Debug dữ liệu trả về

                    let html = "";
                    if ($.isEmptyObject(response)) {
                        html =
                            '<p class="text-center">Không có đơn hàng nào.</p>';
                    } else {
                        let sortedOrders = Object.entries(response).sort(
                            function (a, b) {
                                return b[0] - a[0]; // Sắp xếp theo orderId từ lớn đến nhỏ
                            }
                        );
                        $.each(
                            sortedOrders,
                            function (index, [orderId, order]) {
                                html += '<div class="order-section">';
                                let totalPrice = 0;

                                $.each(
                                    order.products,
                                    function (index, product) {
                                        if (!product) {
                                            console.warn(
                                                "Sản phẩm không tồn tại:",
                                                product
                                            );
                                            return;
                                        }

                                        let productTotal =
                                            product.price * product.qty;
                                        totalPrice += productTotal;

                                        html += `
            <div class="order-item">
                <img class="order-image" src="${
                    product.product_image
                }" alt="Product image">
                <div class="item-details">
                    <div class="item-name">${product.product_name}</div>
                    ${
                        product.variant_name
                            ? `<div class="item-category">Phân loại hàng: ${product.variant_name}</div>`
                            : ""
                    }
                    <div class="item-quantity">x${product.qty}</div>
                </div>
                <div class="item-price">
                    <div class="text-danger">
                        ${
                            product.price !== product.priceOriginal
                                ? `<span class="cart-price-old mr10">${new Intl.NumberFormat(
                                      "vi-VN"
                                  ).format(
                                      product.priceOriginal * product.qty
                                  )}đ</span>`
                                : ""
                        }
                        <span class="cart-price-sale">${new Intl.NumberFormat(
                            "vi-VN"
                        ).format(productTotal)}đ</span>
                    </div>
                </div>
            </div>
        `;
                                    }
                                );

                                let detailUrl = orderDetailUrl.replace(
                                    "__ID__",
                                    order.order_id
                                );

                                html += `
        <div class="order-footer">
            <div class="total-price">
                Thành tiền: ₫${new Intl.NumberFormat("vi-VN").format(
                    totalPrice
                )}
            </div>
            <div>
                ${
                    order.order_id
                        ? ` <a href="${detailUrl}" class="btn btn-outline-secondary">Chi tiết</a>`
                        : "<p>Không có mã đơn hàng hợp lệ.</p>"
                }
            </div>
        </div>
        <hr>
    `;

                                html += "</div>";
                            }
                        );
                    }
                    $("#order-list").html(html);
                },

                error: function () {
                    $("#order-list").html(
                        '<p class="text-center text-danger">Đã xảy ra lỗi khi tải dữ liệu.</p>'
                    );
                },
            });
        });
    };

    HT.cancelOrder = () => {
        $(document).on("click", ".cancelOrderButton", function () {
            let _this = $(this);
            let order = $(".orderData").data("order"); // Lấy dữ liệu mảng từ HTML (data-order='{...}')

            let option = {
                payload: {
                    [_this.attr("data-field")]: _this.attr("data-value"),
                },
                order: order,
                _token: _token,
            };

            $.ajax({
                url: "ajax/order/update-cancle",
                type: "POST",
                data: option,
                dataType: "json",
                success: function (res) {
                    setTimeout(function () {
                        if (res.error == 10) {
                            toastr.success(
                                res.messages,
                                "Thông báo từ hệ thống!"
                            );
                            HT.updateOrderStatusToCancel(_this);
                        } else {
                            toastr.error(
                                res.messages,
                                "Thông báo từ hệ thống!"
                            );
                        }
                    }, 500);
                },
                error: function (xhr, status, error) {
                    toastr.error(
                        "Lỗi: " + xhr.responseText,
                        "Lỗi khi hủy đơn hàng!"
                    );
                },
            });
        });
    };

    HT.updateOrderStatusToCancel = (_this) => {
        $(".order-status .value")
            .text("ĐƠN HÀNG ĐÃ ĐƯỢC HỦY")
            .addClass("text-danger");
        $(".order-status .text-right").html("");
    };

    HT.returnOrder = () => {
        $(document).on("click", ".returnOrderButton", function () {
            let _this = $(this);
            let order = $(".orderData").data("order");

            let option = {
                payload: {
                    [_this.attr("data-field")]: _this.attr("data-value"),
                },
                order: order,
                _token: _token,
            };

            $.ajax({
                url: "ajax/order/update-return",
                type: "POST",
                data: option,
                dataType: "json",
                success: function (res) {
                    console.log(res);
                    console.log(res.messages);
                    setTimeout(function () {
                        if (res.error == 10) {
                            console.log("THÀNH CÔNG");
                            toastr.success(
                                res.messages,
                                "Thông báo từ hệ thống!"
                            );
                            HT.updateOrderStatusToReturn(_this);
                        } else {
                            toastr.error(
                                res.messages,
                                "Thông báo từ hệ thống!"
                            );
                        }
                    }, 500);
                },

                error: function (xhr, status, error) {
                    toastr.error(
                        "Lỗi: " + xhr.responseText,
                        "Lỗi khi hoàn trả đơn hàng!"
                    );
                },
            });
        });
    };

    HT.updateOrderStatusToReturn = (_this) => {
        console.log("CẬP NHẬT GIAO DIỆN");
        $(".order-status .value")
            .text("ĐƠN HÀNG ĐANG ĐƯỢC HOÀN TRẢ")
            .addClass("text-success");
        $(".order-status .text-right").html("");
    };

    $(document).ready(function () {
        HT.getMyOrder();
        HT.cancelOrder();
        HT.returnOrder();
    });
})(jQuery);
