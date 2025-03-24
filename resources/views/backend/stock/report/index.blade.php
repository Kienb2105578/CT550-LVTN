@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['report']['title']])
@include('backend.stock.report.component.filter')
@include('backend.stock.report.component.chart')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <h5>{{ $config['seo']['report']['table'] }} </h5>
                        {{-- @include('backend.dashboard.component.toolbox', ['model' => $config['model']]) --}}
                    </div>
                </div>
                <div class="ibox-content">
                    @include('backend.stock.report.component.table')
                </div>
            </div>
        </div>
    </div>
</div>
