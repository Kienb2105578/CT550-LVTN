<form action="{{ route('stock.stock-taking.list') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="perpage">
                @php
                    $perpage = request('perpage') ?: old('perpage');
                @endphp
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <select name="perpage" class="form-control input-sm perpage filter mr10">
                        @for ($i = 20; $i <= 200; $i += 20)
                            <option {{ $perpage == $i ? 'selected' : '' }} value="{{ $i }}">
                                {{ $i }} bản ghi</option>
                        @endfor
                    </select>
                </div>
            </div>

            @php
                $selectedPublish = request('publish', '');
            @endphp

            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <div class="mr10">
                        <!-- Chọn publish -->
                        <select name="publish" class="form-control setupSelect2 ml10">
                            <option value="">-- Tất cả --</option>
                            <option value="0" {{ $selectedPublish == '0' ? 'selected' : '' }}>
                                Bản nháp
                            </option>
                            <option value="1" {{ $selectedPublish == '1' ? 'selected' : '' }}>
                                Cập nhật vào kho
                            </option>
                        </select>
                    </div>

                    @include('admin.dashboard.component.keyword')
                </div>
            </div>

        </div>
    </div>
</form>
