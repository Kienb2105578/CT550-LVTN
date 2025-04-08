@extends('frontend.homepage.layout')
@section('content')
    <div class="product-container">
        <div class="page-breadcrumb background">
            <div class="uk-container uk-container-center">
                <ul class="uk-list uk-clearfix">
                    <li><a href="/"><i class="mr5"></i>{{ __('frontend.home') }}</a></li>
                    <li><a href="{{ write_url($productCatalogue->canonical) }}"
                            title="{{ $productCatalogue->name }}">{{ $productCatalogue->name }}</a></li>
                </ul>
            </div>
        </div>
        <div class="uk-container uk-container-center mt30">
            <div class="panel-body">
                @include('frontend.product.product.component.detail', [
                    'product' => $product,
                    'productCatalogue' => $productCatalogue,
                ])
            </div>
        </div>
    </div>
    <div id="qrcode" class="uk-modal">
        <div class="uk-modal-dialog">
            <a class="uk-modal-close uk-close"></a>
            <div class="qrcode-container">
                {!! $product->qrcode !!}
            </div>
        </div>
    </div>
@endsection
