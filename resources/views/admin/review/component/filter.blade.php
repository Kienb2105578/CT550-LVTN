<form action="{{ route('review.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('admin.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('admin.dashboard.component.keyword')
                </div>
            </div>
        </div>
    </div>
</form>
