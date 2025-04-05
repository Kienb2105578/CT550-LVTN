(function ($) {
    ("use strict");
    var HT = {}; // Khai báo là 1 đối tượng
    var timer;

    HT.popupSwiperSlide = () => {
        document.querySelectorAll(".popup-gallery").forEach((popup) => {
            var swiper = new Swiper(popup.querySelector(".swiper-container"), {
                loop: true,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                thumbs: {
                    swiper: {
                        el: popup.querySelector(".swiper-container-thumbs"),
                        slidesPerView: 3,
                        spaceBetween: 10,
                        slideToClickedSlide: true,
                    },
                },
            });
        });
    };

    HT.selectVariantProduct = () => {
        if ($(".choose-attribute").length) {
            $(document).on("click", ".choose-attribute", function (e) {
                e.preventDefault();
                let _this = $(this);
                let attribute_id = _this.attr("data-attributeid");
                let attribute_name = _this.text();
                _this
                    .parents(".attribute-item")
                    .find("span")
                    .html(attribute_name);
                _this
                    .parents(".attribute-value")
                    .find(".choose-attribute")
                    .removeClass("active");
                _this.addClass("active");
                HT.handleAttribute();
            });
        }
    };

    HT.selectedAttributeId = [];

    HT.handleAttribute = () => {
        let attribute_id = [];
        let flag = true;
        $(".attribute-value .choose-attribute").each(function () {
            let _this = $(this);
            if (_this.hasClass("active")) {
                attribute_id.push(_this.attr("data-attributeid"));
            }
        });

        $(".attribute").each(function () {
            if ($(this).find(".choose-attribute.active").length === 0) {
                flag = false;
                return false;
            }
        });

        HT.selectedAttributeId = attribute_id;
        console.log("Biến thể được chọn:", HT.selectedAttributeId);

        if (flag) {
            $.ajax({
                url: "ajax/product/loadVariant",
                type: "GET",
                data: {
                    attribute_id: attribute_id,
                    product_id: $("input[name=product_id]").val(),
                    language_id: $("input[name=language_id]").val(),
                },
                dataType: "json",
                beforeSend: function () {},
                success: function (res) {
                    console.log(res);
                    HT.setUpVariantPrice(res);
                    HT.setupVariantGallery(res);
                    HT.setupVariantName(res);
                    HT.setupVariantUrl(res, attribute_id);

                    let priceData = res.variantPrice;
                    if (priceData.priceSale === priceData.price) {
                        priceData.priceSale = null;
                        priceData.percent = 0;
                    }
                    $(".price-container").html(renderPrice(priceData));
                },
            });
        }
    };
    function renderPrice(priceData) {
        let html = `<div class="price uk-flex uk-flex-middle mt10">
                    <div class="price-sale">${addCommas(priceData.price)}đ</div>
                </div>`;

        if (priceData.priceSale && priceData.priceSale < priceData.price) {
            html = `<div class="price uk-flex uk-flex-middle mt10">
                    <div class="price-sale">${addCommas(
                        priceData.priceSale
                    )}đ</div>
                    <div class="price-old uk-flex uk-flex-middle">${addCommas(
                        priceData.price
                    )}đ</div>
                </div>
                <div class="price-save">
                    Tiết kiệm: <strong>${addCommas(
                        priceData.price - priceData.priceSale
                    )}đ</strong>
                    (<span style="color:red">- ${priceData.percent}%</span>)
                </div>`;
        }

        return html;
    }

    HT.setupVariantUrl = (res, attribute_id) => {
        let queryString = "?attribute_id=" + attribute_id.join(",");
        let productCanonical = $(".productCanonical").val();
        productCanonical = productCanonical + queryString;
        let stateObject = { attribute_id: attribute_id };
        history.pushState(stateObject, "Page Title", productCanonical);
    };

    HT.setUpVariantPrice = (res) => {
        $(".popup-product .price").html(res.variantPrice.html);
    };

    HT.setupVariantName = (res) => {
        let productName =
            $(".productName").val() ||
            $(".product-main-title span").text().split(" - ")[0];
        let variantName = res.variant.name;
        let productVariantName = `${productName} - ${variantName}`;

        $(".product-main-title span").html(productVariantName);
    };

    HT.setupVariantGallery = (gallery) => {
        let album = gallery.variant.album.split(",");

        if (album[0] == 0) {
            album = JSON.parse($("input[name=product_gallery]").val());
        }

        let html = `<div class="swiper-container">
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
			<div class="swiper-wrapper big-pic">`;
        album.forEach((val) => {
            html += ` <div class="swiper-slide" data-swiper-autoplay="2000">
					<a href="${val}" data-uk-lightbox="{group:'my-group'}" class="image img-scaledown"><img src="${val}" alt="${val}"></a>
				</div>`;
        });

        html += `</div>
			<div class="swiper-pagination"></div>
		</div>
		<div class="swiper-container-thumbs">
			<div class="swiper-wrapper pic-list">`;

        album.forEach((val) => {
            html += ` <div class="swiper-slide">
				<span class="image img-scaledown"><img src="${val}" alt="${val}"></span>
			</div>`;
        });

        html += `</div>
		</div>`;

        $(".popup-gallery").html(html);
        HT.popupSwiperSlide();
    };

    HT.loadProductVariant = () => {
        let attributeCatalogue = JSON.parse($(".attributeCatalogue").val());
        if (
            typeof attributeCatalogue != "undefined" &&
            attributeCatalogue.length
        ) {
            HT.handleAttribute();
        }
    };
    HT.chooseReviewStar = () => {
        $(document).on("click", ".popup-rating label", function () {
            let _this = $(this);
            let starCount = parseInt(_this.attr("for").replace("star", "")); // Lấy số sao từ label
            let title = _this.attr("title");
            $("#star" + starCount)
                .prop("checked", true)
                .trigger("change");
            $(".rate-text").removeClass("uk-hidden").html(title);
            $(".popup-rating .rate label").css("color", "#ccc");
            for (let i = 1; i <= starCount; i++) {
                $(".popup-rating label[for='star" + i + "']").css(
                    "color",
                    "gold"
                );
            }
        });
    };

    HT.changeQuantityInput = () => {
        $(document).on("change", ".quantity-text", function () {
            let _this = $(this);
            let newQuantity = parseInt(_this.val());
            let productId = _this.data("product-id"); // Lấy ID sản phẩm từ attribute
            let attributeId = HT.selectedAttributeId.join(","); // Lấy ID biến thể

            if (isNaN(newQuantity) || newQuantity < 1) {
                newQuantity = 1;
                _this.val(1);
            }

            // Gửi AJAX kiểm tra số lượng
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
                        _this.val(newQuantity);
                    } else {
                        toastr.error(response.message, "Thông báo từ hệ thống");
                        _this.val(1);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Lỗi AJAX:", xhr.responseText);
                    alert("Có lỗi xảy ra! Xem console để biết chi tiết.");
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
            let productId = quantityInput.data("product-id");

            let attributeId = HT.selectedAttributeId.join(",");

            if (newQuantity < 1) {
                newQuantity = 1;
            }

            // Gửi AJAX kiểm tra số lượng
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
                        if (response.status === "error")
                            toastr.error(
                                response.message,
                                "Thông báo từ hệ thống"
                            );
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Lỗi AJAX:", xhr.responseText);
                    toastr.error("Có lỗi xảy ra!");
                },
            });
        });
    };

    $(document).ready(function () {
        /* CORE JS */

        HT.changeQuantity();
        HT.changeQuantityInput();
        HT.popupSwiperSlide();
        HT.selectVariantProduct();
        HT.loadProductVariant();
        HT.chooseReviewStar();
    });
})(jQuery);
