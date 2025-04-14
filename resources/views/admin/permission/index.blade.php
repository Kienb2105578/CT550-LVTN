@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
<div class="row mt20">
    <div class="col-lg-12">
        <a href="{{ route('permission.create') }}" class="btn btn-danger">Thêm mới quyền</a>
    </div>
</div>
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $config['seo']['index']['table'] }} </h5>
            </div>
            <div class="ibox-content">
                @include('admin.permission.component.filter')
                @include('admin.permission.component.table')
            </div>
        </div>
    </div>
</div>
