@php
    $coreScript = [
        'backend/js/plugins/toastr/toastr.min.js',
        'frontend/resources/plugins/wow/dist/wow.min.js',
        'frontend/core/plugins/jquery-nice-select-1.1.0/js/jquery.nice-select.min.js',
        'frontend/resources/function.js',
    ];
    if (isset($config['js'])) {
        foreach ($config['js'] as $key => $val) {
            array_push($coreScript, $val);
        }
    }
@endphp
@if (isset($config['externalJs']))
    @foreach ($config['externalJs'] as $item)
        <script src="{{ $item }}"></script>
    @endforeach
@endif
@foreach ($coreScript as $item)
    <script src="{{ asset($item) }}"></script>
@endforeach

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
