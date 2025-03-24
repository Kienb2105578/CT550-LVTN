(function ($) {
    ("use strict");
    var HT = {}; // Khai báo là 1 đối tượng
    var timer = null;
    var _token = $('meta[name="csrf-token"]').attr("content");

    /* MAIN VARIABLE */

    var $window = $(window),
        $document = $(document);

    // FUNCTION DECLARGE
    $.fn.elExists = function () {
        return this.length > 0;
    };

    HT.addWishlish = () => {
        $(document).on("click", ".addToWishlist", function (e) {
            e.preventDefault();

            let _this = $(this);

            $.ajax({
                url: "ajax/product/wishlist",
                type: "POST",
                data: {
                    id: _this.attr("data-id"),
                    _token: _token,
                },
                dataType: "json",
                beforeSend: function () {},
                success: function (res) {
                    toastr.success(res.message, "Thông báo từ hệ thống!");
                    if (res.code == 1) {
                        _this.removeClass("active");
                    } else if (res.code == 2) {
                        _this.addClass("active");
                    }
                },
            });
        });
    };

    HT.addCart = () => {
        $(document).on("click", ".addToCart", function (e) {
            e.preventDefault();
            let _this = $(this);
            let id = _this.attr("data-id");
            let type = _this.attr("data-type");
            let quantity = $(".quantity-text").val();
            if (typeof quantity === "undefined") {
                quantity = 1;
            }

            let attribute_ids = [];
            $(".attribute-value .choose-attribute").each(function () {
                let _this = $(this);
                if (_this.hasClass("active")) {
                    attribute_ids.push(_this.attr("data-attributeid"));
                }
            });

            let option = {
                product_id: id,
                quantity: quantity,
                attribute_id:
                    attribute_ids.length > 0 ? attribute_ids.join(",") : "",
                _token: _token,
            };

            console.log("TADA", option);

            // Kiểm tra số lượng trước khi thêm vào giỏ hàng
            $.ajax({
                url: "ajax/product/checkQuantity",
                type: "POST",
                data: option,
                dataType: "json",
                success: function (res) {
                    toastr.clear();
                    console.log(res);
                    if (res.status === "ok") {
                        // Thêm vào giỏ hàng nếu số lượng hợp lệ
                        $.ajax({
                            url: "ajax/cart/create",
                            type: "POST",
                            data: {
                                id: id,
                                quantity: quantity,
                                attribute_id: attribute_ids,
                                _token: _token,
                            },
                            dataType: "json",
                            success: function (res) {
                                if (res.code === 10) {
                                    toastr.success(
                                        res.messages,
                                        "Thông báo từ hệ thống!"
                                    );
                                    HT.changeCartHeader(res);
                                    $("#cartTotalItem").html(res.countMiniCart);
                                    if (type === "buy") {
                                        setTimeout(() => {
                                            window.location.href =
                                                "thanh-toan.html";
                                        }, 100);
                                    }
                                } else {
                                    toastr.error(
                                        "Có vấn đề xảy ra! Hãy thử lại",
                                        "Thông báo từ hệ thống!"
                                    );
                                }
                            },
                            error: function () {
                                if (res.code === 11) {
                                    toastr.error(
                                        "Lỗi khi thêm vào giỏ hàng!",
                                        "Thông báo từ hệ thống!"
                                    );
                                }
                            },
                        });
                    } else {
                        toastr.error(res.message, "Thông báo từ hệ thống!");
                    }
                },
                error: function () {
                    toastr.error(
                        "Lỗi khi kiểm tra số lượng!",
                        "Thông báo từ hệ thống!"
                    );
                },
            });
        });
    };

    HT.changeQuantity = () => {
        $(document).on("click", ".quantity-button", function () {
            let _this = $(this);
            let quantityInput = $(".quantity-text");
            let quantity = parseInt(quantityInput.val());
            let newQuantity = _this.hasClass("minus")
                ? quantity - 1
                : quantity + 1;
            let productId = quantityInput.data("product-id"); // Giả sử có attribute data-product-id

            let attributeId = HT.selectedAttributeId.join(","); // Lấy từ biến toàn cục

            if (newQuantity < 1) {
                newQuantity = 1;
            }

            $.ajax({
                url: "ajax/product/checkQuantity",
                type: "POST",
                data: {
                    product_id: productId,
                    quantity: newQuantity,
                    attribute_id: attributeId,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    console.log("Phản hồi từ server:", response);
                    if (response.status === "ok") {
                        quantityInput.val(newQuantity);
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Lỗi AJAX:", xhr.responseText);
                    alert("Có lỗi xảy ra! Xem console để biết chi tiết.");
                },
            });
        });
    };

    HT.changeQuantityCart = () => {
        $(document).on("click", ".btn-qty", function () {
            let _this = $(this);
            let qtyElement = _this.siblings(".input-qty");
            let rowId = _this.siblings(".rowId").val();
            let productId = _this.siblings(".productId").val();
            let attributeIds = _this
                .closest(".cart-item") // Hoặc lớp cha của các phần tử liên quan
                .find(".attributeId")
                .map(function () {
                    return $(this).val();
                })
                .get()
                .join(",");

            let newQty = _this.hasClass("minus")
                ? Math.max(parseInt(qtyElement.val()) - 1, 1)
                : parseInt(qtyElement.val()) + 1;
            console.log(newQty);
            $.ajax({
                url: "ajax/product/checkQuantityCart",
                type: "POST",
                data: {
                    product_id: productId,
                    quantity: newQty,
                    attribute_id: attributeIds,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.status === "ok") {
                        qtyElement.val(newQty);
                        HT.handleUpdateCart(_this, {
                            qty: newQty,
                            rowId: _this.siblings(".rowId").val(),
                            _token: $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        });
                    } else {
                        toastr.error(response.message, "Thông báo từ hệ thống");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Lỗi AJAX:", xhr.responseText);
                    alert("Có lỗi xảy ra! Vui lòng thử lại.");
                },
            });
        });
    };

    HT.changeQuantityInputCart = () => {
        $(document).on("change", ".input-qty", function () {
            let _this = $(this);
            let rowId = _this.siblings(".rowId").val();
            let productId = _this.siblings(".productId").val();
            let attributeIds = _this
                .siblings(".attributeId")
                .map(function () {
                    return $(this).val();
                })
                .get()
                .join(",");
            let newQty = parseInt(_this.val()) || 1;

            // Gửi AJAX kiểm tra số lượng tồn kho
            $.ajax({
                url: "ajax/product/checkQuantityCart",
                type: "POST",
                data: {
                    product_id: productId,
                    quantity: newQty,
                    attribute_id: attributeIds,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.status === "ok") {
                        _this.val(newQty);

                        // Cập nhật giỏ hàng
                        HT.handleUpdateCart(_this, {
                            qty: newQty,
                            rowId: _this.siblings(".rowId").val(),
                            _token: $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        });
                    } else {
                        toastr.error(response.message, "Thông báo từ hệ thống");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Lỗi AJAX:", xhr.responseText);
                    alert("Có lỗi xảy ra! Vui lòng thử lại.");
                },
            });
        });
    };

    HT.handleUpdateCart = (_this, option) => {
        $.ajax({
            url: "ajax/cart/update",
            type: "POST",
            data: option,
            dataType: "json",
            success: function (res) {
                toastr.clear();
                if (res.code === 10) {
                    $("#cartTotalItem").html(res.countMiniCart);
                    HT.changeMinyQuantityItem(_this, option);
                    HT.changeCartItemSubTotal(_this, res);
                    HT.changeCartOriginalItemSubTotal(_this, res);
                    HT.changetotalDiscountPromotion(res);
                    HT.changeCartTotal(res);

                    toastr.success(res.messages, "Thông báo từ hệ thống!");
                } else {
                    toastr.error(
                        "Có vấn đề xảy ra! Hãy thử lại",
                        "Thông báo từ hệ thống!"
                    );
                }
            },
        });
    };

    HT.changeMinyQuantityItem = (item, option) => {
        item.parents(".cart-item").find(".cart-item-number").html(option.qty);
    };

    HT.changeCartItemSubTotal = (item, res) => {
        item.parents(".cart-item-info")
            .find(".cart-price-sale")
            .html(addCommas(res.response.cartItemSubTotal) + "đ");
    };

    HT.changeCartOriginalItemSubTotal = (item, res) => {
        item.parents(".cart-item-info")
            .find(".cart-price-old")
            .html(addCommas(res.response.cartItemOriginalSubTotal) + "đ");
    };

    HT.changeCartTotal = (res) => {
        $(".cart-total").html(addCommas(res.response.cartTotal) + "đ");
        $(".discount-value").html(
            "-" + addCommas(res.response.cartDiscount) + "đ"
        );
    };
    HT.changetotalDiscountPromotion = (res) => {
        $(".discount-promotion").html(
            "-" + addCommas(res.response.totalDiscountPromotion) + "đ"
        );
    };

    HT.changeCartHeader = (res) => {
        let cartHtml = "";
        let cartArray = Object.values(res.carts);
        console.log("MINICART", cartArray);
        if (cartArray.length > 0) {
            cartHtml += '<div class="cart-list">';
            cartArray.forEach((cart) => {
                cartHtml += `
                <div class="cart-item">
                    <div class="cart-item-image">
                        <span class="image">
                            <img src="${
                                cart.image
                                    ? cart.image
                                    : "frontend/resources/img/no_image.png"
                            }" alt="">
                        </span>
                        <span class="cart-item-number">${cart.qty}</span>
                    </div>
                    <div class="cart-item-info">
                        <h3 class="title">${cart.name}</h3>
                        <div class="cart-item-price">
                            <span class="cart-price-sale">${addCommas(
                                cart.price * cart.qty
                            )}đ</span>
                        </div>
                        <div class="cart-item-remove" data-row-id="${
                            cart.rowId
                        }">✕</div>
                    </div>
                </div>
            `;
            });
            cartHtml += `
            </div>
            <button class="checkout-btn" onclick="window.location.href='thanh-toan.html'">
                Thanh toán
            </button>
        `;
        } else {
            cartHtml = `
            <div class="empty-cart-message">
                <img src="frontend/resources/img/shopping-bag.png" alt="Empty Cart" />
                <p style="font-weight:bold;">Giỏ hàng trống</p>
            </div>
        `;
        }

        $(".cart-minicart-hearder").html(cartHtml);
    };

    HT.removeCartItem = () => {
        $(document).on("click", ".cart-item-remove", function () {
            let _this = $(this);
            let option = {
                rowId: _this.attr("data-row-id"),
                _token: _token,
            };
            $.ajax({
                url: "ajax/cart/delete",
                type: "POST",
                data: option,
                dataType: "json",
                beforeSend: function () {},
                success: function (res) {
                    toastr.clear();
                    if (res.code === 10) {
                        $("#cartTotalItem").html(res.countMiniCart);
                        HT.changeCartTotal(res);
                        HT.removeCartItemRow(_this);
                        toastr.success(res.messages, "Thông báo từ hệ thống!");
                    } else {
                        toastr.error(
                            "Có vấn đề xảy ra! Hãy thử lại",
                            "Thông báo từ hệ thống!"
                        );
                    }
                },
            });
        });
    };

    HT.removeCartItemRow = (_this) => {
        _this.parents(".cart-item").remove();
    };

    HT.setupSelect2 = () => {
        if ($(".setupSelect2").length) {
            $(".setupSelect2").select2();
        }
    };

    // Document ready functions
    $document.ready(function () {
        HT.addCart();
        HT.setupSelect2();
        HT.changeQuantityCart();
        HT.changeQuantityInputCart();
        HT.removeCartItem();
        HT.addWishlish();
    });
})(jQuery);
