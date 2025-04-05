<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <select name="attribute_catalogue_id" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option
                                {{ $val->id == old('attribute_catalogue_id', isset($attribute->attribute_catalogue_id) ? $attribute->attribute_catalogue_id : '') ? 'selected' : '' }}
                                value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
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
                            src="{{ old('image', $attribute->image ?? '') ? old('image', $attribute->image ?? '') : asset('backend/img/not-found.jpg') }}"
                            alt=""></span>
                    <input type="hidden" name="image" value="{{ old('image', $attribute->image ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>
