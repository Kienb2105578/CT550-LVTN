@php
    $name = $product->name;
    $canonical = write_url($product->canonical);
    $image = image($product->image);
    $price = getPrice($product);
    $catName = $productCatalogue->name;
    $review = getReview($product);
    $description = $product->description;
    $attributeCatalogue = $product->attributeCatalogue;
    $gallery = json_decode($product->album);
    $product_canonical = write_url($product->canonical);
@endphp

@php
    $totalReviews = $product->reviews()->count();
    $totalRate = number_format($product->reviews()->avg('score'), 1);
    $starPercent = $totalReviews == 0 ? '0' : ($totalRate / 5) * 100;
    $fiveStar = $product->reviews()->where('score', 5)->count();
@endphp

<div class="panel-body">
    <div class="uk-grid uk-grid-medium">
        <div class="uk-width-large-1-2">
            @if (!is_null($gallery))
                <div class="popup-gallery">
                    <div class="swiper-container">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-wrapper big-pic">
                            <?php foreach($gallery as $key => $val){  ?>
                            <div class="swiper-slide" data-swiper-autoplay="2000">
                                <a href="{{ image($val) }}" data-uk-lightbox="{group:'my-group'}"
                                    class="image img-scaledown"><img src="{{ image($val) }}"
                                        alt="<?php echo $val; ?>"></a>
                            </div>
                            <?php }  ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <div class="swiper-container-thumbs">
                        <div class="swiper-wrapper pic-list">
                            <?php foreach($gallery as $key => $val){  ?>
                            <div class="swiper-slide">
                                <span class="image img-scaledown"><img src="{{ image($val) }}"
                                        alt="<?php echo $val; ?>"></span>
                            </div>
                            <?php }  ?>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="uk-width-large-1-2">
            <div class="popup-product">
                <h1 class="title product-main-title"><span>{{ $name }}</span>
                </h1>
                <div class="rating">
                    <div class="uk-flex uk-flex-middle">
                        <div class="author">Đánh giá: </div>
                        <div class="star-rating">
                            <div class="stars" style="--star-width: {{ $starPercent }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="uk-grid uk-grid-small">
                    <div class="uk-width-large">
                        <div class="a-left">
                            <div class="price-container">
                                @if ($price['priceSale'] > 0 && $price['price'] != $price['priceSale'])
                                    {!! $price['html'] !!}
                                    <div class="price-save">
                                        Tiết kiệm:
                                        <strong>{{ convert_price($price['price'] - $price['priceSale'], true) }}</strong>
                                        (<span style="color:red">- {{ $price['percent'] }}%</span>)
                                    </div>
                                @else
                                    <div class="price uk-flex uk-flex-middle mt10">
                                        <div class="price-sale">{{ convert_price($price['price'], true) }}đ</div>
                                    </div>
                                @endif
                            </div>
                            @include('frontend.product.product.component.variant')
                            <div class="quantity mt10" style="margin-bottom: 10px">
                                <div class="uk-flex uk-flex-middle" style="margin-bottom: 10px">
                                    <div class="quantitybox uk-flex uk-flex-middle">
                                        <div class="minus quantity-button">-</div>
                                        <input type="text" name="" value="1" class="quantity-text"
                                            data-product-id="{{ $product->id }}"
                                            data-attribute-id="{{ $variant->id ?? '' }}">
                                        <div class="plus quantity-button">+</div>
                                    </div>
                                </div>
                                <div class="uk-flex uk-flex-middle">
                                    <div class="btn-group uk-flex uk-flex-middle">
                                        <div class="btn-item btn-1 addToCart" data-id="{{ $product->id }}"
                                            data-type="add" style="margin-right: 10px">
                                            <a href="" title="">Thêm vào giỏ</a>
                                        </div>
                                        <div class="btn-item btn-1 addToCart" data-id="{{ $product->id }}"
                                            data-type="buy">
                                            <a href="" title="">Mua ngay</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-group">
                                <div class="btn-item btn-1 addToCart mobile" data-id="{{ $product->id }}"
                                    data-type="add">
                                    <a href="" title="">Thêm vào giỏ</a>
                                </div>
                                <div class="btn-item btn-1 addToCart mobile" data-id="{{ $product->id }}"
                                    data-type="buy">
                                    <a href="" title="">Mua ngay</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="uk-grid uk-grid-medium">
        <div class="uk-width-large-3-4">
            <div class="product-wrapper">
                @include('frontend.product.product.component.general')
            </div>
        </div>
        <div class="uk-width-large-1-4 uk-visible-large">
            <div class="aside">
                @if (count($product_recommend))
                    <div class="aside-category aside-product mt20">
                        <div class="aside-heading">Sản phẩm gợi ý</div>
                        <div class="aside-body">
                            @foreach ($product_recommend as $product)
                                @php
                                    $name = $product->name;
                                    $canonical = write_url($product->canonical);
                                    $image = $product->image;
                                    $price = getPrice($product);
                                @endphp
                                <div class="aside-product uk-clearfix">
                                    <a href="" class="image img-cover"><img src="{{ $image }}"
                                            alt="{{ $name }}"></a>
                                    <div class="info">
                                        <h3 class="title"><a href="{{ $canonical }}"
                                                title="{{ $name }}">{{ $name }}</a></h3>
                                        {!! $price['html'] !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <div class="product-related move-up">
        <div class="panel-head style-head">
            <h2 class="heading"><span id="style-title">SẢN PHẨM CÙNG DANH MỤC</span></h2>
        </div>
        <div class="panel-body list-product">
            @if (count($productCatalogue->products))
                <div class="uk-grid uk-grid-medium">
                    @foreach ($productCatalogue->products as $index => $product)
                        @if ($index > 7)
                            @break
                        @endif
                        <div class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5 mb20">
                            @include('frontend.component.product-item', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="panel-footer">
            <h2 class="heading style-footer"><a class="btn btn-readmode"
                    href="{{ write_url($productCatalogue->canonical) }}"><span>XEM
                        THÊM</span></a></h2>
        </div>
    </div>
    {{-- @if (count($cartSeen))
        <div class="product-related">
            <div class="panel-product">
                <div class="panel-head style-head">
                    <h2 class="heading"><span id="style-title">SẢN PHẨM ĐÃ XEM</span></h2>
                </div>
                <div class="panel-body list-product">
                    <div class="uk-grid uk-grid-medium">
                        @foreach ($cartSeen as $key => $val)
                            @php
                                $price = getPrice($val);
                            @endphp
                            <div class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5 mb20">
                                <div class="product-item product move-up">
                                    <a href="{{ $val->options->canonical }}" class="image img-scaledown img-zoomin">
                                        <img src="{{ $val->options->image }}" alt="{{ $val->name }}">
                                    </a>
                                    <div class="info">
                                        <div class="category-title">
                                            <a href="{{ $val->options->canonical }}" title="{{ $val->name }}">
                                                {{ $val->options->catName }}
                                            </a>
                                        </div>
                                        <h3 class="title product-title-filter">
                                            <a href="{{ $val->options->canonical }}" title="{{ $val->name }}">
                                                {{ $val->name }}
                                            </a>
                                        </h3>
                                        <div class="rating">
                                            <div class="uk-flex uk-flex-middle">
                                                <div class="star-rating">
                                                    <div class="stars"
                                                        style="--star-width: {{ $val->options->review['star'] ?? 0 }}%">
                                                    </div>
                                                </div>
                                                <span
                                                    class="rate-number">({{ $val->options->review['count'] ?? 0 }})</span>
                                            </div>
                                        </div>
                                        <div class="product-group">
                                            {!! $price['html'] !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif --}}


</div>

<input type="hidden" class="productName" value="{{ $product->name }}">
<input type="hidden" class="attributeCatalogue" value="{{ json_encode($attributeCatalogue) }}">
<input type="hidden" class="productCanonical" value="{{ $product_canonical }}">
