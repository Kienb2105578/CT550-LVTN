@php
    $attributeQueryString = explode(',', request()->get('attribute_id'));
@endphp

@if (!is_null($attributeCatalogue))
    @foreach ($attributeCatalogue as $key => $val)
        <div class="attribute mb-3">
            <div class="attribute-item">
                <div class="label fw-bold mb-2">
                    {{ $val->name }}: <span></span>
                </div>
                @if (!is_null($val->attributes))
                    <div class="attribute-value d-flex flex-wrap gap-2">
                        @foreach ($val->attributes as $keyAttr => $attr)
                            @php
                                $isActive =
                                    (is_array($attributeQueryString) && in_array($attr->id, $attributeQueryString)) ||
                                    ($keyAttr == 0 && empty($attributeQueryString[0]));
                            @endphp
                            <a href="javascript:void(0)"
                                class="choose-attribute {{ $val->name == 'Màu sắc' ? 'color-item' : 'btn btn-outline-secondary btn-sm' }} {{ $isActive ? 'active' : '' }}"
                                data-attributeid="{{ $attr->id }}" title="{{ $attr->name }}"
                                style="{{ $val->name == 'Màu sắc' ? 'padding: 0.25rem; border: 1px solid #ccc; border-radius: 4px;' : '' }}">
                                @if ($val->name == 'Màu sắc')
                                    <img src="{{ $attr->image }}" alt="{{ $attr->name }}"
                                        style="width: 24px; height: 24px; object-fit: cover;">
                                @else
                                    {{ $attr->name }}
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@endif

<input type="hidden" name="product_id" value="{{ $product->id }}">
<input type="hidden" name="language_id" value="{{ $config['language'] }}">
<input type="hidden" name="product_gallery" value="{{ $product->album }}">
