@extends('frontend.homepage.layout')
@section('content')
    <div class="product-container">
        <div class="page-breadcrumb bg-light py-2">
            <div class="container">
                <ul class="list-unstyled d-flex flex-wrap mb-0">
                    <li>
                        <a href="/"><i class="me-1"></i>{{ __('frontend.home') }}</a>
                    </li>
                    <li>
                        <a href="{{ write_url($productCatalogue->canonical) }}" title="{{ $productCatalogue->name }}">
                            {{ $productCatalogue->name }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="container mt-4">
            <div class="panel-body">
                @include('frontend.product.product.component.detail', [
                    'product' => $product,
                    'productCatalogue' => $productCatalogue,
                ])
            </div>
        </div>
    </div>
@endsection
