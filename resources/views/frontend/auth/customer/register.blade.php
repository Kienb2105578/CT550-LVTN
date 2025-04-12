@extends('frontend.homepage.layout')
@section('content')
    <div class="row">
        <div class="col-4"></div>
        <div class="col-12 col-md-8 col-lg-4 mx-auto">
            <form action="{{ route('customer.reg') }}" method="post" class="customer_login px-5 py-4 my-5">
                @csrf
                <h4 class="text-center mb-3">Đăng ký</h4>
                <div class="mb-3">
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Họ tên"
                        class="form-control">
                    @if ($errors->has('name'))
                        <span class="text-danger">* {{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="mb-3">
                    <input type="text" name="email" value="{{ old('email') }}" placeholder="Email"
                        class="form-control">
                    @if ($errors->has('email'))
                        <span class="text-danger">* {{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="mb-3">
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Số điện thoại"
                        class="form-control">
                </div>
                <div class="mb-3">
                    <input type="password" name="password" placeholder="Mật khẩu" autocomplete="off" class="form-control">
                    @if ($errors->has('password'))
                        <span class="text-danger">* {{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" name="re_password" placeholder="Nhập lại mật khẩu"
                        autocomplete="off">
                    @if ($errors->has('re_password'))
                        <span class="text-danger">* {{ $errors->first('re_password') }}</span>
                    @endif
                </div>
                <div class="mb-3 text-center" style="margin-top: 20px;">
                    <button type="submit" class="btn-login">Đăng ký</button>
                </div>
            </form>
        </div>
        <div class="col-4"></div>
    </div>
    <style>
        .form-control {
            font-family: 'Mulish', sans-serif;
            line-height: 1.6;
        }
    </style>
@endsection
