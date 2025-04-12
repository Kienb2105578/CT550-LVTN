<div class="panel-subcribe"></div>

<footer class="footer move-up" style="margin-top: 20px">
    <div class="upper">
        <div class="container">
            <div class="footer-information">
                <!-- Logo nếu muốn bật -->
                <!-- <div class="footer-logo" style="margin-top: 20px; margin-bottom: 20px;">
                    <img src="{{ $system['homepage_logo'] }}" alt="">
                </div> -->

                <div class="row g-4">
                    <div class="col-lg-3 move-up">
                        <div class="footer-contact">
                            <div class="ft-heading">GIỚI THIỆU</div>
                            <p style="text-align: justify;">
                                Hệ thống cửa hàng INCOM tự hào là một trong những đơn vị hàng đầu trong lĩnh vực
                                cung cấp nội thất cao cấp, mang đến cho khách hàng những sản phẩm chất lượng, thiết
                                kế hiện đại và phù hợp với mọi không gian sống.
                            </p>
                            <div class="ft-heading">Thông tin liên hệ</div>
                            <p>Địa chỉ: {{ $system['contact_address'] }}</p>
                            <p>Số điện thoại: {{ $system['contact_hotline'] }}</p>
                            <p>Email: {{ $system['contact_email'] }}</p>
                            <p>Website: {{ $system['contact_website'] }}</p>
                        </div>
                    </div>

                    @if (isset($menu['footer-menu']))
                        @foreach ($menu['footer-menu'] as $key => $val)
                            @php
                                $name = $val['item']->name;
                            @endphp
                            <div class="col-lg-3">
                                <div class="footer-menu">
                                    <div class="ft-heading move-up">{{ $name }}</div>
                                    @if (count($val['children']))
                                        <ul class="list-unstyled clearfix">
                                            @foreach ($val['children'] as $item)
                                                @php
                                                    $name = $item['item']->name;
                                                    $canonical = write_url($item['item']->canonical);
                                                @endphp
                                                <li><a href="{{ $canonical }}"
                                                        title="{{ $name }}">{{ $name }}</a></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="col-lg-3 move-up">
                        <div class="footer-contact">
                            <div class="ft-heading">HỖ TRỢ THANH TOÁN</div>
                            <div class="payment-methods">
                                <div><img src="frontend/resources/core/image/vnpay.jpg" alt="VNPay"></div>
                                <div><img src="frontend/resources/core/image/momo.png" alt="MoMo"></div>
                                <div><img src="frontend/resources/core/image/paypal.jpg" alt="Paypal"></div>
                            </div>
                            <div class="ft-heading mt-3">CHỨNG NHẬN</div>
                            <div class="logo-bct mt-2">
                                <!-- Logo BCT nếu cần bật -->
                                <!-- <a href="http://online.gov.vn/Home/WebDetails/75902" rel="nofollow" target="_blank">
                                    <img alt='Đăng ký Bộ Công Thương'
                                        src="https://file.hstatic.net/1000361746/file/thongbaoct_6bf20cdb8562478598625259a9c91707.png"
                                        width="150" />
                                </a> -->
                            </div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
    </div>

    <div class="copyright text-center mt-3">
        © Copyright 2025, All Rights Reserved - Design by: {{ $system['homepage_brand'] }}
    </div>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".footer-menu ul li > *").forEach(function(el) {
            let text = el.innerText.trim();
            if (text.length > 0) {
                el.innerText = text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
            }
        });
    });
</script>
