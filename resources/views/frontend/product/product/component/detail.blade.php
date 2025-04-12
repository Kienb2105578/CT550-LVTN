@php
    $name = $product->name;
    $canonical = write_url($product->canonical);
    $image = image($product->image);
    $price = getPrice($product);
    $attributeCatalogue = $product->attributeCatalogue;
    $gallery = json_decode($product->album);
    $product_canonical = write_url($product->canonical);
@endphp

@php
    $totalReviews = $product->reviews()->count();
    $totalRate = number_format($product->reviews()->avg('score'), 1);
    $starPercent = $totalReviews == 0 ? '0' : ($totalRate / 5) * 100;
@endphp


<div class="panel-body">
    <div class="row g-3">
        <div class="col-lg-6">
            @if (!is_null($gallery))
                <div class="popup-gallery">
                    <div class="swiper-container">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-wrapper big-pic">
                            @foreach ($gallery as $key => $val)
                                <div class="swiper-slide" data-swiper-autoplay="2000">
                                    <a href="{{ image($val) }}" data-bs-toggle="lightbox" class="image img-scaledown">
                                        <img src="{{ image($val) }}" alt="{{ $val }}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <div class="swiper-container-thumbs">
                        <div class="swiper-wrapper pic-list">
                            @foreach ($gallery as $key => $val)
                                <div class="swiper-slide">
                                    <span class="image img-scaledown">
                                        <img src="{{ image($val) }}" alt="{{ $val }}">
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-6">
            <div class="popup-product">
                <h1 class="title product-main-title"><span>{{ $name }}</span></h1>
                <div class="rating mb-3">
                    <div class="d-flex align-items-center">
                        <div class="author me-2">Đánh giá:</div>
                        <div class="star-rating">
                            <div class="stars" style="--star-width: {{ $starPercent }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <div class="a-left">
                            <div class="Qrcode mb-4 mt-2">
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#qrcodeModal">Xem QR Code</button>
                            </div>

                            <!-- QR Modal -->
                            <div class="modal fade" id="qrcodeModal" tabindex="-1" aria-labelledby="qrcodeLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content p-4">
                                        <div class="qrcode-container">
                                            {!! $product->qrcode !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="price-container">
                                @if ($price['priceSale'] > 0 && $price['price'] != $price['priceSale'])
                                    {!! $price['html'] !!}
                                    <div class="price-save mt-2">
                                        Tiết kiệm:
                                        <strong>{{ convert_price($price['price'] - $price['priceSale'], true) }}</strong>
                                        (<span class="text-danger">- {{ $price['percent'] }}%</span>)
                                    </div>
                                @else
                                    <div class="price d-flex align-items-center mt-2">
                                        <div class="price-sale">{{ convert_price($price['price'], true) }}đ</div>
                                    </div>
                                @endif
                            </div>

                            @include('frontend.product.product.component.variant')

                            <div class="quantity mt-3 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="quantitybox d-flex align-items-center">
                                        <div class="minus quantity-button">-</div>
                                        <input type="text" name="" value="1" class="quantity-text"
                                            data-product-id="{{ $product->id }}"
                                            data-attribute-id="{{ $variant->id ?? '' }}">
                                        <div class="plus quantity-button">+</div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <div class="btn-group d-flex align-items-center">
                                        <div class="btn-item btn-1 addToCart me-2" data-id="{{ $product->id }}"
                                            data-type="add">
                                            <a href="#">Thêm vào giỏ</a>
                                        </div>
                                        <div class="btn-item btn-1 addToCart" data-id="{{ $product->id }}"
                                            data-type="buy">
                                            <a href="#">Mua ngay</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="btn-group d-block d-lg-none">
                                <div class="btn-item btn-1 addToCart mobile mb-2" data-id="{{ $product->id }}"
                                    data-type="add">
                                    <a href="#" class=" w-100">Thêm vào giỏ</a>
                                </div>
                                <div class="btn-item btn-1 addToCart mobile" data-id="{{ $product->id }}"
                                    data-type="buy">
                                    <a href="#" class=" w-100">Mua ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-4">
        <div class="col-lg-9">
            <div class="product-wrapper">
                @include('frontend.product.product.component.general')
            </div>
        </div>
        <div class="col-lg-3 d-none d-lg-block">
            <div class="aside">
                @if (count($product_recommend))
                    <div class="aside-category aside-product mt-3">
                        <div class="aside-heading fw-bold mb-2">Sản phẩm gợi ý</div>
                        <div class="aside-body">
                            @foreach ($product_recommend as $product)
                                @php
                                    $name = $product->name;
                                    $canonical = write_url($product->canonical);
                                    $image = $product->image;
                                    $price = getPrice($product);
                                @endphp
                                <div class="aside-product clearfix mb-3 d-flex">
                                    <a href="#" class="image img-cover me-2">
                                        <img src="{{ $image }}" alt="{{ $name }}" class="img-fluid"
                                            style="width: 80px;">
                                    </a>
                                    <div class="info">
                                        <h6 class="title mb-1"><a href="{{ $canonical }}"
                                                class="text-decoration-none">{{ $name }}</a></h6>
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

    <div class="product-related mt-4">
        <div class="panel-head style-head mb-3">
            <h2 class="heading"><span id="style-title">SẢN PHẨM CÙNG DANH MỤC</span></h2>
        </div>
        <div class="panel-body list-product">
            @if (count($productCatalogue->products))
                <div class="row g-3">
                    @foreach ($productCatalogue->products as $index => $product)
                        @if ($index > 7)
                            @break
                        @endif
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2-4 mb-3">
                            @include('frontend.component.product-item', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="panel-footer mt-4">
            <h2 class="heading style-footer">
                <a class="btn btn-readmode" href="{{ write_url($productCatalogue->canonical) }}">
                    <span>XEM THÊM</span>
                </a>
            </h2>
        </div>
    </div>
</div>


<input type="hidden" class="productName" value="{{ $product->name }}">
<input type="hidden" class="attributeCatalogue" value="{{ json_encode($attributeCatalogue) }}">
<input type="hidden" class="productCanonical" value="{{ $product_canonical }}">
