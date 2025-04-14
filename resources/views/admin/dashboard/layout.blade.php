<!DOCTYPE html>
<html>

<head>
    @include('admin.dashboard.component.head')

</head>

<body>
    <div id="wrapper">
        @include('admin.dashboard.component.sidebar')

        <div id="page-wrapper" class="gray-bg" style=" background-color: #f3fbfc !important;">
            @include('admin.dashboard.component.nav')
            @include($template)
            @include('admin.dashboard.component.footer')
        </div>
    </div>
    @include('admin.dashboard.component.script')
</body>

</html>
