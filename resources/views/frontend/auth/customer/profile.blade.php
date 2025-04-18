@extends('frontend.homepage.layout')
@section('content')
    <div class="container p-5">
        <div class="row">
            <div class="col-12 col-md-4 col-lg-3 mx-auto side-profile">
                @include('frontend.auth.customer.components.sidebar')
            </div>

            <div class="col-12 col-md-8 col-lg-9 mx-auto">
                @include('admin.dashboard.component.formError')
                <form action="{{ route('customer.profile.update') }}" method="post" class="px-5"
                    enctype="multipart/form-data">
                    @csrf
                    <h4 class="text-center mb-3 mt-3 profile-title">Hồ sơ của tôi</h4>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label class="mt-1" for="">Email</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" placeholder="Email" name="email" readonly
                                        style="height: 43px" value="{{ old('email', $customer->email) }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label class="mt-1" for="">Họ tên</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" placeholder="Họ Tên" name="name"
                                        style="height: 43px" value="{{ old('name', $customer->name) }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label class="mt-1" for="">Số điện thoại</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" placeholder="Số điện thoại" name="phone"
                                        style="height: 43px" value="{{ old('phone', $customer->phone) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="row mb-3">
                                <div class="col-lg-12 text-center">
                                    <label class="mt-1">Ảnh đại diện</label>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12 text-center">
                                    <img id="image_preview"
                                        src="{{ $customer->image ? asset($customer->image) : 'frontend/resources/img/no_image.png' }}"
                                        style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 1px solid #ccc; margin-right: 10px;">

                                    <input type="file" name="image" id="image_input" class="d-none" accept="image/*">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12 text-center">
                                    <button type="button" class="btn-login" style="font-size: 11px;"
                                        onclick="document.getElementById('image_input').click()">Chọn ảnh</button>
                                </div>
                            </div>

                            <script>
                                document.getElementById('image_input').addEventListener('change', function(event) {
                                    var file = event.target.files[0];

                                    if (file) {
                                        var reader = new FileReader();
                                        reader.onload = function(e) {
                                            document.getElementById('image_preview').src = e.target.result;
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                });
                            </script>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="mt-1">Thành Phố</label>
                        </div>
                        <div class="col-lg-8">
                            <select name="province_id"
                                class="form-control input-address custom-width province location setupSelect2"
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
                            <select name="district_id"
                                class="form-control input-address custom-width setupSelect2 districts location"
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
                            <select name="ward_id" class="form-control input-address scustom-width setupSelect2 wards">
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


                    <div class="d-flex justify-content-start align-items-center mt-3" style="">
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
    <script src="backend/plugins/ckeditor/ckeditor.js"></script>
    <script src="frontend/core/plugins/ckfinder/ckfinder.js"></script>
    <script>
        var province_id = '{{ auth()->guard('customer')->user()->province_id ?? old('province_id') }}';
        var district_id = '{{ auth()->guard('customer')->user()->district_id ?? old('district_id') }}';
        var ward_id = '{{ auth()->guard('customer')->user()->ward_id ?? old('ward_id') }}';
    </script>
@endsection
