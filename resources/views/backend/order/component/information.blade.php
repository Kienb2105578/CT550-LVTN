<div class="row">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Thông tin người nhận</h5>
        </div>
        <div class="ibox-content">
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">Mã đơn hàng</label>
                        <input type="text" name="code" id="codeInput" value="{{ old('code', $order->code ?? '') }}"
                            class="form-control" readonly>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        let codeInput = document.getElementById("codeInput");
                        if (codeInput.value.trim() === "") {
                            codeInput.value = Math.floor(Date.now() / 1000);
                        }
                    });
                </script>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Họ Tên <span
                                class="text-danger">(*)</span></label>
                        <input type="text" name="fullname" value="{{ old('fullname', $order->fullname ?? '') }}"
                            class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-6">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Email <span
                                class="text-danger">(*)</span></label>
                        <input type="text" name="email" value="{{ old('email', $order->email ?? '') }}"
                            class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Số điện thoại <span
                                class="text-danger">(*)</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $order->phone ?? '') }}"
                            class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-6">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Thành Phố <span
                                class="text-danger">(*)</span></label>
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
                        <label for="" class="control-label text-left">Quận/Huyện <span
                                class="text-danger">(*)</span></label>
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
                        <label for="" class="control-label text-left">Phường/Xã <span
                                class="text-danger">(*)</span></label>
                        <select name="ward_id" class="form-control setupSelect2 wards">
                            <option value="0">[Chọn Phường/Xã]</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Địa chỉ <span
                                class="text-danger">(*)</span></label>
                        <input type="text" name="address" value="{{ old('address', $order->address ?? '') }}"
                            class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Ghi chú</label>
                        <input type="text" name="description"
                            value="{{ old('description', $order->description ?? '') }}" class="form-control"
                            placeholder="" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="ibox">
        <div class="ibox-title">
            <h5>PHƯƠNG THỨC THANH TOÁN <span class="text-danger">(*)</span></h5>
        </div>
        <div class="ibox-content">
            <div class="row mb15">
                <div class="col-lg-9">
                    <div class="form-row">
                        <div class="payment-methods">
                            @foreach (__('payment.method') as $key => $val)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="payment_{{ $val['name'] }}" value="{{ $val['name'] }}"
                                        {{ old('payment_method') == $val['name'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_{{ $val['name'] }}">
                                        {{ $val['title'] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    var province_id = '{{ isset($order->province_id) ? $order->province_id : old('province_id') }}'
    var district_id = '{{ isset($order->district_id) ? $order->district_id : old('district_id') }}'
    var ward_id = '{{ isset($order->ward_id) ? $order->ward_id : old('ward_id') }}'
</script>
<style>
    .form-check {
        padding: 10px 0px;
    }

    .form-check input {
        margin-right: 15px
    }

    .form-check label {
        font-weight: 400;
    }
</style>
