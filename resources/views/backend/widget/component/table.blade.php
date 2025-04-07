<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>Tên Widget</th>
                <th>Từ khóa</th>
                <th>ShortCode</th>
                <th class="text-center">Tình Trạng</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($widgets) && is_object($widgets))
                @foreach ($widgets as $widget)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $widget->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            {{ $widget->name }}
                        </td>
                        <td>
                            {{ $widget->keyword }}
                        </td>
                        <td>
                            {{ $widget->short_code ?? '-' }}
                        </td>
                        @foreach ($languages as $language)
                            @if (session('app_locale') === $language->canonical)
                                @continue
                            @endif
                            @php
                                $translated = isset($widget->description[$language->id]) ? 1 : 0;
                            @endphp
                            <td class="text-center">
                                <a class="{{ $translated == 1 ? '' : 'text-danger' }}"
                                    href="{{ route('widget.translate', ['languageId' => $language->id, 'id' => $widget->id]) }}">{{ $translated == 1 ? 'Đã dịch' : 'Chưa dịch' }}</a>
                            </td>
                        @endforeach
                        <td class="text-center js-switch-{{ $widget->id }}">
                            <input type="checkbox" value="{{ $widget->publish }}" class="js-switch status "
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $widget->publish == 2 ? 'checked' : '' }} data-modelId="{{ $widget->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('widget.edit', $widget->id) }}" class="btn btn-info btn-outline"><i
                                    class="fa fa-edit"></i></a>

                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $widgets->links('pagination::bootstrap-4') }}
</div>
