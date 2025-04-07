(function ($) {
    "use strict";
    var HT = {};

    HT.getProductDetails = function (productId, callback) {
        $.ajax({
            url: "/ajax/purchaseOrder/getProductDetails",
            type: "POST",
            data: { product_id: productId },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Nếu có CSRF
            },
            success: function (response) {
                if (callback) callback(response);
            },
            error: function () {
                alert("Lỗi khi lấy thông tin sản phẩm!");
            },
        });
    };
    HT.loadExistingProducts = function (orderId) {
        $.ajax({
            url: "/ajax/purchaseOrder/loadExistingProducts/" + orderId,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.details.length > 0) {
                    response.details.forEach((product) => {
                        HT.renderProductInfo(product, true);
                    });
                }
            },
            error: function () {
                alert("Lỗi khi tải thông tin đơn hàng!");
            },
        });
    };

    HT.renderProductInfo = function (product, isEditing = false) {
        let showPriceColumn =
            !product.variants || product.variants.length === 0;

        console.log(product.status);
        let deleteButton = "";
        if (!product.status || product.status === "pending") {
            deleteButton = `<button type="button" class="btn btn-danger btn-sm remove-product" 
                            style="float: right;" data-id="${product.id}">Xóa</button>`;
        }

        let html = `
    <div class="ibox product-info" data-product-id="${product.id}">
        <div class="ibox-title" style="padding-bottom: 30px;">
            <h5>Thông tin sản phẩm - ${product.name}</h5>
           ${deleteButton} 
        </div>
        <div class="ibox-content">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" style="width:7%">Mã SP</th>
                        <th>Tên sản phẩm</th>
                        <th class="text-center" style="width:15%">Số lượng nhập</th>
                        ${
                            showPriceColumn
                                ? `<th class="text-center" style="width:15%">Giá mua 1 SP</th>`
                                : ""
                        }
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">${product.id}</td>
                        <td>${product.name}</td>
                        <td>
                            <input type="number" name="quantity[${
                                product.id
                            }]" class="form-control" value="${
            isEditing ? product.quantity : ""
        }" min="1">
                        </td>
                        ${
                            showPriceColumn
                                ? `<td><input type="text" name="price[${
                                      product.id
                                  }]" class="form-control int price-input" value="${
                                      isEditing && product.price
                                          ? product.price.toLocaleString(
                                                "vi-VN"
                                            )
                                          : ""
                                  }" data-raw-value="${
                                      isEditing ? product.price : ""
                                  }"></td>`
                                : ""
                        }
                    </tr>
                </tbody>
            </table>`;

        // Nếu có biến thể, hiển thị danh sách biến thể
        if (product.variants && product.variants.length > 0) {
            html += `
        <div class="variant-section mt-4">
            <h5>Danh sách phiên bản</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên phiên bản</th>
                        <th>Số lượng hiện tại</th>
                        <th>Số lượng nhập mới</th>
                        <th>Giá mua 1 SP</th>
                    </tr>
                </thead>
                <tbody>`;
            product.variants.forEach((variant) => {
                html += `
            <tr>
                <td>${variant.name}</td>
                <td>${variant.quantity}</td>
                <td>
                    <input type="number" name="quantity[${product.id}][${
                    variant.id
                }]" class="form-control" value="${
                    isEditing ? variant.quantity : ""
                }" min="1">
                </td>
                <td>
                    <input type="text" name="price[${product.id}][${
                    variant.id
                }]" class="form-control int price-input" value="${
                    isEditing && variant.price
                        ? variant.price.toLocaleString("vi-VN")
                        : ""
                }" data-raw-value="${isEditing ? variant.price : ""}">
                </td>
            </tr>`;
            });
            html += `</tbody></table></div>`;
        }

        html += `</div></div>`;

        $("#product-info-container").append(html);

        // Lắng nghe sự kiện nhập giá để tự động format số
        $(".price-input").on("input", function () {
            let rawValue = $(this).val().replace(/\./g, ""); // Xóa dấu chấm
            if (!isNaN(rawValue) && rawValue !== "") {
                $(this).attr("data-raw-value", rawValue);
                $(this).val(Number(rawValue).toLocaleString("vi-VN"));
            } else {
                $(this).val("");
                $(this).attr("data-raw-value", "");
            }
        });

        $(".price-input").on("blur", function () {
            let rawValue = $(this).attr("data-raw-value");
            if (rawValue && !isNaN(rawValue)) {
                $(this).val(Number(rawValue).toLocaleString("vi-VN"));
            }
        });
        // Lắng nghe sự kiện thay đổi số lượng cho sản phẩm chính
        $(`input[name="quantity[${product.id}]"]`).on("input", function () {
            if (!product.variants || product.variants.length === 0) {
                // Không có biến thể, người dùng nhập số lượng bình thường
                $(this).prop("readonly", false);
                return;
            }

            let totalQuantity = 0;
            $(`input[name^="quantity[${product.id}]["]`).each(function () {
                totalQuantity += parseInt($(this).val()) || 0;
            });

            $(this).val(totalQuantity).prop("readonly", true);
        });

        // Lắng nghe sự kiện thay đổi số lượng cho các biến thể (nếu có)
        $(`input[name^="quantity[${product.id}]["]`).on("input", function () {
            let totalQuantity = 0;
            $(`input[name^="quantity[${product.id}]["]`).each(function () {
                totalQuantity += parseInt($(this).val()) || 0;
            });
            $(`input[name="quantity[${product.id}]"]`)
                .val(totalQuantity)
                .prop("readonly", true);
        });
    };

    HT.getFullInfoProduct = function () {
        $("#product_id").on("change", function () {
            var productId = $(this).val();
            if (!productId) return;

            if (
                $(".product-info[data-product-id='" + productId + "']").length
            ) {
                alert("Sản phẩm này đã được chọn!");
                return;
            }

            HT.getProductDetails(productId, function (product) {
                HT.renderProductInfo(product);
            });
        });

        $(document).on("click", ".remove-product", function () {
            var productId = $(this).data("id");
            $(".product-info[data-product-id='" + productId + "']").remove();
        });
    };

    HT.int = () => {
        $(document).on("change keyup blur", ".int", function () {
            let _this = $(this);
            let value = _this.val();

            // Nếu giá trị rỗng, thay thế bằng 0
            if (value === "") {
                $(this).val("0");
                return;
            }

            // Xóa tất cả dấu chấm
            value = value.replace(/\./gi, "");

            // Kiểm tra nếu giá trị không phải là số
            if (isNaN(value)) {
                _this.val("0");
                return;
            }

            // Định dạng lại giá trị với dấu phân cách
            _this.val(HT.addCommas(value));
        });

        // Xử lý sự kiện keydown, nếu giá trị là 0, chỉ cho phép nhập dấu chấm (.)
        $(document).on("keydown", ".int", function (e) {
            let _this = $(this);
            let data = _this.val();
            if (data == 0) {
                let unicode = e.keyCode || e.which;
                if (unicode != 190) {
                    _this.val(""); // Xóa giá trị khi nhập số khác 0
                }
            }
        });
    };

    HT.addCommas = (nStr) => {
        nStr = String(nStr); // Chuyển giá trị thành chuỗi
        nStr = nStr.replace(/\./gi, ""); // Xóa dấu chấm trước khi định dạng lại

        let str = "";
        for (let i = nStr.length; i > 0; i -= 3) {
            let a = i - 3 < 0 ? 0 : i - 3;
            str = nStr.slice(a, i) + "." + str;
        }
        str = str.slice(0, str.length - 1); // Loại bỏ dấu chấm thừa ở cuối
        return str;
    };

    $(document).ready(function () {
        HT.getFullInfoProduct();
        HT.int();

        let orderId = $("#purchase_order_id").val();
        if (orderId) {
            HT.loadExistingProducts(orderId);
        }
    });
})(jQuery);
