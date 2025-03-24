@extends('frontend.homepage.layout')
@section('content')
    <div class="container p-5">
        <div class="row">
            <div class="col-12 col-md-4 col-lg-3 mx-auto">
                <div class="list-group">
                    <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action">
                        Tài khoản của tôi
                    </a>
                    <a href="{{ route('my-order.index') }}" class="list-group-item list-group-item-action">
                        Đơn hàng đã mua
                    </a>
                    <a href="{{ route('customer.password.change') }}" class="list-group-item list-group-item-action active"
                        aria-current="true">Đổi mật khẩu</a>
                    <a href="{{ route('customer.logout') }}" class="list-group-item list-group-item-action">Đăng xuất</a>
                </div>

            </div>
            <div class="col-12 col-md-8 col-lg-9 mx-auto">
                @include('backend/dashboard/component/formError')
                <form action="{{ route('customer.password.recovery') }}" method="post" class="px-5">
                    @csrf
                    <h4 class="text-center mb-3">Thay đổi mật khẩu</h4>
                    <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1" for="">Mật khẩu cũ</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="password" class="form-control" placeholder="Nhập vào mật khẩu cũ" name="password"
                                style="height: 43px" value="">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1" for="">Mật khẩu mới</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="password" class="form-control" placeholder="Nhập vào mật khẩu mới"
                                style="height: 43px" name="new_password" value="">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-2" for=""></label>Xác nhận mật khẩu</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="password" class="form-control" placeholder="Nhập lại mật khẩu mới"
                                style="height: 43px" name="re_new_password" value="">
                        </div>
                    </div>



                    <div class="d-flex justify-content-start align-items-center" style="">
                        <button type="submit" class="btn-main">Đổi mật khẩu</button>

                    </div>

                </form>
            </div>

        </div>
    </div>
@endsection


@section('css')
    <style>
        .btn-main {
            height: 33px;
            background: #da2229;
            text-transform: uppercase;
            color: #fff;
            font-weight: 600;
            right: 5px;
            top: 6px;
            border: 12px;
            padding: 0 20px;
            border-radius: 5px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
