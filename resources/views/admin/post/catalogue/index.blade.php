@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
<div class="row mt20">
    <div class="col-lg-12">
        <a href="{{ route('post.catalogue.create') }}"
            class="btn btn-danger">{{ __('messages.postCatalogue.create.title') }}</a>
    </div>
</div>
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $config['seo']['index']['table'] }} </h5>
                @include('admin.dashboard.component.toolbox', ['model' => 'PostCatalogue'])
            </div>
            <div class="ibox-content">
                @include('admin.post.catalogue.component.filter')
                @include('admin.post.catalogue.component.table')
            </div>
        </div>
    </div>
</div>
<!-- toastr JS (nếu chưa có) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "2000",
    };

    @if ($errors->any())
        toastr.error("{{ $errors->first() }}");
    @endif

    @if (session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if (session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
