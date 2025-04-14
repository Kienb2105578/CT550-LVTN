<form action="{{ route('post.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('admin.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('admin.dashboard.component.filterPublish')
                    @php
                        $postCatalogueId = request('post_catalogue_id') ?: old('post_catalogue_id');
                    @endphp
                    <select name="post_catalogue_id" class="form-control setupSelect2 ml10">
                        @foreach ($dropdown as $key => $val)
                            <option {{ $postCatalogueId == $key ? 'selected' : '' }} value="{{ $key }}">
                                {{ $val }}</option>
                        @endforeach
                    </select>
                    @include('admin.dashboard.component.keyword')
                </div>
            </div>
        </div>
    </div>
</form>
