<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ADMIN INCOM</title>

    <link href="backend/css/bootstrap.min.css" rel="stylesheet">
    <link href="backend/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="backend/css/animate.css" rel="stylesheet">
    <link href="backend/css/style.css" rel="stylesheet">
    <link href="backend/css/customize.css" rel="stylesheet">
</head>

<body class="gray-bg">

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <h1 class="logo-name" style="font-size: 110px">INCOM</h1>
            </div>
            <p>CHÀO MỪNG BẠN ĐẾN VỚI TRANG ĐĂNG NHẬP CỦA HỆ THỐNG CỦA HÀNG NỘI THẤT</p>
            <form class="m-t" method="post" role="form" action="{{ route('auth.login') }}">
                @csrf
                <div class="form-group">
                    <input type="text" name="email" class="form-control" placeholder="Email"
                        value="{{ old('email') }}">
                    @if ($errors->has('email'))
                        <span class="error-message">* {{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu">
                    @if ($errors->has('password'))
                        <span class="error-message">* {{ $errors->first('password') }}</span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">Đăng nhập</button>
            </form>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="backend/js/jquery-3.1.1.min.js"></script>
    <script src="backend/js/bootstrap.min.js"></script>

</body>

</html>
