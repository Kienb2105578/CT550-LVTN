@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['inventory']['title']])
@include('backend.stock.inventory.component.chart')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <h5>{{ $config['seo']['inventory']['table'] }} </h5>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('backend.stock.inventory.component.filter')
                    @include('backend.stock.inventory.component.table')
                </div>
            </div>
        </div>
    </div>
</div>
