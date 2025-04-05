<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.product.information') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">Mã phiếu nhập hàng</label>
                    <input type="text" name="code" id="codeInput"
                        value="{{ old('code', $purchaseOrder->code ?? '') }}" class="form-control">
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
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>TRẠNG THÁI</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12 mt-3">
                <div class="form-row">
                    @if ($config['method'] !== 'create')
                        <select name="status" id="status" class="form-control  setupSelect2">
                            <option value="pending"
                                {{ old('status', $purchaseOrder->status ?? '') == 'pending' ? 'selected' : '' }}>Chờ
                                kiểm
                                định</option>
                            <option value="approved"
                                {{ old('status', $purchaseOrder->status ?? '') == 'approved' ? 'selected' : '' }}>Đã
                                nhập
                                kho</option>
                            <option value="returned"
                                {{ old('status', $purchaseOrder->status ?? '') == 'returned' ? 'selected' : '' }}>Đã
                                hoàn
                                trả</option>
                        </select>
                    @else
                        <input type="text" class="form-control" value="Chờ kiểm định" readonly>
                        <input type="hidden" name="status" value="pending">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>GHI CHÚ</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12 mt-3">
                <div class="form-row">
                    <input type="text" name="note" value="{{ old('note', $purchaseOrder->note ?? '') }}"
                        class="form-control" placeholder="" autocomplete="off">
                </div>
            </div>
        </div>
    </div>
</div>
