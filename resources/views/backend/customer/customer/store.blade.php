@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = $config['method'] == 'create' ? route('customer.store') : route('customer.update', $customer->id);
@endphp
<form action="{{ $url }}" method="post" class="box" enctype="multipart/form-data">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Nhập thông tin chung của người sử dụng</p>
                        <p>Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-5">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Email <span
                                            class="text-danger">(*)</span></label>
                                    <input type="text" name="email"
                                        value="{{ old('email', $customer->email ?? '') }}" class="form-control"
                                        placeholder="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mã khách hàng</label>
                                    <div class="code">
                                        <input type="text" name="code"
                                            value="{{ old('code', $customer->code ?? time()) }}" class="form-control"
                                            placeholder="" autocomplete="off" readonly>
                                        <input type="checkbox">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Họ Tên <span
                                            class="text-danger">(*)</span></label>
                                    <input type="text" name="name"
                                        value="{{ old('name', $customer->name ?? '') }}" class="form-control"
                                        placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row customerWrapper">
                                    <label for="" class="control-label text-left">Nhóm khách hàng <span
                                            class="text-danger">(*)</span></label>
                                    <select name="customer_catalogue_id" class="form-control setupSelect2">
                                        <option value="0">[Chọn Nhóm Khách Hàng]</option>
                                        @foreach ($customerCatalogues as $key => $item)
                                            <option
                                                {{ $item->id == old('customer_catalogue_id', isset($customer->customer_catalogue_id) ? $customer->customer_catalogue_id : '') ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row customerWrapper">
                                    <label for="" class="control-label text-left">Nguồn khách <span
                                            class="text-danger">(*)</span></label>
                                    <select name="source_id" class="form-control setupSelect2">
                                        <option value="0">[Chọn Nguồn khách]</option>
                                        @foreach ($sources as $key => $val)
                                            <option
                                                {{ $val->id == old('source_id', isset($customer->source_id) ? $customer->source_id : '') ? 'selected' : '' }}
                                                value="{{ $val->id }}">{{ $val->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        @if ($config['method'] == 'create')
                            <div class="row mb15">
                                <div class="col-lg-6">
                                    <div class="form-row">
                                        <label for="" class="control-label text-left">Mật khẩu <span
                                                class="text-danger">(*)</span></label>
                                        <input type="password" name="password" value="" class="form-control"
                                            placeholder="" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-row">
                                        <label for="" class="control-label text-left">Nhập lại mật khẩu <span
                                                class="text-danger">(*)</span></label>
                                        <input type="password" name="re_password" value="" class="form-control"
                                            placeholder="" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ảnh đại diện</label>
                                    <div class="d-flex align-items-center">
                                        <img id="image_preview"
                                            src="{{ old('image', $customer->image ?? 'frontend/resources/img/no_image.png') }}"
                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 1px solid #ccc; margin-right: 10px;">
                                        <input type="file" name="image" id="image_input"
                                            style="opacity: 0; position: absolute; z-index: -1;" accept="image/*">
                                        <button type="button" class="btn btn-primary"
                                            onclick="document.getElementById('image_input').click()">
                                            Chọn ảnh
                                        </button>
                                    </div>
                                    <input type="hidden" name="image_old" value="{{ $customer->image ?? '' }}">
                                </div>
                            </div>

                            <script>
                                document.getElementById('image_input').addEventListener('change', function(event) {
                                    let file = event.target.files[0];
                                    if (file) {
                                        let reader = new FileReader();
                                        reader.onload = function(e) {
                                            document.getElementById('image_preview').src = e.target.result;
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                });
                            </script>


                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ngày sinh </label>
                                    <input type="date" name="birthday"
                                        value="{{ old('birthday', isset($customer->birthday) ? date('Y-m-d', strtotime($customer->birthday)) : '') }}"
                                        class="form-control" placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin liên hệ</div>
                    <div class="panel-description">Nhập thông tin liên hệ của người sử dụng</div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Thành Phố</label>
                                    <select name="province_id" class="form-control setupSelect2 province location"
                                        data-target="districts">
                                        <option value="0">[Chọn Thành Phố]</option>
                                        @if (isset($provinces))
                                            @foreach ($provinces as $province)
                                                <option @if (old('province_id') == $province->code) selected @endif
                                                    value="{{ $province->code }}">{{ $province->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Quận/Huyện </label>
                                    <select name="district_id" class="form-control districts setupSelect2 location"
                                        data-target="wards">
                                        <option value="0">[Chọn Quận/Huyện]</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Phường/Xã </label>
                                    <select name="ward_id" class="form-control setupSelect2 wards">
                                        <option value="0">[Chọn Phường/Xã]</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Địa chỉ </label>
                                    <input type="text" name="address"
                                        value="{{ old('address', $customer->address ?? '') }}" class="form-control"
                                        placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Số điện thoại</label>
                                    <input type="text" name="phone"
                                        value="{{ old('phone', $customer->phone ?? '') }}" class="form-control"
                                        placeholder="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ghi chú</label>
                                    <input type="text" name="description"
                                        value="{{ old('description', $customer->description ?? '') }}"
                                        class="form-control" placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>

<script>
    var province_id = '{{ isset($customer->province_id) ? $customer->province_id : old('province_id') }}'
    var district_id = '{{ isset($customer->district_id) ? $customer->district_id : old('district_id') }}'
    var ward_id = '{{ isset($customer->ward_id) ? $customer->ward_id : old('ward_id') }}'
</script>
