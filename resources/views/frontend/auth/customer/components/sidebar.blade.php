<div class="row">
    <div class="col-lg-12 text-center">
        <img id=""
            src="{{ $customer->image ? asset($customer->image) : asset('frontend/resources/img/no_image.png') }}"
            style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 1px solid #ccc; margin-right: 10px;">
        <input type="file" name="image" id="image_input" class="d-none" accept="image/*">
    </div>
</div>
<div class="row">
    <div class="col-lg-12 text-center">
        <h2 id="customer-name">{{ $customer->name ?? 'Thêm tên' }} </h2>
    </div>
</div>
<div class="list-group" style=" border: none !important;">
    <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action">
        <i class="fa icon-profile fa-user"></i> Tài khoản của tôi
    </a>
    <a href="{{ route('my-order.index') }}" class="list-group-item list-group-item-action">
        <i class="fa icon-profile fa-shopping-cart"></i> Đơn hàng đã mua
    </a>
    <a href="{{ route('customer.password.change') }}" class="list-group-item list-group-item-action">
        <i class="fa icon-profile fa-key"></i> Đổi mật khẩu
    </a>
    <a href="{{ route('customer.logout') }}" class="list-group-item list-group-item-action">
        <i class="fa icon-profile fa-sign-out"></i> Đăng xuất
    </a>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentUrl = window.location.pathname;
        let menuItems = document.querySelectorAll(".list-group-item");

        menuItems.forEach(item => {
            if (!item.href) return; // Bỏ qua nếu không có href

            try {
                let menuUrl = new URL(item.href).pathname;
                if (
                    currentUrl.startsWith(menuUrl) ||
                    (menuUrl === "/don-hang-cua-toi" && currentUrl.startsWith("/don-hang-cua-toi/")) ||
                    (menuUrl === "/my-order" && currentUrl.startsWith("/my-order/"))
                ) {
                    item.classList.add("active");
                } else {
                    item.classList.remove("active");
                }
            } catch (error) {
                console.warn("Không thể xử lý URL:", item.href, error);
            }
        });
    });
</script>
