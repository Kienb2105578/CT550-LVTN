@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
<div class="row mt20">
    <div class="col-lg-12">
        <a href="{{ route('user.create') }}" class="btn btn-danger">Thêm mới nhân viên</a>
    </div>
</div>
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $config['seo']['index']['table'] }} </h5>
                @include('admin.dashboard.component.toolbox', ['model' => 'User'])
            </div>
            <div class="ibox-content">
                @include('admin.user.user.component.filter')
                @include('admin.user.user.component.table')
            </div>
        </div>
    </div>
</div>
