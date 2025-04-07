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
                <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }} </th>
                <th class="text-center" style="width:100px;">{{ __('messages.tableAction') }} </th>
            </tr>
        </thead>
        <tbody>
            @if (isset($productCatalogues) && is_object($productCatalogues))
                @foreach ($productCatalogues as $productCatalogue)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $productCatalogue->id }}"
                                class="input-checkbox checkBoxItem">
                        </td>

                        <td>
                            {{ str_repeat('|----', $productCatalogue->level > 0 ? $productCatalogue->level - 1 : 0) . $productCatalogue->name }}
                        </td>
                        <td class="text-center">
                            <input type="checkbox" value="{{ $productCatalogue->publish }}"
                                class="js-switch status js-switch-{{ $productCatalogue->id }} " data-field="publish"
                                data-model="{{ $config['model'] }}"
                                {{ $productCatalogue->publish == 2 ? 'checked' : '' }}
                                data-modelId="{{ $productCatalogue->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('product.catalogue.edit', [$productCatalogue->id, $queryUrl]) }}"
                                class="btn btn-info btn-outline"><i class="fa fa-edit"></i></a>
                            <a href="{{ route('product.catalogue.delete', $productCatalogue->id) }}"
                                class="btn btn-danger btn-outline"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $productCatalogues->links('pagination::bootstrap-4') }}
</div>
