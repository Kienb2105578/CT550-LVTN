<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <select name="product_catalogue_id" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option
                                {{ $key == old('product_catalogue_id', isset($product->product_catalogue_id) ? $product->product_catalogue_id : '') ? 'selected' : '' }}
                                value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $catalogue = [];
            if (isset($product)) {
                foreach ($product->product_catalogues as $key => $val) {
                    $catalogue[] = $val->id;
                }
            }
        @endphp
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label">{{ __('messages.subparent') }}</label>
                    <select multiple name="catalogue[]" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option @if (is_array(old('catalogue', isset($catalogue) && count($catalogue) ? $catalogue : [])) &&
                                    isset($product->product_catalogue_id) &&
                                    $key !== $product->product_catalogue_id &&
                                    in_array($key, old('catalogue', isset($catalogue) ? $catalogue : []))) selected @endif value="{{ $key }}">
                                {{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.product.information') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{ __('messages.product.code') }}</label>
                    <input type="text" name="code" id="codeInput" value="{{ old('code', $product->code ?? '') }}"
                        class="form-control">
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
                    <label for="">{{ __('messages.product.made_in') }}</label>
                    <input type="text" name="made_in" value="{{ old('made_in', $product->made_in ?? null) }}"
                        class="form-control ">
                </div>
            </div>
        </div>

        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{ __('messages.product.price') }}</label>
                    <input type="text" name="price"
                        value="{{ old('price', isset($product) ? number_format($product->price, 0, ',', '.') : '') }}"
                        class="form-control int">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.image') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target"><img
                            src="{{ old('image', $product->image ?? '') ? old('image', $product->image ?? '') : asset('backend/img/not-found.jpg') }}"
                            alt=""></span>
                    <input type="hidden" name="image" value="{{ old('image', $product->image ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.advange') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="mb15">
                        <select name="publish" class="form-control setupSelect2" id="">
                            @foreach (__('messages.publish') as $key => $val)
                                <option
                                    {{ $key == old('publish', isset($product->publish) ? $product->publish : '2') ? 'selected' : '' }}
                                    value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


@if (!empty($product->qrcode))
    <div class="ibox w">
        <div class="ibox-title">
            <h5>Mã QRCODE</h5>
        </div>
        <div class="ibox-content qrcode">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-row">
                        {!! $product->qrcode !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
