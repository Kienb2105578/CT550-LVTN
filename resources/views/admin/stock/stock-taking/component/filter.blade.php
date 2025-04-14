<form action="{{ route('stock.stock-taking.index') }}">
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
                $type = [
                    '0' => 'Loại hoạt động',
                    'import' => 'Nhập hàng',
                    'export' => 'Xuất hàng',
                    'return' => 'Trả hàng',
                ];

                $selectedType = request('type', '0');
            @endphp

            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <div class="mr10">
                        <select name="type" class="form-control setupSelect2 ml10">
                            <option value="0" {{ $selectedType == '0' ? 'selected' : '' }}>
                                {{ $type['0'] }}
                            </option>
                            <option value="import" {{ $selectedType == 'import' ? 'selected' : '' }}>
                                {{ $type['import'] }}
                            </option>
                            <option value="export" {{ $selectedType == 'export' ? 'selected' : '' }}>
                                {{ $type['export'] }}
                            </option>
                            <option value="return" {{ $selectedType == 'return' ? 'selected' : '' }}>
                                {{ $type['return'] }}
                            </option>
                        </select>
                    </div>
                    @include('admin.dashboard.component.keyword')
                    {{-- <a href="{{ route('stock.stock-taking.create') }}" class="btn btn-danger"><i
                            class="fa fa-plus mr5"></i>Thêm phiếu xuất kho</a> --}}
                </div>
            </div>

        </div>
    </div>
</form>
