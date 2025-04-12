@extends('frontend.homepage.layout')

@section('content')
    <div class="cart-container">
        <div class="page-breadcrumb background">
            <div class="uk-container uk-container-center container">
                <ul class="uk-list uk-clearfix list-unstyled d-flex flex-wrap">
                    <li><a href="/"><i class="mr5"></i>Trang chủ</a></li>
                    <li><a href="http://127.0.0.1:8000/thanh-toan.html" title="Thanh toán"> Thanh toán</a></li>
                </ul>
            </div>
        </div>

        <div class="uk-container uk-container-center container">

            @if ($errors->any())
                <div class="uk-alert uk-alert-danger alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('cart.store') }}" class="uk-form form" method="post">
                @csrf
                <h2 class="heading-1"><span>Thông tin đặt hàng</span></h2>
                <div class="cart-wrapper">
                    <div class="uk-grid uk-grid-medium row g-3">
                        <div class="uk-width-large-3-5 col-lg-7">
                            <div class="panel-cart">
                                <div class="panel-head">
                                    <h2 class="cart-heading"><span>Đơn hàng</span></h2>
                                </div>
                                @include('frontend.cart.component.item')
                                @include('frontend.cart.component.summary')
                                @include('frontend.cart.component.method')
                            </div>
                        </div>

                        <div class="uk-width-large-2-5 col-lg-5">
                            <div class="panel-cart cart-left">
                                @include('frontend.cart.component.information')
                                <button type="submit" class="cart-checkout btn btn-primary w-100 mt-3" value="create"
                                    name="create">Thanh toán đơn hàng</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection

<script>
    var province_id = '{{ isset($order->province_id) ? $order->province_id : old('province_id') }}'
    var district_id = '{{ isset($order->district_id) ? $order->district_id : old('district_id') }}'
    var ward_id = '{{ isset($order->ward_id) ? $order->ward_id : old('ward_id') }}'
</script>
