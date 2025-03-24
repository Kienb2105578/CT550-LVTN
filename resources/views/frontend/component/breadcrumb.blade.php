{{-- @php
    $modelName = $model->name;
@endphp
<div class="page-breadcrumb background">
    <div class="uk-container uk-container-center">
        <ul class="uk-list uk-clearfix">
            <li><a href="/"><i class="fi-rs-home mr5"></i>{{ __('frontend.home') }}</a></li>
            @if (!is_null($breadcrumb))
                @foreach ($breadcrumb as $key => $val)
                    @php
                        $name = $val->name;
                        $canonical = write_url($val->canonical, true, true);
                    @endphp
                    <li><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></li>
                @endforeach
            @endif
        </ul>
    </div>
</div> --}}
