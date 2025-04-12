@extends('frontend.homepage.layout')
@section('content')
    <div class="product-catalogue page-wrapper">
        <div class="page-breadcrumb background">
            <div class="container">
                <ul class="list-unstyled d-flex flex-wrap gap-2">
                    <li><a href="/"><i class="mr-2"></i>{{ __('frontend.home') }}</a></li>
                    <li><a href="{{ write_url($productCatalogue->canonical) }}"
                            title="{{ $productCatalogue->name }}">{{ $productCatalogue->name }}</a></li>
                </ul>
            </div>
        </div>

        <div class="container mt-3">
            <div class="panel-body">
                <div class="row g-3">
                    <div class="col-lg-3 d-none d-lg-block">
                        <div class="aside">
                            <div class="aside-category">
                                @if (!empty($categories[0]))
                                    <div class="aside-heading">DANH MỤC SẢN PHẨM</div>
                                    <div class="cate-product">
                                        <ul class="menu">
                                            @foreach ($categories[0] as $category)
                                                <li class="has-submenu">
                                                    <a href="{{ write_url($category->canonical) }}">{{ $category->name }}
                                                        <span class="arrow">›</span></a>
                                                    @if (!empty($categories[$category->id]))
                                                        <ul class="submenu">
                                                            @foreach ($categories[$category->id] as $subCategory)
                                                                <li class="has-submenu">
                                                                    <a href="{{ write_url($subCategory->canonical) }}">{{ $subCategory->name }}
                                                                        <span class="arrow">›</span></a>
                                                                    @if (!empty($categories[$subCategory->id]))
                                                                        <ul class="submenu">
                                                                            @foreach ($categories[$subCategory->id] as $childCategory)
                                                                                <li class="has-submenu">
                                                                                    <a
                                                                                        href="{{ write_url($childCategory->canonical) }}">{{ $childCategory->name }}
                                                                                        <span class="arrow">›</span></a>
                                                                                    @if (!empty($categories[$childCategory->id]))
                                                                                        <ul class="submenu">
                                                                                            @foreach ($categories[$childCategory->id] as $subChildCategory)
                                                                                                <li>
                                                                                                    <a
                                                                                                        href="{{ write_url($subChildCategory->canonical) }}">{{ $subChildCategory->name }}</a>
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

                            <div class="aside-category mt-3">
                                <div class="aside-heading">Bộ lọc thông minh</div>
                                <div class="aside-body">
                                    <ul class="list-unstyled">
                                        @include('frontend.product.catalogue.component.filterContent')
                                    </ul>
                                </div>
                            </div>
                        </div>
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
                    <div class="col-lg-9">
                        <div class="wrapper">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h1 class="heading-2"><span>{{ $productCatalogue->name }}</span></h1>
                            </div>

                            @if (!is_null($products))
                                <div class="product-list">
                                    <div class="row g-3">
                                        @foreach ($products as $product)
                                            @if ($product['publish'] == 2 && $product->total_quantity > 0)
                                                <div class="col-6 col-md-4 col-lg-3 mb-3">
                                                    @include('frontend.component.product-item', [
                                                        'product' => $product,
                                                    ])
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    @include('frontend.component.pagination', ['model' => $products])
                                </div>
                            @endif

                            @if (!empty($productCatalogue->description))
                                <div class="product-catalogue-description mt-3">
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
