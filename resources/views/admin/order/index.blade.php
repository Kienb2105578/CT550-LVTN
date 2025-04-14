@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row mt20">
        <div class="col-lg-12">
            <a href="{{ route('order.create') }}" class="btn btn-danger">{{ __('messages.order.create.title') }}</a>
        </div>
    </div>
    <div class="row mt20">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title uk-flex uk-flex-middle uk-flex-space-between">
                    <h5>{{ $config['seo']['index']['table'] }} </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>

                </div>
                <div class="ibox-content">
                    @include('admin.order.component.filter')
                    @include('admin.order.component.table')
                </div>
            </div>
        </div>
    </div>
</div>
