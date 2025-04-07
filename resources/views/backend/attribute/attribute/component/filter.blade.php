<form action="{{ route('attribute.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @php
                        $attributeCatalogueId = request('attribute_catalogue_id') ?: old('attribute_catalogue_id');
                    @endphp
                    <select name="attribute_catalogue_id" class="form-control setupSelect2 ml10">
                        @foreach ($dropdown as $key => $val)
                            <option {{ $attributeCatalogueId == $val->id ? 'selected' : '' }}
                                value="{{ $val->id }}">
                                {{ $val->name }}</option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.component.keyword')
                </div>
            </div>
        </div>
    </div>
</form>
