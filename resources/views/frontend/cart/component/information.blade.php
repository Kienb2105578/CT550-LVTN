<div class="panel-head">
    <div class="d-flex align-items-center justify-content-between">
        <h2 class="cart-heading">
            <span>Thông tin giao hàng</span>
        </h2>
    </div>
</div>

<div class="panel-body mb-4">
    @php
        $user = auth()->guard('customer')->user();
    @endphp

    <div class="cart-information">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" name="fullname" value="{{ old('fullname', $user->name ?? '') }}"
                        placeholder="Nhập vào Họ Tên" class="form-control input-text">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                        placeholder="Nhập vào Số điện thoại" class="form-control input-text">
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <input type="text" name="email" value="{{ old('email', $user->email ?? '') }}"
                placeholder="Nhập vào Email" class="form-control input-text">
        </div>

        <div class="form-group mb-3">
            <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}"
                placeholder="Nhập vào địa chỉ ... " class="form-control input-text">
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <select name="province_id" class="form-control province location setupSelect2" data-target="districts">
                    <option value="0">[Chọn Thành Phố]</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->code }}" @if (old('province_id', $user->province_id ?? '') == $province->code) selected @endif>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="district_id" class="form-control setupSelect2 districts location" data-target="wards">
                    <option value="0">[Chọn Quận/Huyện]</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="ward_id" class="form-control setupSelect2 wards">
                    <option value="0">[Chọn Phường/Xã]</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <input type="text" name="description" value="{{ old('description') }}" placeholder="Ghi chú"
                class="form-control input-text">
        </div>
    </div>
</div>

<script>
    var province_id = '{{ auth()->guard('customer')->user()->province_id ?? old('province_id') }}';
    var district_id = '{{ auth()->guard('customer')->user()->district_id ?? old('district_id') }}';
    var ward_id = '{{ auth()->guard('customer')->user()->ward_id ?? old('ward_id') }}';
</script>
