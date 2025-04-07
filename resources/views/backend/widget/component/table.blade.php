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
                        <td>{{ $widget->name }}</td>
                        <td>{{ $widget->keyword }}</td>
                        <td>{{ $widget->short_code ?? '-' }}</td>
                        @foreach ($languages as $language)
                            @if (session('app_locale') === $language->canonical)
                                @continue
                            @endif
                            @php
                                $translated = isset($widget->description[$language->id]) ? 1 : 0;
                            @endphp
                            <td class="text-center">
                                <a class="{{ $translated == 1 ? '' : 'text-danger' }}"
                                    href="{{ route('widget.translate', ['languageId' => $language->id, 'id' => $widget->id]) }}">
                                    {{ $translated == 1 ? 'Đã dịch' : 'Chưa dịch' }}
                                </a>
                            </td>
                        @endforeach
                        <td class="text-center js-switch-{{ $widget->id }}">
                            <input type="checkbox" value="{{ $widget->publish }}" class="js-switch status"
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $widget->publish == 2 ? 'checked' : '' }} data-modelId="{{ $widget->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('widget.edit', $widget->id) }}" class="btn btn-info btn-outline">
                                <i class="fa fa-edit"></i>
                            </a>
                            <!-- Trigger Modal for Deletion -->
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteWidgetModal-{{ $widget->id }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal for Widget Deletion -->
                    <div class="modal fade" id="deleteWidgetModal-{{ $widget->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteWidgetModalLabel-{{ $widget->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('widget.destroy', $widget->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deleteWidgetModalLabel-{{ $widget->id }}">
                                            Xác nhận xóa bản ghi
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa bản ghi <strong>{{ $widget->name }}</strong>?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label for="" class="control-label text-left">Tên bản ghi</label>
                                            <input type="text" class="form-control" value="{{ $widget->name }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Hủy</button>
                                        <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $widgets->links('pagination::bootstrap-4') }}
</div>
