<div class="" style="margin: 10px;">
    <div class="filter-overlay">
        <div class="filter-content-container">
            <div class="filter-item filter-price slider-box">
                <div class="filter-heading" for="priceRange">Lọc Theo Giá:</div>
                <div class="filter-price-content">
                    <input type="text" id="priceRange" readonly class="d-none">
                    <div id="price-range" class="slider">
                        <div class="slider-range" style="left: 0%; width: 100%;"></div>
                        <span class="slider-handle" tabindex="0" style="left: 0%;"></span>
                        <span class="slider-handle" tabindex="0" style="left: 100%;"></span>
                    </div>
                </div>
                <div class="filter-input-value mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <input type="text" class="min-value input-value" value="0đ">
                        <input type="text" class="max-value input-value" value="20.000.000đ">
                    </div>
                </div>
            </div>

            <div class="filter-review mb-3">
                <div class="filter-heading">Lọc theo đánh giá</div>
                <div class="filter-choose d-flex align-items-center">
                    <input id="input-rate-5" type="checkbox" name="rate[]" value="5"
                        class="form-check-input filtering">
                    <label for="input-rate-5" class="d-flex align-items-center">
                        <div class="filter-star">
                            <i class="fi fi-rs-star"></i>
                            <i class="fi fi-rs-star"></i>
                            <i class="fi fi-rs-star"></i>
                            <i class="fi fi-rs-star"></i>
                            <i class="fi fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ms-2 mb-1">(5)</span>
                </div>
                <div class="filter-choose d-flex align-items-center">
                    <input id="input-rate-4" type="checkbox" name="rate[]" value="4"
                        class="form-check-input filtering">
                    <label for="input-rate-4" class="d-flex align-items-center">
                        <div class="filter-star">
                            <i class="fi fi-rs-star"></i>
                            <i class="fi fi-rs-star"></i>
                            <i class="fi fi-rs-star"></i>
                            <i class="fi fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ms-2 mb-1">(4)</span>
                </div>
                <div class="filter-choose d-flex align-items-center">
                    <input id="input-rate-3" type="checkbox" name="rate[]" value="3"
                        class="form-check-input filtering">
                    <label for="input-rate-3" class="d-flex align-items-center">
                        <div class="filter-star">
                            <i class="fi fi-rs-star"></i>
                            <i class="fi fi-rs-star"></i>
                            <i class="fi fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ms-2 mb-1">(3)</span>
                </div>
                <div class="filter-choose d-flex align-items-center">
                    <input id="input-rate-2" type="checkbox" name="rate[]" value="2"
                        class="form-check-input filtering">
                    <label for="input-rate-2" class="d-flex align-items-center">
                        <div class="filter-star">
                            <i class="fi fi-rs-star"></i>
                            <i class="fi fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ms-2 mb-1">(2)</span>
                </div>
                <div class="filter-choose d-flex align-items-center">
                    <input id="input-rate-1" type="checkbox" name="rate[]" value="1"
                        class="form-check-input filtering">
                    <label for="input-rate-1" class="d-flex align-items-center">
                        <div class="filter-star">
                            <i class="fi fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ms-2 mb-1">(1)</span>
                </div>
            </div>
            @if (!is_null($filters))
                @foreach ($filters as $key => $val)
                    @php
                        $catName = $val->name;
                        if (is_null($val->attributes) || count($val->attributes) == 0) {
                            continue;
                        }
                    @endphp
                    <div class="filter-item">
                        <div class="filter-heading">Lọc theo {{ $catName }}</div>
                        @if (count($val->attributes))
                            <div class="filter-body">
                                @foreach ($val->attributes as $item)
                                    @php
                                        $attributeName = $item->name;
                                        $id = $item->id;
                                    @endphp
                                    <div class="filter-choose">
                                        <input type="checkbox" id="attribute-{{ $id }}"
                                            class="form-check-input filtering filterAttribute"
                                            value="{{ $id }}" data-group="{{ $val->id }}">
                                        <label for="attribute-{{ $id }}">{{ $attributeName }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif

            {{-- Mobile --}}
            <div class="filter-input-value-mobile mt-2">
                <div class="filter-heading" for="priceRange">Lọc Theo Giá:</div>
                <a class="input-value" data-from="0" data-to="499.999">Dưới 500.000đ</a>
                <a class="input-value" data-from="500.000" data-to="1.000.000">Từ 500-1 triệu</a>
                <a class="input-value" data-from="1.000.000" data-to="2.000.000">Từ 1-2 triệu</a>
                <a class="input-value" data-from="2.000.000" data-to="4.000.000">Từ 2-4 triệu</a>
                <a class="input-value" data-from="4.000.000" data-to="7.000.000">Từ 4-7 triệu</a>
                <a class="input-value" data-from="7.000.000" data-to="13.000.000">Từ 7-13 triệu</a>
                <a class="input-value" data-from="13.000.000" data-to="20.000.000">Từ 13-20 triệu</a>
            </div>

            @if (!is_null($filters))
                @foreach ($filters as $key => $val)
                    @php
                        $catName = $val->name;
                        if (is_null($val->attributes) || count($val->attributes) == 0) {
                            continue;
                        }
                    @endphp
                    <div class="filter-input-value-mobile">
                        <div class="filter-heading">{{ $catName }}</div>
                        @if (count($val->attributes))
                            <div class="filter-body ms-3">
                                @foreach ($val->attributes as $item)
                                    @php
                                        $attributeName = $item->name;
                                        $id = $item->id;
                                    @endphp
                                    <div class="filter-choose">
                                        <input type="checkbox" id="attribute-{{ $id }}"
                                            class="form-check-input filtering filterAttribute"
                                            value="{{ $id }}" data-group="{{ $val->id }}">
                                        <label for="attribute-{{ $id }}">{{ $attributeName }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<input type="hidden" class="product_catalogue_id" value="{{ $productCatalogue->id ?? '' }}">
<style>
    /* Increase the size of the checkbox */
    .filter-choose .form-check-input {
        width: 20px;
        height: 20px;
        margin-right: 10px;
    }
</style>
