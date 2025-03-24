@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12 col-md-9 col-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title uk-flex uk-flex-middle uk-flex-space-between">
                    <h5>{{ $config['seo']['index']['table'] }} </h5>
                    @include('backend.dashboard.component.toolbox', ['model' => $config['model']])
                </div>
                <div class="ibox-content">
                    @include('backend.order.component.filter')
                    @include('backend.order.component.table')
                </div>
            </div>
        </div>
    </div>
</div>
