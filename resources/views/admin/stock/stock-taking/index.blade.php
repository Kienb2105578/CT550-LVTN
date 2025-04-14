@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo']['stockTaking']['title']])
<div class="row mt20">
    <div class="col-lg-12">
        <a href="{{ route('stock.stock-taking.create') }}" class="btn btn-danger"></i>Thực hiện kiểm kê kho</a>
        <a href="{{ route('stock.stock-taking.create') }}" class="btn btn-warning mr10"></i>Danh sách kiểm kê kho</a>
    </div>
</div>
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <h5>{{ $config['seo']['stockTaking']['table'] }} </h5>
                </div>
            </div>
            <div class="ibox-content">
                @include('admin.stock.stock-taking.component.filter')
                @include('admin.stock.stock-taking.component.table')
            </div>
        </div>
    </div>
</div>
