<div class="panel-subcribe">
</div>
<footer class="footer move-up" style="margin-top: 20px">
    <div class="upper">
        <div class="uk-container uk-container-center">
            <div class="footer-information">
                {{-- <div class="footer-logo" style="margin-top: 20px; margin-bottom: 20px;"><img
                        src="{{ $system['homepage_logo'] }}" alt=""></div> --}}
                <div class="uk-grid uk-grid-medium">
                    <div class="uk-width-large-1-4 move-up">
                        <div class="footer-contact">
                            <div class="ft-heading">GIỚI THIỆU</div>
                            <p style="text-align: justify;">
                                Hệ thống cửa hàng INCOM tự hào là một trong những đơn vị hàng đầu trong lĩnh
                                vực cung cấp nội thất cao cấp, mang đến cho khách hàng những sản phẩm chất lượng, thiết
                                kế hiện đại và phù hợp với mọi không gian sống.</p>
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
                            <div class="uk-width-large-1-4">
                                <div class="footer-menu">
                                    <div class="ft-heading move-up">{{ $name }}</div>
                                    @if (count($val['children']))
                                        <ul class="uk-list uk-clearfix">
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
                    <div class="uk-width-large-1-4 move-up">
                        <div class="footer-contact">
                            <div class="ft-heading">HỖ TRỢ THANH TOÁN</div>
                            <div class="payment-methods">
                                <div><img src="frontend/resources/core/image/vnpay.jpg" alt="VNPay"></div>
                                <div><img src="frontend/resources/core/image/momo.png" alt="MoMo"></div>
                                <div><img src="frontend/resources/core/image/paypal.jpg" alt="Paypal"></div>
                            </div>
                            <div class="ft-heading" style="margin-top: 20px;">CHỨNG NHẬN</div>
                            <div class="logo-bct" style="margin-top: 10px;">
                                <a href="http://online.gov.vn/Home/WebDetails/75902" rel="nofollow" target="_blank">
                                    <img alt='Đăng ký Bộ Công Thương'
                                        src="https://file.hstatic.net/1000361746/file/thongbaoct_6bf20cdb8562478598625259a9c91707.png"
                                        width="150" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="copyright uk-text-center">
        © Copyright 2025, All Rights Reserved - Design by: {{ $system['homepage_brand'] }}
    </div>
</footer>
<div class="bottom-support-online">
    <div class="support-content">
        <a href="tel:0905620486" class="phone-call-now" rel="nofollow">
            <i style="background:#d92329" class="fa fa-phone rotate" aria-hidden="true"></i>
            <div class="animated infinite zoomIn kenit-alo-circle" style="border-color:#d92329"></div>
            <div class="animated infinite pulse kenit-alo-circle-fill" style="background-color:#d92329"></div>
            <span style="background:#d92329">Gọi ngay: {{ $system['contact_hotline'] }}</span>
        </a>
        <a class="mes" href="mailto:{{ $system['contact_email'] }}" target="_blank">
            <i style="background:#d92329" class="fa fa-envelope"></i>
            <span style="background:#d92329">Liên hệ Email</span>
        </a>
        <a class="mes" href="https://zalo.me/{{ $system['contact_hotline'] }}" target="_blank">
            <i style="background:#d92329" class="fa fa-comments"></i>
            <span style="background:#d92329">Chat qua Zalo</span>
        </a>
    </div>
    <a class="btn-support">
        <i style="background:#d92329" class="fa fa-bell" aria-hidden="true"></i>
        <div class="animated infinite zoomIn kenit-alo-circle" style="border-color:#d92329"></div>
        <div class="animated infinite pulse kenit-alo-circle-fill" style="background-color:#d92329"></div>
    </a>
</div>

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

{{-- <div class="noti" id="noti" style="bottom:-80px;">
   
</div> --}}
