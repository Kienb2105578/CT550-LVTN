@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url =
        $config['method'] == 'create'
            ? route('attribute.store')
            : route('attribute.update', [$attribute->id, $queryUrl ?? '']);
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
                        @if (!isset($offTitle))
                            <div class="row mb15">
                                <div class="col-lg-12">
                                    <div class="form-row">
                                        <label for=""
                                            class="control-label text-left">{{ __('messages.title') }}<span
                                                class="text-danger">(*)</span></label>
                                        <input type="text" name="name"
                                            value="{{ old('name', $attribute->name ?? '') }}"
                                            class="form-control change-title"
                                            data-flag = "{{ isset($attribute->name) ? 1 : 0 }}" placeholder=""
                                            autocomplete="off" {{ isset($disabled) ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row mb30">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for=""
                                        class="control-label text-left">{{ __('messages.description') }} </label>
                                    <textarea name="description" class="ck-editor" id="ckDescription" {{ isset($disabled) ? 'disabled' : '' }}
                                        data-height="100">{{ old('description', $attribute->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('messages.seo') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="seo-wrapper">
                            <div class="row mb15">
                                <div class="col-lg-12">
                                    <div class="form-row">
                                        <label for="" class="control-label text-left">
                                            <span>{{ __('messages.canonical') }} (không bao gồm đuôi .html) <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <div class="input-wrapper">
                                            <input type="text" name="canonical"
                                                value="{{ old('canonical', $attribute->canonical ?? '') }}"
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
            </div>
            <div class="col-lg-3">
                @include('backend.attribute.attribute.component.aside')
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>
