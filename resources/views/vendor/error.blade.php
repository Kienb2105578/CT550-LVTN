<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    <link href="{{ asset('backend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">
</head>

<body class="gray-bg">
    <div class="middle-box text-center animated fadeInDown">
        <h1 style="color:red;">403</h1>
        <h3 class="font-bold">Bạn không có quyền truy cập</h3>

        <div class="error-desc">
            Xin lỗi, bạn không có quyền truy cập vào trang này.<br>
            Vui lòng kiểm tra lại quyền của bạn hoặc liên hệ quản trị viên để được hỗ trợ.<br>
            Bạn cũng có thể quay lại trang chủ hoặc thử lại sau.
        </div>
    </div>
    <script src="{{ asset('backend/js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
</body>

</html>
