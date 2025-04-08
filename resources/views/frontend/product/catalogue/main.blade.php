@extends('frontend.homepage.layout')
@section('content')
    <div class="product-catalogue page-wrapper">
        <div class="page-breadcrumb background">
            <div class="uk-container uk-container-center">
                <ul class="uk-list uk-clearfix">
                    <li><a href="/"><i class="mr5"></i>{{ __('frontend.home') }}</a></li>
                    {{-- <li><a href="{{ route('product.catalogue.main') }}" title="Sản phẩm">Sản phẩm</a></li> --}}
                </ul>
            </div>
        </div>
        <div class="uk-container uk-container-center mt20">
            <div class="panel-body">
                <div class="uk-grid uk-grid-medium">
                    <div class="uk-width-large-1-4 uk-hidden-small">
                        <div class="aside">
                            <div class="aside-category">
                                @if (!empty($categories[0]))
                                    <div class="aside-heading">DANH MỤC SẢN PHẨM</div>
                                    <div class="cate-product">
                                        <ul class="menu">
                                            @foreach ($categories[0] as $category)
                                                <li class="has-submenu">
                                                    <a href="#">{{ $category->name }} <span
                                                            class="arrow">›</span></a>
                                                    @if (!empty($categories[$category->id]))
                                                        <ul class="submenu">
                                                            @foreach ($categories[$category->id] as $subCategory)
                                                                <li class="has-submenu">
                                                                    <a href="#">{{ $subCategory->name }} <span
                                                                            class="arrow">›</span></a>
                                                                    @if (!empty($categories[$subCategory->id]))
                                                                        <ul class="submenu">
                                                                            @foreach ($categories[$subCategory->id] as $childCategory)
                                                                                <li class="has-submenu">
                                                                                    <a href="#">{{ $childCategory->name }}
                                                                                        <span class="arrow">›</span></a>
                                                                                    @if (!empty($categories[$childCategory->id]))
                                                                                        <ul class="submenu">
                                                                                            @foreach ($categories[$childCategory->id] as $subChildCategory)
                                                                                                <li><a
                                                                                                        href="#">{{ $subChildCategory->name }}</a>
                                                                                                </li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    @endif
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            <style>
                                .menu {
                                    list-style: none;
                                    padding: 0;
                                    margin: 0;
                                    position: relative;
                                }

                                .menu li {
                                    position: relative;
                                    padding: 10px;
                                    cursor: pointer;
                                    background: #fff;
                                    /* Nền trắng */
                                }

                                .menu .submenu {
                                    display: none;
                                    position: absolute;
                                    top: 0;
                                    left: 100%;
                                    background: #fff;
                                    color: #000;
                                    /* Nền trắng */
                                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                                    min-width: 200px;
                                    list-style: none;
                                    padding: 0;
                                    z-index: 9999;
                                    /* Đặt menu trên tất cả các phần tử khác */
                                }

                                .menu li:hover>.submenu {
                                    display: block;
                                }

                                .menu .submenu li {
                                    padding: 10px;
                                    white-space: nowrap;
                                    /* Ngăn nội dung bị xuống dòng */
                                }

                                .arrow {
                                    float: right;
                                    font-size: 12px;
                                }

                                /* Đảm bảo ảnh và các phần tử khác không che menu */
                                img,
                                .content {
                                    position: relative;
                                    z-index: 1;
                                    /* Các phần tử khác có z-index thấp hơn */
                                }
                            </style>
                            <script>
                                document.querySelectorAll('.menu li.has-submenu > a').forEach(item => {
                                    item.addEventListener('click', function(e) {
                                        e.preventDefault(); // Ngăn chặn link mặc định
                                        let submenu = this.nextElementSibling;
                                        if (submenu.style.display === 'block') {
                                            submenu.style.display = 'none';
                                        } else {
                                            submenu.style.display = 'block';
                                        }
                                    });
                                });
                            </script>
                            <div class="aside-category mt20">
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
                                <h1 class="heading-2"><span>SẢN PHẨM</span>
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
