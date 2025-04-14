@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('admin.dashboard.component.formError')
@php
    $url = $config['method'] == 'create' ? route('order.store') : route('order.update', [$order->id, $queryUrl ?? '']);
@endphp
<form id="order-form" action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-6">
                @include('admin.order.component.product')
            </div>
            <div class="col-lg-6">
                @include('admin.order.component.information')
            </div>
        </div>
        @include('admin.dashboard.component.button')
    </div>
</form>
