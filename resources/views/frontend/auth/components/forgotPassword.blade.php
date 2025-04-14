@extends('frontend.homepage.layout')
@section('content')
    <div class="row">
        <div class="col-4"></div>
        <div class="col-12 col-md-8 col-lg-4 mx-auto">
            <form action="{{ route($route) }}" method="get" class="customer_login px-5 py-4 my-5">
                @csrf
                <h4 class="text-center mb-3">Quên mật khẩu</h4>
                <p>Nhập địa chỉ email của bạn? Chúng tôi sẽ liên hệ qua email cho bạn.</p>
                <div class="mb-3">
                    <input type="text" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">
                    @if ($errors->has('email'))
                        <span class="error-message">* {{ $errors->first('email') }}</span>
                    @endif
                </div>

                <button type="submit" class="btn-login">Gửi mật khẩu mới</button>

                <div>

                </div>
            </form>
        </div>
        <div class="col-4"></div>
    </div>
@endsection
