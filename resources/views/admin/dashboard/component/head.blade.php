<base href="{{ config('app.url') }}">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard </title>
<link href="backend/css/bootstrap.min.css" rel="stylesheet">
<link href="backend/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="backend/css/animate.css" rel="stylesheet">
<link href="backend/plugins/jquery-ui.css" rel="stylesheet">
@if (isset($config['css']) && is_array($config['css']))
    @foreach ($config['css'] as $key => $val)
        {!! '<link rel="stylesheet" href="' . $val . '">' !!}
    @endforeach
@endif

<link href="backend/css/plugins/toastr/toastr.min.css" rel="stylesheet">
<link href="backend/css/style.css" rel="stylesheet">
<link href="backend/css/customize.css" rel="stylesheet">
<script src="backend/js/jquery-3.1.1.min.js"></script>
<script src="{{ asset('backend/js/plugins/icheck/icheck.min.js') }}"></script>
<script src="backend/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
<script src="backend/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script>
    var BASE_URL = '{{ config('app.url') }}'
    var SUFFIX = '{{ config('apps.general.suffix') }}'
</script>
