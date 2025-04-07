@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
<div class="row mt20">
    <div class="col-lg-12">
        <a href="{{ route('user.catalogue.create') }}" class="btn btn-danger">Thêm mới nhóm nhân
            viên</a>
        <a href="{{ route('user.catalogue.permission') }}" class="btn btn-warning mr10">Phân
            Quyền</a>
    </div>
</div>
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $config['seo']['index']['table'] }} </h5>
                @include('backend.dashboard.component.toolbox', ['model' => 'UserCatalogue'])
            </div>
            <div class="ibox-content">
                @include('backend.user.catalogue.component.filter')
                @include('backend.user.catalogue.component.table')
            </div>
        </div>
    </div>
</div>
