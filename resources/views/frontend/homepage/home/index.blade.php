@extends('frontend.homepage.layout')

@section('content')
    <div id="homepage" class="homepage move-up">
        <div class="panel-main-slide">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large">
                    @include('frontend.component.slide')
                </div>
            </div>
        </div>

        @if (isset($product_promotion))
            <div class="panel-flash-sale">
                <div class="uk-container uk-container-center ">
                    <div class="panel-head style-sale ">
                        <div class="heading-sale">
                            <span id="">Flash sale:</span>
                            <span id="countdown" data-end-date="{{ $promotion_new->endDate }}"></span>

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
                        <div class="uk-grid uk-grid-medium">
                            @foreach ($product_promotion as $key => $product)
                                @if ($product->publish == 2 && $product->quantity > 0)
                                    <div
                                        class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5 mb20">
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
                <div class="uk-container uk-container-center ">
                    <div class="panel-head style-head">
                        <h2 class="heading"><span id="style-title">GỢI Ý HÔM NAY</span></h2>
                    </div>
                    <div class="panel-body">
                        <div class="uk-grid uk-grid-medium">
                            @foreach ($product_recommend as $key => $product)
                                @if ($product->publish == 2 && $product->quantity > 0)
                                    <div
                                        class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5 mb20">
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
        <div class="uk-container uk-container-center">
            <div class="panel-general move-up">
                <div class="swiper-container mySwiper">
                    <div class="swiper-wrapper">
                        @foreach ($slides['banner']['item'] as $key => $val)
                            <div class="swiper-slide slide-banner-image">
                                <img src="{{ $val['image'] }}" style="height: 400px; width: 100%; object-fit: cover;"
                                    class="d-block banner-img" alt="{{ $val['description'] }}">
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
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
                <div class="uk-container uk-container-center ">
                    <div class="panel-head style-head">
                        <h2 class="heading"><span id="style-title">SẢN PHẨM MỚI NHẤT</span></h2>
                    </div>
                    <div class="panel-body">
                        <div class="uk-grid uk-grid-medium">
                            @foreach ($product_new as $key => $product)
                                @if ($product->publish == 2 && $product->total_quantity != 0)
                                    <div
                                        class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5 mb20">
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

        <div class="panel-general page move-up">
            <div class="uk-container uk-container-center">
                @if (isset($widgets['product']->object) && count($widgets['product']->object))
                    @foreach ($widgets['product']->object as $key => $category)
                        @php
                            $catName = $category->name;
                            $catCanonical = write_url($category->canonical);
                        @endphp


                        <div class="panel-head">
                            <h2 class="heading style-head-product"><a style="color:black;" href="{{ $catCanonical }}"
                                    title="{{ $catName }}"><span id="style-title">{{ $catName }}</span></a></h2>
                        </div>

                        <div class="panel-body">
                            @if (count($category->products))
                                <div class="uk-grid uk-grid-medium">
                                    @foreach ($category->products as $index => $product)
                                        @if ($product->publish == 2 && $product->total_quantity != 0)
                                            <div
                                                class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5 mb20">
                                                @include('frontend.component.product-item', [
                                                    'product' => $product,
                                                ])
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="panel-footer">
                            <h2 class="heading style-footer"><a class="btn btn-readmode"
                                    href="{{ $catCanonical }}"><span>XEM THÊM</span></a></h2>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        @if (isset($widgets['posts']->object))
            @foreach ($widgets['posts']->object as $key => $val)
                @php
                    $catName = $val->name;
                    $catCanonical = write_url($val->canonical);
                @endphp
                <div class="panel-news move-up">
                    <div class="uk-container uk-container-center">
                        <div class="panel-head">
                            <h2 class="heading style-head-product"><span id="style-title"><?php echo $catName; ?></span></h2>
                        </div>
                        <div class="panel-body">
                            @if (count($val->posts))
                                @php
                                    $val->posts = $val->posts->unique('name');
                                @endphp
                                <div class="uk-grid uk-grid-medium move-up">
                                    @foreach ($val->posts as $post)
                                        @php
                                            $name = $post->name;
                                            $canonical = write_url($post->canonical);
                                            $createdAt = convertDateTime($post->created_at, 'd/m/Y');
                                            $description = cutnchar(strip_tags($post->description), 100);
                                            $image = $post->image;
                                        @endphp
                                        <div class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5">
                                            <div class="news-item move-up">
                                                <a href="{{ $canonical }}" class="image img-cover"><img
                                                        src="{{ $image }}" alt="{{ $name }}"></a>
                                                <div class="info">
                                                    <h3 class="title"><a href="{{ $canonical }}"
                                                            title="{{ $name }}">{{ $name }}</a></h3>
                                                    <div class="description">{!! $description !!}</div>
                                                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                        <a href="{{ $canonical }}" class="readmore">Xem thêm</a>
                                                        <span class="created_at">{{ $createdAt }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>
@endsection
