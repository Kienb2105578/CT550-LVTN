@extends('frontend.homepage.layout')

@section('content')
    <div id="homepage" class="homepage move-up">
        <div class="panel-main-slide">
            <div class="row g-4">
                <div class="col-lg-12">
                    @include('frontend.component.slide')
                </div>
            </div>
        </div>

        @if (isset($product_promotion) && isset($promotion_new))
            <div class="panel-flash-sale">
                <div class="container">
                    <div class="panel-head style-sale ">
                        <div class="heading-sale">
                            <span>Flash sale:</span>
                            <span id="countdown" data-end-date="{{ optional($promotion_new)->endDate }}"></span>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    var countdownElement = document.getElementById("countdown");
                                    var endDate = countdownElement.getAttribute("data-end-date");

                                    if (!endDate) {
                                        countdownElement.innerHTML = "Không có thời gian khuyến mãi!";
                                        return;
                                    }

                                    var countDownDate = new Date(endDate).getTime();

                                    var x = setInterval(function() {
                                        var now = new Date().getTime();
                                        var distance = countDownDate - now;

                                        if (distance < 0) {
                                            clearInterval(x);
                                            countdownElement.innerHTML = "Hết thời gian!";
                                            return;
                                        }

                                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                        countdownElement.innerHTML = `${days} ngày ${hours} giờ ${minutes} phút ${seconds} giây`;
                                    }, 1000);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row g-4">
                            @foreach ($product_promotion as $key => $product)
                                @if ($product->publish == 2 && $product->quantity > 0)
                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2-4 mb-3">
                                        @include('frontend.component.product-item', [
                                            'product' => $product,
                                        ])
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($product_recommend))
            <div class="panel-flash-sale">
                <div class="container">
                    <div class="panel-head style-head">
                        <h2 class="heading"><span id="style-title">GỢI Ý HÔM NAY</span></h2>
                    </div>
                    <div class="panel-body">
                        <div class="row g-4">
                            @foreach ($product_recommend as $key => $product)
                                @if ($product->publish == 2 && $product->quantity > 0)
                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2-4 mb-3">
                                        @include('frontend.component.product-item', [
                                            'product' => $product,
                                        ])
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @php
            $bannerSlides = $slides['banner']['item'] ?? [];
        @endphp

        @if (!empty($bannerSlides) && is_array($bannerSlides))
            <div class="container">
                <div class="panel-general move-up">
                    <div class="swiper-container mySwiper"
                        data-setting="{{ json_encode($slides['banner']['setting'] ?? []) }}">
                        <div class="swiper-wrapper">
                            @foreach ($bannerSlides as $val)
                                <div class="swiper-slide slide-banner-image">
                                    <img src="{{ $val['image'] ?? '' }}" alt="{{ $val['description'] ?? 'Banner' }}"
                                        class="d-block banner-img" style="height: 400px; width: 100%; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        @endif

        <script>
            var swiper = new Swiper(".mySwiper", {
                loop: true,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
            });
        </script>

        @if (isset($product_new))
            <div class="panel-flash-sale">
                <div class="container">
                    <div class="panel-head style-head">
                        <h2 class="heading"><span id="style-title">SẢN PHẨM MỚI NHẤT</span></h2>
                    </div>
                    <div class="panel-body">
                        <div class="row g-4">
                            @foreach ($product_new as $key => $product)
                                @if ($product->publish == 2 && $product->total_quantity != 0)
                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2-4 mb-3">
                                        @include('frontend.component.product-item', [
                                            'product' => $product,
                                        ])
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <style>
        @media (min-width: 992px) {
            .col-lg-2-4 {
                flex: 0 0 auto;
                width: 20%;
            }
        }
    </style>
@endsection
