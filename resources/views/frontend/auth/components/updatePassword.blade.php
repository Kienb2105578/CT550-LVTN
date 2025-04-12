@extends('frontend.homepage.layout')
@section('content')
    <div class="row">
        <div class="col-4"></div>
        <div class="col-12 col-md-8 col-lg-4 mx-auto">
            <form action="{{ route($route, ['email' => $email]) }}" method="post" class="customer_login px-5 py-4 my-5">
                @csrf
                <h4 class="text-center mb-3">CẬP NHẬT MẬT KHẨU</h4>

                <div class="mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Mật khẩu">
                </div>

                <div class="mb-3">
                    <input type="password" class="form-control" name="re_password" placeholder="Nhập lại mật khẩu">
                </div>

                <button type="submit" class="btn-login">Đổi mật khẩu</button>

                <div>

                </div>
            </form>
        </div>
        <div class="col-4"></div>
    </div>
@endsection
