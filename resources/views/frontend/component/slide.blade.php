@php
    use App\Enums\SlideEnum;

    $slideKeyword = SlideEnum::MAIN;
    $slideItem = $slides[$slideKeyword]['item'] ?? null;
@endphp

@if (!empty($slideItem) && is_array($slideItem))
    <div class="panel-slide page-setup" data-setting="{{ json_encode($slides[$slideKeyword]['setting']) }}">
        <div class="swiper-container mainSwiper">
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-wrapper">
                @foreach ($slideItem as $item)
                    <div class="swiper-slide">
                        <div class="slide-item" style="height: 400px">
                            <span class="image img-cover">
                                <img src="{{ $item['image'] ?? '' }}" alt="{{ $item['name'] ?? 'Slide' }}">
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
@endif
