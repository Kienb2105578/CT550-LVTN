(function ($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf-token"]').attr("content");

    HT.review = () => {
        // Nếu bạn dùng Bootstrap 5
        var modal = new bootstrap.Modal(document.getElementById("review"));
        let selectedScore = 0;

        $(document).on("click", ".rate input", function () {
            selectedScore = $(this).val();
            console.log("Đánh giá chọn: " + selectedScore);
        });

        $(document).on("click", ".btn-send-review", function () {
            let score = parseInt(selectedScore);
            let option = {
                score: score,
                description: $(".review-textarea").val(),
                gender: $(".gender:checked").val(),
                fullname: $(".product-preview input[name=fullname]").val(),
                email: $(".product-preview input[name=email]").val(),
                phone: $(".product-preview input[name=phone]").val(),
                reviewable_type: $(".reviewable_type").val(),
                reviewable_id: $(".reviewable_id").val(),
                _token: _token,
                parent_id: $(".review_parent_id").val(),
            };

            if (!score) {
                $(".rate-text")
                    .html("* Bạn chưa chọn điểm đánh giá")
                    .removeClass("d-none"); // Dùng class ẩn phù hợp với framework bạn dùng
                return false;
            }

            $(".rate-text").addClass("d-none");

            // Tạo đối tượng FormData để gửi dữ liệu bao gồm ảnh
            var formData = new FormData();
            formData.append("score", score);
            formData.append("description", $(".review-textarea").val());
            formData.append("gender", $(".gender:checked").val());
            formData.append(
                "fullname",
                $(".product-preview input[name=fullname]").val()
            );
            formData.append(
                "email",
                $(".product-preview input[name=email]").val()
            );
            formData.append(
                "phone",
                $(".product-preview input[name=phone]").val()
            );
            formData.append("reviewable_type", $(".reviewable_type").val());
            formData.append("reviewable_id", $(".reviewable_id").val());
            formData.append("_token", _token);
            formData.append("parent_id", $(".review_parent_id").val());

            // Thêm các ảnh đã chọn vào FormData
            var files = $("#review-images")[0].files;
            if (files.length > 0) {
                for (var i = 0; i < files.length; i++) {
                    formData.append("images[]", files[i]);
                }
            }

            $.ajax({
                url: "ajax/review/create",
                type: "POST",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function () {},
                success: function (res) {
                    if (res.code === 10) {
                        toastr.success(res.messages, "Thông báo từ hệ thống!");
                        modal.hide();
                        location.reload();
                    } else {
                        toastr.error(res.messages, "Thông báo từ hệ thống!");
                    }
                },
            });
        });
    };

    $(document).ready(function () {
        HT.review();
    });
})(jQuery);
