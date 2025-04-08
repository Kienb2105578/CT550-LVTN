@php
    $slideKeyword = App\Enums\SlideEnum::MAIN;
@endphp
@if (count($slides[$slideKeyword]['item']))
    @php
        dd($slides);
    @endphp
    <div class="panel-slide page-setup" data-setting="{{ json_encode($slides[$slideKeyword]['setting']) }}">
        <div class="swiper-container">
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-wrapper">
                @foreach ($slides[$slideKeyword]['item'] as $key => $val)
                    <div class="swiper-slide">
                        <div class="slide-item" style="height: 400px">
                            <span class="image img-cover"><img src="{{ $val['image'] }}"
                                    alt="{{ $val['name'] }}"></span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
@endif
