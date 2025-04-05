@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['stockTaking']['create']])
@include('backend.dashboard.component.formError')
@php
    $url =
        $config['method'] == 'create'
            ? route('stock.stock-taking.store')
            : route('stock.stock-taking.update', $stockTaking->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            123
        </div>

        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>
