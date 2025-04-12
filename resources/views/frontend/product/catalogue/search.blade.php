@extends('frontend.homepage.layout')
@section('content')
    <div class="product-catalogue page-wrapper">
        <div class="container mt-4">

            <div class="panel-body">
                <h2 class="mb-4 h3"><span>{{ $seo['meta_title'] }}</span></h2>

                @if (!is_null($products) && count($products))
                    <div class="product-list">
                        <div class="row g-3">
                            @foreach ($products as $product)
                                @if ($product->publish == 2 && $product->total_quantity > 0)
                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2-4 mb-3">
                                        @include('frontend.component.product-item', [
                                            'product' => $product,
                                        ])
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="py-4 text-center">
                        Không có sản phẩm phù hợp....
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
