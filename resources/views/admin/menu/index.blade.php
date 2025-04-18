@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
{{-- <div class="row mt20">
    <div class="col-lg-12">
        <a href="{{ route('menu.create') }}" class="btn btn-danger">Thêm mới menu</a>
    </div>
</div> --}}
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $config['seo']['index']['table'] }} </h5>
                @include('admin.dashboard.component.toolbox', ['model' => 'User'])
            </div>
            <div class="ibox-content">
                {{-- @include('admin.menu.component.filter') --}}
                @include('admin.menu.component.table')
            </div>
        </div>
    </div>
</div>
