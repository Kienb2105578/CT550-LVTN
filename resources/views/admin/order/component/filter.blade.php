<form action="{{ route('order.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="uk-flex uk-flex-middle">
                @include('admin.dashboard.component.perpage')
                <div class="date-item-box">
                    <input type="type" name="created_at" readonly
                        value="{{ request('created_at') ?: old('created_at') }}" class="rangepicker form-control"
                        placeholder="Click chọn ngày">
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <div class="mr10">
                        @foreach (__('cart') as $key => $val)
                            @php
                                ${$key} = request($key) ?: old($key);
                            @endphp
                            <select name="{{ $key }}" class="form-control setupSelect2 ml10">
                                @foreach ($val as $index => $item)
                                    <option {{ ${$key} == $index ? 'selected' : '' }} class="mt-10"
                                        value="{{ $index }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        @endforeach
                    </div>
                    @include('admin.dashboard.component.keyword')
                </div>
            </div>
        </div>
    </div>

</form>
