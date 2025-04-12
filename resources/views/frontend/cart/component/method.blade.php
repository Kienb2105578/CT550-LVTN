<div class="panel-foot">
    <h2 class="cart-heading mb-3"><span>Phương thức thanh toán</span></h2>
    <div class="cart-method mb-4">
        <div class="row g-3">
            @foreach (__('payment.method') as $key => $val)
                <div class="col-12 col-md-6">
                    <label for="{{ $val['name'] }}"
                        class="d-flex align-items-center p-3 border rounded method-item h-100">
                        <input type="radio" name="method" value="{{ $val['name'] }}"
                            @if (old('method', '') == $val['name'] || (!old('method') && $key == 0)) checked @endif id="{{ $val['name'] }}" class="me-3 mt-1">

                        <span class="image me-3" style="width: 40px; height: 40px; display: flex; align-items: center;">
                            <img src="{{ $val['image'] }}" alt="" class="img-fluid">
                        </span>

                        <span class="title fw-medium">{{ $val['title'] }}</span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
