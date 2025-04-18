@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('admin.dashboard.component.formError')
@php
    $url =
        $config['method'] == 'create'
            ? route('post.catalogue.store')
            : route('post.catalogue.update', $postCatalogue->id);
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
                        @include('admin.dashboard.component.content', [
                            'model' => $postCatalogue ?? null,
                        ])
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('messages.seo') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">
                                        <span>{{ __('messages.canonical') }} (không bao gồm đuôi .html) <span
                                                class="text-danger">*</span></span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" name="canonical"
                                            value="{{ old('canonical', $postCatalogue->canonical ?? '') }}"
                                            class="form-control seo-canonical" placeholder="" autocomplete="off"
                                            {{ isset($disabled) ? 'disabled' : '' }}>
                                        <span class="baseUrl">{{ config('app.url') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                @include('admin.post.catalogue.component.aside')
            </div>
        </div>
        @include('admin.dashboard.component.button')
    </div>
</form>
