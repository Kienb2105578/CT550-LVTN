@php
    $query = base64_encode(http_build_query(request()->query()));
    $queryUrl = rtrim($query, '=');
@endphp

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th style="width:50px;">
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>{{ __('messages.tableName') }}</th>
                {{-- @include('backend.dashboard.component.languageTh') --}}
                {{-- <th style="width:80px;" class="text-center">{{ __('messages.tableOrder') }}</th> --}}
                <th class="text-center" style="width:200px;">{{ __('messages.tableAction') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($attributes) && is_object($attributes))
                @foreach ($attributes as $attribute)
                    <tr id="{{ $attribute->id }}">
                        <td>
                            <input type="checkbox" value="{{ $attribute->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="main-info">
                                    <div class="name">
                                        <span class="maintitle">{{ $attribute->name }}</span>
                                    </div>
                                    <div class="catalogue">
                                        <span class="text-danger">{{ __('messages.tableGroup') }} </span>
                                        @foreach ($attribute->array_attribute_catalogue_name as $val)
                                            <a href="{{ route('attribute.index', ['attribute_catalogue_id' => $val['id']]) }}"
                                                class="catalogue-link">{{ $val['name'] }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </td>
                        @include('backend.dashboard.component.languageTd', [
                            'model' => $attribute,
                            'modeling' => 'Attribute',
                        ])
                        {{-- <td>
                            <input type="text" name="order" value="{{ $attribute->order }}"
                                class="form-control sort-order text-right" data-id="{{ $attribute->id }}"
                                data-model="{{ $config['model'] }}">
                        </td> --}}
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('attribute.edit', [$attribute->id, $queryUrl ?? '']) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fa fa-edit"></i>
                                </a>
                                {{-- <a href="{{ route('attribute.delete', $attribute->id) }}"
                                    class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </a> --}}
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<div class="pagination-wrapper">
    {{ $attributes->links('pagination::bootstrap-4') }}
</div>
