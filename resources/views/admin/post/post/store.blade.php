@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $url = $config['method'] == 'create' ? route('post.store') : route('post.update', $post->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('messages.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('admin.dashboard.component.content', ['model' => $post ?? null])
                    </div>
                </div>
                @include('admin.dashboard.component.seo', ['model' => $post ?? null])
            </div>
            <div class="col-lg-3">
                @include('admin.post.post.component.aside')
            </div>
        </div>
        @include('admin.dashboard.component.button')
    </div>
</form>
