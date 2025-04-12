@php
    $name = $product->name;
    $canonical = write_url($product->canonical);
    $image = image($product->image);
    $price = getPrice($product);
    $catName = $product->product_catalogues->first()->name;
    $review = getReview($product);
@endphp
@if ($product->publish == 2 && $product->total_quantity > 0)
    <div class="product-item product move-up card h-100">
        <a href="{{ $canonical }}" class="image img-scaledown img-zoomin position-relative d-block">
            <img src="{{ $image }}" alt="{{ $name }}" class="card-img-top img-fluid">
        </a>
        <div class="info card-body p-3 d-flex flex-column justify-content-between">
            <div>
                <div class="category-title mb-1">
                    <a id="catalogue-home" href="{{ $canonical }}" title="{{ $name }}"
                        class="text-muted small text-decoration-none">
                        {{ $catName }}
                    </a>
                </div>
                <h3 class="title product-title-filter h6 mb-2">
                    <a href="{{ $canonical }}" title="{{ $name }}" class="text-dark text-decoration-none">
                        {{ $name }}
                    </a>
                </h3>
            </div>
            <div class="rating d-flex align-items-center">
                <div class="star-rating me-1">
                    <div class="stars" style="--star-width: {{ $review['star'] }}%"></div>
                </div>
                <span class="rate-number small text-muted">({{ $review['count'] }})</span>
            </div>
            <div class="product-group">
                {!! $price['html'] !!}
            </div>
        </div>
    </div>
@endif
