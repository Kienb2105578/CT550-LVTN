@extends('frontend.homepage.layout')
@section('content')
    <div class="product-catalogue page-wrapper">
        <div class="page-breadcrumb background">
            <div class="uk-container uk-container-center">
                <ul class="uk-list uk-clearfix">
                    <li><a href="/"><i class="fi-rs-home mr5"></i>{{ __('frontend.home') }}</a></li>
                    {{-- <li><a href="/" title="Sản phẩm">Sản phẩm</a></li> --}}
                    <li><a href="{{ write_url($productCatalogue->canonical) }}"
                            title="{{ $productCatalogue->name }}">{{ $productCatalogue->name }}</a></li>
                </ul>
            </div>
        </div>
        <div class="uk-container uk-container-center mt20">

            <div class="panel-body">
                <div class="uk-grid uk-grid-medium">
                    <div class="uk-width-large-1-4 uk-hidden-small">
                        <div class="aside">
                            <div class="aside-category">
                                <div class="aside-heading">Bộ lọc thông minh</div>
                                <div class="aside-body">
                                    <ul class="uk-list uk-clearfix">
                                        @include('frontend.product.catalogue.component.filterContent')
                                    </ul>
                                </div>
                            </div>
                            <div class="aside-category aside-product mt20">
                                <div class="aside-heading">Sản phẩm nổi bật</div>
                                <div class="aside-body">
                                    @foreach ($widgets['products-hl']->object as $product)
                                        @php
                                            $name = $product->name;
                                            $canonical = write_url($product->canonical);
                                            $image = $product->image;
                                            $price = getPrice($product);
                                        @endphp
                                        @if ($product->publish == 2 && $product->total_quantity > 0)
                                            <div class="aside-product uk-clearfix">
                                                <a href="" class="image img-cover"><img src="{{ $image }}"
                                                        alt="{{ $name }}"></a>
                                                <div class="info">
                                                    <h3 class="title"><a href="{{ $canonical }}"
                                                            title="{{ $name }}">{{ $name }}</a></h3>
                                                    {!! $price['html'] !!}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="uk-width-large-3-4">
                        <div class="wrapper ">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between mb20">
                                <h1 class="heading-2"><span>{{ $productCatalogue->name }}</span>
                                </h1>
                                @include('frontend.product.catalogue.component.filter')
                            </div>
                            {{-- @include('frontend.product.catalogue.component.filterContent') --}}
                            @if (!is_null($products))
                                <div class="product-list">
                                    <div class="uk-grid uk-grid-medium">

                                        @foreach ($products as $product)
                                            @if ($product['publish'] == 2 && $product->total_quantity > 0)
                                                <div
                                                    class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-4 mb20">
                                                    @include('frontend.component.product-item', [
                                                        'product' => $product,
                                                    ])
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div class="uk-flex uk-flex-center">
                                    @include('frontend.component.pagination', ['model' => $products])
                                </div>
                            @endif
                            @if (!empty($productCatalogue->description))
                                <div class="product-catalogue-description">
                                    {!! $productCatalogue->description !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
