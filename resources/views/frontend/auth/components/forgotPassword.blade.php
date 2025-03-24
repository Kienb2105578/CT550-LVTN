@extends('frontend.homepage.layout')
@section('content')
    <div class="row">
        <div class="col-4"></div>
        <div class="col-12 col-md-8 col-lg-4 mx-auto">
            <form action="{{ route($route) }}" method="get" class="customer_login px-5 py-4 my-5">
                @csrf
                <h4 class="text-center mb-3">Quên mật khẩu</h4>
                <p>Nhập địa chỉ email của bạn và mật khẩu của bạn sẽ được đặt lại và gửi qua email cho bạn.</p>
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

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
