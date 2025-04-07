@php
    $query = base64_encode(http_build_query(request()->query()));
    $queryUrl = rtrim($query, '=');
@endphp
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th style="width:50px;">
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>{{ __('messages.tableName') }}</th>
                <th class="text-center" style="width:200px;">{{ __('messages.tableAction') }} </th>
            </tr>
        </thead>
        <tbody>
            @if (isset($attributeCatalogues) && is_object($attributeCatalogues))
                @foreach ($attributeCatalogues as $attributeCatalogue)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $attributeCatalogue->id }}"
                                class="input-checkbox checkBoxItem">
                        </td>

                        <td>
                            {{ str_repeat('|----', $attributeCatalogue->level > 0 ? $attributeCatalogue->level - 1 : 0) . $attributeCatalogue->name }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('attribute.catalogue.edit', [$attributeCatalogue->id, $queryUrl ?? '']) }}"
                                class="btn btn-info btn-outline"><i class="fa fa-edit"></i></a>

                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $attributeCatalogues->links('pagination::bootstrap-4') }}
</div>
