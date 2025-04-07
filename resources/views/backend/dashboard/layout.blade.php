<!DOCTYPE html>
<html>

<head>
    @include('backend.dashboard.component.head')

</head>

<body>
    <div id="wrapper">
        @include('backend.dashboard.component.sidebar')

        <div id="page-wrapper" class="gray-bg" style=" background-color: #f3fbfc !important;">
            @include('backend.dashboard.component.nav')
            @include($template)
            @include('backend.dashboard.component.footer')
        </div>
    </div>
    @include('backend.dashboard.component.script')
</body>

</html>
