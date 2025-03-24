@php
    $name = $product->name;
    $canonical = write_url($product->canonical);
    $image = image($product->image);
    $price = getPrice($product);
    // dd($price);
    // $catName = array_map(
    //     function ($category, $product) {
    //         return $category['id'] === $product->product_catalogue_id ? $category['languages'][0]['pivot']['name'] : '';
    //     },
    //     $product->product_catalogues->toArray(),
    //     [$product],
    // )[0];
    $catName = $product->product_catalogues->first()->name;
    $review = getReview($product);
@endphp
@if ($product->publish == 2 && $product->total_quantity > 0)
    <div class="product-item product move-up">
        <a href="{{ $canonical }}" class="image img-scaledown img-zoomin"><img src="{{ $image }}"
                alt="{{ $name }}"></a>
        <div class="info">
            <div class="category-title"><a id="catalogue-home" href="{{ $canonical }}"
                    title="{{ $name }}">{{ $catName }}</a></div>
            <h3 class="title product-title-filter"><a href="{{ $canonical }}"
                    title="{{ $name }}">{{ $name }}</a></h3>
            <div class="rating">
                <div class="uk-flex uk-flex-middle">
                    <div class="star-rating">
                        <div class="stars" style="--star-width: {{ $review['star'] }}%"></div>
                    </div>
                    <span class="rate-number">({{ $review['count'] }})</span>
                </div>
            </div>
            <div class="product-group">
                {!! $price['html'] !!}
            </div>
        </div>
    </div>
@endif
