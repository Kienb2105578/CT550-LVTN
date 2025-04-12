@extends('frontend.homepage.layout')
@section('content')
    <div class="row">
        <div class="col-4"></div>
        <div class="col-12 col-md-8 col-lg-4 mx-auto">
            <form action="{{ route('fe.auth.dologin') }}" class="customer_login px-5 py-4 my-5">
                @csrf
                <h4 class="text-center mb-3">Đăng nhập</h4>
                <div class="mb-3">
                    <input type="text" name="email" value="" placeholder="Email đăng nhập" class="form-control">
                </div>
                <div class="mb-3">
                    <input type="password" name="password" value="" placeholder="Mật khẩu" class="form-control">
                    @if ($errors->has('email'))
                        <span class="text-danger">* {{ $errors->first('email') }}</span>
                    @endif
                </div>

                <div class="d-flex justify-content-start align-items-center" style="">
                    <button type="submit" class="btn-login">Đăng nhập</button>
                    <a class="d-block" style="margin-left: 130px" href="{{ route('forgot.customer.password') }}">Quên mật
                        khẩu</a>
                </div>


                <div class="text-center mt-4">
                    <span>Bạn chưa có tài khoản</span>
                    <a href="{{ route('customer.register') }}">Đăng ký ngay</a>
                </div>

                <div>

                </div>
            </form>
        </div>
        <div class="col-4"></div>
    </div>
@endsection
