@extends('frontend.homepage.layout')
@section('content')
    <div class="container p-5">
        <div class="row">
            <div class="col-12  col-lg-3 mx-auto">
                <div class="list-group">
                    <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action active"
                        aria-current="true">
                        Tài khoản của tôi
                    </a>
                    <a href="{{ route('my-order.index') }}" class="list-group-item list-group-item-action">
                        Đơn hàng đã mua
                    </a>
                    <a href="{{ route('customer.password.change') }}" class="list-group-item list-group-item-action">Đổi mật
                        khẩu</a>
                    <a href="{{ route('customer.logout') }}" class="list-group-item list-group-item-action">Đăng xuất</a>
                </div>

            </div>
            <div class="col-12 col-lg-9 mx-auto">
                @include('backend/dashboard/component/formError')
                <form action="{{ route('customer.profile.update') }}" method="post" class="px-5">
                    @csrf
                    <h4 class="text-center mb-3">Hồ sơ của tôi</h4>
                    <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1" for="">Email</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" placeholder="Email" name="email" readonly
                                style="height: 43px" value="{{ old('email', $customer->email) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1" for="">Họ tên</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" placeholder="Họ Tên" name="name"
                                style="height: 43px" value="{{ old('name', $customer->name) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1" for="">Số điện thoại</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" placeholder="Số điện thoại" name="phone"
                                style="height: 43px" value="{{ old('phone', $customer->phone) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1" for="">Số điện thoại</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" placeholder="Số điện thoại" name="phone"
                                style="height: 43px" value="{{ old('phone', $customer->phone) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1">Thành Phố</label>
                        </div>
                        <div class="col-lg-8">
                            <select name="province_id" class="form-control custom-width province location setupSelect2"
                                data-target="districts">
                                <option value="0">[Chọn Thành Phố]</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->code }}" @if (old('province_id', auth()->guard('customer')->user()->province_id) == $province->code) selected @endif>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1">Quận/Huyện</label>
                        </div>
                        <div class="col-lg-8">
                            <select name="district_id" class="form-control custom-width setupSelect2 districts location"
                                data-target="wards">
                                <option value="0">[Chọn Quận/Huyện]</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1">Phường/Xã</label>
                        </div>
                        <div class="col-lg-8">
                            <select name="ward_id" class="form-control custom-width setupSelect2 wards">
                                <option value="0">[Chọn Phường/Xã]</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1">Địa chỉ</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" name="address" style="height: 43px"
                                value="{{ old('address', $customer->address ?? '') }}" class="form-control" placeholder=""
                                autocomplete="off">
                        </div>
                    </div>

                    <div class="d-flex justify-content-start align-items-center" style="">
                        <button type="submit" class="btn-login">Lưu thông tin</button>

                    </div>

                </form>
            </div>

        </div>
    </div>
@endsection

@section('css')
    <style>
        @media (max-width: 960px) {
            .custom-width {
                width: 100% !important;
                max-width: 552px !important;
            }
        }

        @media (max-width: 767px) {
            .custom-width {
                max-width: 372px !important;
            }
        }

        @media (max-width: 575px) {
            .custom-width {
                max-width: 282px !important;
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        var province_id = '{{ auth()->guard('customer')->user()->province_id ?? old('province_id') }}';
        var district_id = '{{ auth()->guard('customer')->user()->district_id ?? old('district_id') }}';
        var ward_id = '{{ auth()->guard('customer')->user()->ward_id ?? old('ward_id') }}';
    </script>
@endsection
