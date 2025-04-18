<form action="{{ route('user.index') }}">
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
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @php
                        $publish = request('publish') ?: old('publish');
                    @endphp
                    <select name="publish" class="form-control setupSelect2 ml10">
                        @foreach (config('apps.general.publish') as $key => $val)
                            <option {{ $publish == $key ? 'selected' : '' }} value="{{ $key }}">
                                {{ $val }}</option>
                        @endforeach
                    </select>
                    @php
                        $user_catalogue_id = request('user_catalogue_id') ?: old('user_catalogue_id');
                    @endphp
                    <select name="user_catalogue_id" class="form-control mr10 setupSelect2">
                        <option value="0" {{ $user_catalogue_id == 0 ? 'selected' : '' }}>Chọn nhóm nhân viên
                        </option>
                        @foreach ($userCatalogues as $key => $value)
                            <option value="{{ $key }}" {{ $user_catalogue_id == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>

                    <div class="uk-search uk-flex uk-flex-middle mr10">
                        <div class="input-group">
                            <input type="text" name="keyword" value="{{ request('keyword') ?: old('keyword') }}"
                                placeholder="Nhập từ khóa ..." class="form-control">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary mb0 btn-sm" style="height: 40px">Tìm Kiếm
                                </button>
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
